<?php
require_once __DIR__ . '/BaseModel.php';

class FriendModel extends BaseModel {
    protected $table = 'friend_requests';

    // Send a friend request
    public function sendFriendRequest($senderId, $receiverId) {
        // Check if request already exists
        $existing = $this->findRequest($senderId, $receiverId);
        if ($existing) {
            return false; // Request already exists
        }

        // Check if users are already friends
        if ($this->areFriends($senderId, $receiverId)) {
            return false; // Already friends
        }

        $query = "INSERT INTO {$this->table} (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $senderId, $receiverId);
        return $stmt->execute();
    }

    // Find a friend request
    public function findRequest($senderId, $receiverId) {
        $query = "SELECT * FROM {$this->table} WHERE sender_id = ? AND receiver_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $senderId, $receiverId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Accept a friend request
    public function acceptFriendRequest($requestId, $senderId, $receiverId) {
        // Update request status
        $query = "UPDATE {$this->table} SET status = 'accepted' WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $requestId);
        
        if ($stmt->execute()) {
            // Add to friendships table
            $friendshipModel = new FriendshipModel();
            return $friendshipModel->createFriendship($senderId, $receiverId);
        }
        return false;
    }

    // Reject a friend request
    public function rejectFriendRequest($requestId) {
        $query = "UPDATE {$this->table} SET status = 'rejected' WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $requestId);
        return $stmt->execute();
    }

    // Cancel a friend request
    public function cancelFriendRequest($requestId) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $requestId);
        return $stmt->execute();
    }

    // Get pending friend requests for a user
    public function getPendingRequests($userId) {
        $query = "SELECT fr.*, u.id as sender_user_id, u.username as sender_username, u.profile_picture as sender_profile_picture 
                  FROM {$this->table} fr 
                  JOIN users u ON fr.sender_id = u.id 
                  WHERE fr.receiver_id = ? AND fr.status = 'pending' 
                  ORDER BY fr.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get sent friend requests
    public function getSentRequests($userId) {
        $query = "SELECT fr.*, u.id as receiver_user_id, u.username as receiver_username, u.profile_picture as receiver_profile_picture 
                  FROM {$this->table} fr 
                  JOIN users u ON fr.receiver_id = u.id 
                  WHERE fr.sender_id = ? AND fr.status = 'pending' 
                  ORDER BY fr.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check if two users are friends
    public function areFriends($userId1, $userId2) {
        $friendshipModel = new FriendshipModel();
        return $friendshipModel->checkFriendship($userId1, $userId2);
    }

    // Get all friends of a user
    public function getFriends($userId) {
        $friendshipModel = new FriendshipModel();
        return $friendshipModel->getUserFriends($userId);
    }
}

// Separate model for friendships table
class FriendshipModel extends BaseModel {
    protected $table = 'friendships';

    public function createFriendship($userId1, $userId2) {
        // Ensure smaller ID is always user1_id
        if ($userId1 > $userId2) {
            $temp = $userId1;
            $userId1 = $userId2;
            $userId2 = $temp;
        }

        $query = "INSERT INTO {$this->table} (user1_id, user2_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId1, $userId2);
        return $stmt->execute();
    }

    public function checkFriendship($userId1, $userId2) {
        // Ensure smaller ID is always user1_id
        if ($userId1 > $userId2) {
            $temp = $userId1;
            $userId1 = $userId2;
            $userId2 = $temp;
        }

        $query = "SELECT * FROM {$this->table} WHERE user1_id = ? AND user2_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId1, $userId2);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() !== null;
    }

    public function getUserFriends($userId) {
        $query = "SELECT u.* FROM {$this->table} f
                  JOIN users u ON (
                      (f.user1_id = ? AND u.id = f.user2_id) OR 
                      (f.user2_id = ? AND u.id = f.user1_id)
                  )
                  WHERE f.user1_id = ? OR f.user2_id = ?
                  ORDER BY u.username";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function removeFriendship($userId1, $userId2) {
        // Ensure smaller ID is always user1_id
        if ($userId1 > $userId2) {
            $temp = $userId1;
            $userId1 = $userId2;
            $userId2 = $temp;
        }

        $query = "DELETE FROM {$this->table} WHERE user1_id = ? AND user2_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId1, $userId2);
        return $stmt->execute();
    }
}
?>
