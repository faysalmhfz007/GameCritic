<?php
require_once __DIR__ . '/../config/database.php';

class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        error_log("BaseModel: Creating new instance");
        $database = new Database();
        $this->db = $database->getConnection();
        
        if ($this->db) {
            error_log("BaseModel: Database connection successful");
        } else {
            error_log("BaseModel: Database connection failed");
        }
    }

    public function findAll() {
        $query = "SELECT * FROM {$this->table}";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>



