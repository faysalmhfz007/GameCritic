<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    protected $table = 'users';

    public function authenticate($email, $password) {
        error_log("UserModel: Attempting to authenticate email: {$email}");
        
        $query = "SELECT * FROM {$this->table} WHERE email = ?";
        error_log("UserModel: Query: {$query}");
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("UserModel: Prepare failed: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        error_log("UserModel: User found: " . ($user ? 'yes' : 'no'));
        if ($user) {
            // Check if user is banned
            if (isset($user['banned']) && $user['banned'] == 1) {
                error_log("UserModel: User is banned");
                return false;
            }

            error_log("UserModel: User data: " . json_encode($user));
            $storedPassword = $user['password'] ?? '';
            // Detect if stored password is a hash (password_get_info algo > 0) or plain text
            $info = is_string($storedPassword) ? password_get_info($storedPassword) : ['algo' => 0];
            $isHashed = isset($info['algo']) && $info['algo'] !== 0;

            $verified = false;
            if ($isHashed) {
                $verified = password_verify($password, $storedPassword);
                error_log("UserModel: Detected hashed password. Verify result: " . ($verified ? 'true' : 'false'));
            } else {
                // Fallback to plain-text comparison for legacy data
                $verified = hash_equals((string)$storedPassword, (string)$password);
                error_log("UserModel: Detected plain-text password. Compare result: " . ($verified ? 'true' : 'false'));
            }

            if ($verified) {
                error_log("UserModel: Authentication successful");
                return $user;
            }
        }

        if ($user && password_verify($password, $user['password'])) {
            error_log("UserModel: Authentication successful");
            return $user;
        }
        
        error_log("UserModel: Authentication failed");
        return false;
    }

    public function createUser($data) {
        // Match current DB schema: users(id, username, email, phone, password)
        $query = "INSERT INTO {$this->table} (username, email, phone, password) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        // Store as plain to match existing data convention if requested; otherwise hash.
        $storeHashed = false;
        $passwordToStore = $storeHashed ? password_hash($data['password'], PASSWORD_DEFAULT) : (string)$data['password'];
        $username = $data['name'] ?? $data['username'] ?? '';
        $phone = $data['phone'] ?? '';
        $stmt->bind_param("ssss", 
            $username,
            $data['email'], 
            $phone,
            $passwordToStore
        );
        return $stmt->execute();
    }

    public function updateUser($id, $data) {
        // Build dynamic query based on provided data
        $fields = [];
        $types = '';
        $values = [];
        
        if (isset($data['username'])) {
            $fields[] = 'username = ?';
            $types .= 's';
            $values[] = $data['username'];
        }
        
        if (isset($data['email'])) {
            $fields[] = 'email = ?';
            $types .= 's';
            $values[] = $data['email'];
        }
        
        if (isset($data['phone'])) {
            $fields[] = 'phone = ?';
            $types .= 's';
            $values[] = $data['phone'];
        }
        
        if (isset($data['profile_picture'])) {
            $fields[] = 'profile_picture = ?';
            $types .= 's';
            $values[] = $data['profile_picture'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $types .= 'i';
        $values[] = $id;
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function findByUsernameOrEmail($username, $email, $excludeUserId = null) {
        $query = "SELECT * FROM {$this->table} WHERE (username = ? OR email = ?)";
        $types = "ss";
        $values = [$username, $email];
        
        if ($excludeUserId) {
            $query .= " AND id != ?";
            $types .= "i";
            $values[] = $excludeUserId;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        // First verify current password
        $user = $this->findById($userId);
        if (!$user) {
            return false;
        }
        
        $storedPassword = $user['password'];
        $info = is_string($storedPassword) ? password_get_info($storedPassword) : ['algo' => 0];
        $isHashed = isset($info['algo']) && $info['algo'] !== 0;
        
        $verified = false;
        if ($isHashed) {
            $verified = password_verify($currentPassword, $storedPassword);
        } else {
            $verified = hash_equals((string)$storedPassword, (string)$currentPassword);
        }
        
        if (!$verified) {
            return false;
        }
        
        // Update to new password (store as plain text as per user's preference)
        $query = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $newPassword, $userId);
        return $stmt->execute();
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function isAdmin($userId) {
        $query = "SELECT is_admin FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user && $user['is_admin'] == 1;
    }

    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalAdmins() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_admin = 1";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Search users (excluding banned users and specified user)
    public function searchUsers($searchTerm, $excludeUserId = null, $limit = 10) {
        $query = "SELECT id, username, email, profile_picture FROM {$this->table} 
                  WHERE (username LIKE ? OR email LIKE ?) 
                  AND (banned IS NULL OR banned = 0)";
        
        $types = "ss";
        $values = ["%{$searchTerm}%", "%{$searchTerm}%"];
        
        if ($excludeUserId) {
            $query .= " AND id != ?";
            $types .= "i";
            $values[] = $excludeUserId;
        }
        
        $query .= " LIMIT ?";
        $types .= "i";
        $values[] = $limit;
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Ban user
    public function banUser($userId, $reason = null) {
        $query = "UPDATE {$this->table} SET banned = 1, banned_at = NOW(), banned_reason = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $reason, $userId);
        return $stmt->execute();
    }

    // Unban user
    public function unbanUser($userId) {
        $query = "UPDATE {$this->table} SET banned = 0, banned_at = NULL, banned_reason = NULL WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    // Check if user is banned
    public function isBanned($userId) {
        $query = "SELECT banned FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user && $user['banned'] == 1;
    }

    // Get all users with reviews (for admin)
    public function getUsersWithReviews() {
        $query = "SELECT DISTINCT u.*, 
                  (SELECT COUNT(*) FROM reviews WHERE user_id = u.id) as review_count
                  FROM {$this->table} u
                  INNER JOIN reviews r ON u.id = r.user_id
                  ORDER BY review_count DESC, u.username";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>



