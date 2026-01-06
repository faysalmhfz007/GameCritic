<?php
require_once __DIR__ . '/BaseModel.php';

class ReviewModel extends BaseModel {
    protected $table = 'reviews';

    public function getReviewsForGame(int $gameId): array {
        $stmt = $this->db->prepare("SELECT r.*, u.username FROM {$this->table} r LEFT JOIN users u ON u.id = r.user_id WHERE r.game_id = ? ORDER BY r.created_at DESC");
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function addReview(int $gameId, int $userId, float $rating, string $comment = null): bool {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (game_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iids', $gameId, $userId, $rating, $comment);
        return $stmt->execute();
    }

    public function getUserVote(int $gameId, int $userId): ?array {
        // Check if user has already voted on this game
        $stmt = $this->db->prepare("SELECT id, rating FROM {$this->table} WHERE game_id = ? AND user_id = ? AND comment IS NULL LIMIT 1");
        $stmt->bind_param('ii', $gameId, $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public function addThumb(int $gameId, int $userId, bool $isUp): bool {
        // Check if user already voted
        $existingVote = $this->getUserVote($gameId, $userId);
        
        if ($existingVote) {
            // Update existing vote
            $newValue = $isUp ? 1.0 : 0.0;
            $stmt = $this->db->prepare("UPDATE {$this->table} SET rating = ? WHERE id = ?");
            $stmt->bind_param('di', $newValue, $existingVote['id']);
            return $stmt->execute();
        } else {
            // Create new vote
            $value = $isUp ? 1.0 : 0.0;
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (game_id, user_id, rating, comment) VALUES (?, ?, ?, NULL)");
            $stmt->bind_param('iid', $gameId, $userId, $value);
            return $stmt->execute();
        }
    }

    public function getAggregates(int $gameId): array {
        // pos = count where rating >= 0.5, neg = count where rating < 0.5
        $stmt = $this->db->prepare("SELECT 
            SUM(CASE WHEN rating >= 0.5 THEN 1 ELSE 0 END) AS pos,
            SUM(CASE WHEN rating < 0.5 THEN 1 ELSE 0 END) AS neg,
            AVG(rating) AS avg_rating,
            COUNT(*) AS total
          FROM {$this->table} WHERE game_id = ?");
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $pos = (int)($res['pos'] ?? 0);
        $neg = (int)($res['neg'] ?? 0);
        $avg = (float)($res['avg_rating'] ?? 0);
        $total = (int)($res['total'] ?? 0);
        // Overall 0-10 derived from percentage positive if thumbs exist, else from avg*10 for star-like reviews
        $overall = 0.0;
        if ($total > 0) {
            if ($pos + $neg > 0) {
                $overall = round(($pos / max(1, $pos + $neg)) * 10, 1);
            } else {
                $overall = round($avg * 10, 1);
            }
        }
        return [
            'positive' => $pos,
            'negative' => $neg,
            'average' => $avg,
            'overall10' => $overall,
            'total' => $total,
        ];
    }

    public function voteComment($commentId, $userId, $voteType) {
        // Check if user already voted on this comment
        $checkQuery = "SELECT id, vote_type FROM comment_votes WHERE comment_id = ? AND user_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bind_param("ii", $commentId, $userId);
        $checkStmt->execute();
        $existingVote = $checkStmt->get_result()->fetch_assoc();
        
        if ($existingVote) {
            if ($existingVote['vote_type'] === $voteType) {
                // User is trying to vote the same way again, remove the vote
                $this->removeVote($commentId, $userId);
                return ['success' => true, 'action' => 'removed'];
            } else {
                // User is changing their vote
                $this->updateVote($commentId, $userId, $voteType);
                return ['success' => true, 'action' => 'changed'];
            }
        } else {
            // New vote
            $this->addVote($commentId, $userId, $voteType);
            return ['success' => true, 'action' => 'added'];
        }
    }

    private function addVote($commentId, $userId, $voteType) {
        $query = "INSERT INTO comment_votes (comment_id, user_id, vote_type) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $commentId, $userId, $voteType);
        $stmt->execute();
        
        // Update vote counts
        $this->updateVoteCounts($commentId);
    }

    private function updateVote($commentId, $userId, $voteType) {
        $query = "UPDATE comment_votes SET vote_type = ? WHERE comment_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sii", $voteType, $commentId, $userId);
        $stmt->execute();
        
        // Update vote counts
        $this->updateVoteCounts($commentId);
    }

    private function removeVote($commentId, $userId) {
        $query = "DELETE FROM comment_votes WHERE comment_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $commentId, $userId);
        $stmt->execute();
        
        // Update vote counts
        $this->updateVoteCounts($commentId);
    }

    private function updateVoteCounts($commentId) {
        // Get current vote counts
        $countQuery = "SELECT 
            SUM(CASE WHEN vote_type = 'up' THEN 1 ELSE 0 END) as upvotes,
            SUM(CASE WHEN vote_type = 'down' THEN 1 ELSE 0 END) as downvotes
            FROM comment_votes WHERE comment_id = ?";
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->bind_param("i", $commentId);
        $countStmt->execute();
        $counts = $countStmt->get_result()->fetch_assoc();
        
        $upvotes = $counts['upvotes'] ?? 0;
        $downvotes = $counts['downvotes'] ?? 0;
        $voteScore = $upvotes - $downvotes;
        
        // Update the review record
        $updateQuery = "UPDATE reviews SET upvotes = ?, downvotes = ?, vote_score = ? WHERE id = ?";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->bind_param("iiii", $upvotes, $downvotes, $voteScore, $commentId);
        $updateStmt->execute();
    }

    public function getUserVoteOnComment($commentId, $userId) {
        $query = "SELECT vote_type FROM comment_votes WHERE comment_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $commentId, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['vote_type'] : null;
    }

    public function getCommentsForGameSorted($gameId) {
        // Get comments sorted by vote score (highest first)
        $query = "SELECT r.*, u.username 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.game_id = ? AND r.comment IS NOT NULL AND r.comment != ''
                  ORDER BY r.vote_score DESC, r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    // Get reviews by user ID
    public function getReviewsByUserId($userId) {
        $query = "SELECT r.*, g.title as game_title, g.id as game_id
                  FROM {$this->table} r
                  JOIN games g ON r.game_id = g.id
                  WHERE r.user_id = ?
                  ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
