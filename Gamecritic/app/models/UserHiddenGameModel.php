<?php
require_once __DIR__ . '/BaseModel.php';

class UserHiddenGameModel extends BaseModel {
    protected $table = 'user_hidden_games';

    // Hide a game for a user (mark as spoiler)
    public function hideGame($userId, $gameId) {
        $query = "INSERT INTO {$this->table} (user_id, game_id) VALUES (?, ?) 
                  ON DUPLICATE KEY UPDATE created_at = NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $gameId);
        return $stmt->execute();
    }

    // Unhide a game for a user
    public function unhideGame($userId, $gameId) {
        $query = "DELETE FROM {$this->table} WHERE user_id = ? AND game_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $gameId);
        return $stmt->execute();
    }

    // Check if game is hidden for user
    public function isHidden($userId, $gameId) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? AND game_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() !== null;
    }

    // Get all hidden game IDs for a user
    public function getHiddenGameIds($userId) {
        $query = "SELECT game_id FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $games = $result->fetch_all(MYSQLI_ASSOC);
        return array_column($games, 'game_id');
    }
}
?>
