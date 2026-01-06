<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        
        return $this->render('auth/login');
    }

    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->redirect('/login?error=missing_fields');
        }

        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            error_log("AuthController: User authenticated successfully: " . $user['email']);
            error_log("AuthController: User is_admin: " . ($user['is_admin'] ?? 0));
            
            // Ensure session is started before writing
            $this->ensureSessionStarted();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'] ?? ($user['name'] ?? '');
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
            
            // Redirect based on user type
            if ($user['is_admin'] == 1) {
                error_log("AuthController: Redirecting admin to /admin/dashboard");
                $this->redirect('/admin/dashboard');
            } else {
                error_log("AuthController: Redirecting user to homepage");
                $this->redirect('/');
            }
        } else {
            error_log("AuthController: Authentication failed");
            $this->redirect('/login?error=invalid_credentials');
        }
    }

    public function signup() {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        
        return $this->render('auth/signup');
    }

    public function signupProcess() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/signup');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $this->redirect('/signup?error=missing_fields');
        }

        if ($password !== $confirmPassword) {
            $this->redirect('/signup?error=password_mismatch');
        }

        if (strlen($password) < 6) {
            $this->redirect('/signup?error=password_too_short');
        }

        // Check if email already exists
        $existingUser = $this->userModel->getUserByEmail($email);
        if ($existingUser) {
            $this->redirect('/signup?error=email_exists');
        }

        // Create user
        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];

        if ($this->userModel->createUser($userData)) {
            $this->redirect('/login?success=account_created');
        } else {
            $this->redirect('/signup?error=creation_failed');
        }
    }

    public function logout() {
        $this->ensureSessionStarted();
        
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        $this->redirect('/');
    }
}
?>



