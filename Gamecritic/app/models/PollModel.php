<?php
require_once __DIR__ . '/BaseModel.php';

class PollModel extends BaseModel {
    protected $table = 'poll_games';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all poll games ordered by votes (descending)
     */
    public function getAllPollGames() {
        $sql = "SELECT * FROM poll_games ORDER BY votes DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get top 3 most voted games
     */
    public function getTopPollGames($limit = 3) {
        $sql = "SELECT * FROM poll_games ORDER BY votes DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get the most anticipated game (highest votes)
     */
    public function getMostAnticipatedGame() {
        $sql = "SELECT * FROM poll_games ORDER BY votes DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Add a new game to the poll
     */
    public function addGameToPoll($gameName, $gamePicture) {
        $sql = "INSERT INTO poll_games (game_name, game_picture, votes) VALUES (?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $gameName, $gamePicture);
        return $stmt->execute();
    }

    /**
     * Vote for a game
     */
    public function voteForGame($gameName) {
        $sql = "UPDATE poll_games SET votes = votes + 1 WHERE game_name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $gameName);
        return $stmt->execute();
    }

    /**
     * Check if user has already voted (we'll use session for this)
     */
    public function hasUserVoted() {
        return isset($_SESSION['has_voted']) && $_SESSION['has_voted'] === true;
    }

    /**
     * Mark user as voted
     */
    public function markUserAsVoted() {
        $_SESSION['has_voted'] = true;
    }

    /**
     * Get total votes across all games
     */
    public function getTotalVotes() {
        $sql = "SELECT SUM(votes) as total FROM poll_games";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    /**
     * Clear all poll games (for admin to reset)
     */
    public function clearAllPollGames() {
        $sql = "DELETE FROM poll_games";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Get poll statistics
     */
    public function getPollStats() {
        $totalVotes = $this->getTotalVotes();
        $games = $this->getAllPollGames();
        
        $stats = [];
        foreach ($games as $game) {
            $percentage = $totalVotes > 0 ? ($game['votes'] / $totalVotes) * 100 : 0;
            $stats[] = [
                'game_name' => $game['game_name'],
                'game_picture' => $game['game_picture'],
                'votes' => $game['votes'],
                'percentage' => round($percentage, 1)
            ];
        }
        
        return $stats;
    }
}
?>
