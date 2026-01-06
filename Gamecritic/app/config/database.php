<?php
class Database {
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = "";
    private $database = "gamecritic";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $mysqli = mysqli_init();
            if (!$mysqli) {
                throw new Exception('Failed to initialize MySQLi');
            }
            // Fail fast if DB is down
            $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
            // Connect
            if (!$mysqli->real_connect($this->host, $this->username, $this->password, $this->database)) {
                throw new Exception('Connection failed: ' . mysqli_connect_error());
            }
            $mysqli->set_charset('utf8mb4');
            $this->conn = $mysqli;
        } catch (Exception $e) {
            // Surface a clear message without hanging
            header('Content-Type: text/plain');
            http_response_code(500);
            echo 'Database connection error: ' . $e->getMessage();
            exit;
        }
        return $this->conn;
    }
}
?>



