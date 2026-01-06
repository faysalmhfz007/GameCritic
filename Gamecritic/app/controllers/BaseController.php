<?php
class BaseController {
    protected function ensureSessionStarted() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }
    protected function baseUrl() {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // For now, hardcode the base path for Gamecritic
        $basePath = '/Gamecritic/public';
        
        return $scheme . '://' . $host . $basePath;
    }
    protected function render($view, $data = []) {
        // Always include currentUser if not already set
        if (!isset($data['currentUser'])) {
            $data['currentUser'] = $this->getCurrentUser();
        }
        
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering for content
        ob_start();
        
        // Make base URL available to views
        $baseUrl = $this->baseUrl();

        // Include the view file
        include __DIR__ . "/../views/{$view}.php";
        
        // Get the contents and clean the buffer
        $content = ob_get_clean();
        
        // Now render the layout with the content
        ob_start();
        include __DIR__ . "/../views/layouts/main.php";
        $finalOutput = ob_get_clean();
        
        // Return the final rendered content
        return $finalOutput;
    }

    protected function redirect($url) {
        // Debug: Log the redirect attempt
        error_log("BaseController: Redirecting to: {$url}");
        
        // If it's a relative URL, make it absolute
        if (strpos($url, 'http') !== 0) {
            $url = $this->baseUrl() . $url;
            error_log("BaseController: Made absolute URL: {$url}");
        }
        
        error_log("BaseController: Final redirect URL: {$url}");
        header("Location: {$url}");
        exit();
    }

    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function isLoggedIn() {
        $this->ensureSessionStarted();
        return isset($_SESSION['user_id']);
    }

    protected function isAdmin() {
        $this->ensureSessionStarted();
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    protected function requireLogin() {
        $this->ensureSessionStarted();
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin() {
        $this->ensureSessionStarted();
        if (!$this->isAdmin()) {
            $this->redirect('/');
        }
    }

    protected function getCurrentUser() {
        $this->ensureSessionStarted();
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'username' => $_SESSION['user_name'], // Alias for compatibility
                'email' => $_SESSION['user_email'],
                'is_admin' => $_SESSION['is_admin'] ?? 0
            ];
        }
        return null;
    }
}
?>


