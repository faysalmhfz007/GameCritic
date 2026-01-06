<?php
require_once __DIR__ . '/BaseModel.php';

class MessageModel extends BaseModel {
    protected $table = 'messages';

    // Send a message
    public function sendMessage($senderId, $receiverId, $message) {
        $query = "INSERT INTO {$this->table} (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $senderId, $receiverId, $message);
        return $stmt->execute();
    }

    // Get conversation between two users
    public function getConversation($userId1, $userId2, $limit = 50) {
        $query = "SELECT m.*, 
                  u1.username as sender_username, u1.profile_picture as sender_profile_picture,
                  u2.username as receiver_username, u2.profile_picture as receiver_profile_picture
                  FROM {$this->table} m
                  JOIN users u1 ON m.sender_id = u1.id
                  JOIN users u2 ON m.receiver_id = u2.id
                  WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                     OR (m.sender_id = ? AND m.receiver_id = ?)
                  ORDER BY m.created_at DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiiii", $userId1, $userId2, $userId2, $userId1, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        return array_reverse($messages); // Reverse to show oldest first
    }

    // Get all conversations for a user
    public function getConversations($userId) {
        // Get distinct conversation partners
        $query = "SELECT 
                  CASE 
                      WHEN m.sender_id = ? THEN m.receiver_id 
                      ELSE m.sender_id 
                  END as other_user_id
                  FROM {$this->table} m
                  WHERE m.sender_id = ? OR m.receiver_id = ?
                  GROUP BY other_user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $conversations = $result->fetch_all(MYSQLI_ASSOC);
        
        // Get details for each conversation
        $conversationList = [];
        foreach ($conversations as $conv) {
            $otherUserId = $conv['other_user_id'];
            
            // Get user details
            $userQuery = "SELECT id, username, profile_picture FROM users WHERE id = ?";
            $userStmt = $this->db->prepare($userQuery);
            $userStmt->bind_param("i", $otherUserId);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            $user = $userResult->fetch_assoc();
            
            if ($user) {
                // Get last message
                $lastMsgQuery = "SELECT message, created_at FROM {$this->table} 
                                WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                                ORDER BY created_at DESC LIMIT 1";
                $lastMsgStmt = $this->db->prepare($lastMsgQuery);
                $lastMsgStmt->bind_param("iiii", $userId, $otherUserId, $otherUserId, $userId);
                $lastMsgStmt->execute();
                $lastMsgResult = $lastMsgStmt->get_result();
                $lastMsg = $lastMsgResult->fetch_assoc();
                
                // Get unread count
                $unreadQuery = "SELECT COUNT(*) as count FROM {$this->table} 
                               WHERE receiver_id = ? AND sender_id = ? AND is_read = 0";
                $unreadStmt = $this->db->prepare($unreadQuery);
                $unreadStmt->bind_param("ii", $userId, $otherUserId);
                $unreadStmt->execute();
                $unreadResult = $unreadStmt->get_result();
                $unread = $unreadResult->fetch_assoc();
                
                $conversationList[] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'profile_picture' => $user['profile_picture'],
                    'last_message' => $lastMsg ? $lastMsg['message'] : null,
                    'last_message_time' => $lastMsg ? $lastMsg['created_at'] : null,
                    'unread_count' => $unread ? (int)$unread['count'] : 0
                ];
            }
        }
        
        // Sort by last message time
        usort($conversationList, function($a, $b) {
            $timeA = $a['last_message_time'] ?? '1970-01-01';
            $timeB = $b['last_message_time'] ?? '1970-01-01';
            return strtotime($timeB) - strtotime($timeA);
        });
        
        return $conversationList;
    }

    // Mark messages as read
    public function markAsRead($senderId, $receiverId) {
        $query = "UPDATE {$this->table} SET is_read = 1 
                  WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $senderId, $receiverId);
        return $stmt->execute();
    }

    // Get unread message count for a user
    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE receiver_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    // Get unread messages for a user
    public function getUnreadMessages($userId) {
        $query = "SELECT m.*, u.username as sender_username, u.profile_picture as sender_profile_picture
                  FROM {$this->table} m
                  JOIN users u ON m.sender_id = u.id
                  WHERE m.receiver_id = ? AND m.is_read = 0
                  ORDER BY m.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
