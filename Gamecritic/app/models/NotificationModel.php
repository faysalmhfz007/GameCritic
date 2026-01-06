<?php
require_once __DIR__ . '/BaseModel.php';

class NotificationModel extends BaseModel {
    protected $table = 'notifications';

    // Create a notification
    public function createNotification($userId, $type, $message, $relatedId = null, $relatedType = null) {
        $query = "INSERT INTO {$this->table} (user_id, type, message, related_id, related_type) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        // related_id is INT, so use 'i' not 's'
        $stmt->bind_param("issis", $userId, $type, $message, $relatedId, $relatedType);
        return $stmt->execute();
    }

    // Get notifications for a user
    public function getNotifications($userId, $limit = 20, $unreadOnly = false) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ?";
        if ($unreadOnly) {
            $query .= " AND is_read = 0";
        }
        $query .= " ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get unread notification count
    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    // Mark notification as read
    public function markAsRead($notificationId, $userId = null) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE id = ?";
        if ($userId) {
            $query .= " AND user_id = ?";
        }
        $stmt = $this->db->prepare($query);
        if ($userId) {
            $stmt->bind_param("ii", $notificationId, $userId);
        } else {
            $stmt->bind_param("i", $notificationId);
        }
        return $stmt->execute();
    }

    // Mark all notifications as read for a user
    public function markAllAsRead($userId) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    // Delete a notification
    public function deleteNotification($notificationId, $userId = null) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        if ($userId) {
            $query .= " AND user_id = ?";
        }
        $stmt = $this->db->prepare($query);
        if ($userId) {
            $stmt->bind_param("ii", $notificationId, $userId);
        } else {
            $stmt->bind_param("i", $notificationId);
        }
        return $stmt->execute();
    }
}
?>
