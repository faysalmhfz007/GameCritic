<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';

class AdminUserController extends BaseController {
    private $userModel;
    private $reviewModel;

    public function __construct() {
        $this->requireAdmin();
        $this->userModel = new UserModel();
        $this->reviewModel = new ReviewModel();
    }

    // List users with reviews
    public function listUsers() {
        $users = $this->userModel->getUsersWithReviews();
        
        // Get review details for each user
        foreach ($users as &$user) {
            $user['reviews'] = $this->reviewModel->getReviewsByUserId($user['id']);
        }

        return $this->render('admin/users', [
            'currentUser' => $this->getCurrentUser(),
            'users' => $users
        ]);
    }

    // Ban user
    public function banUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Violation of community guidelines');

        if (!$userId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid user ID']);
        }

        if ($this->userModel->banUser($userId, $reason)) {
            $this->jsonResponse(['success' => true, 'message' => 'User banned successfully']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to ban user']);
        }
    }

    // Unban user
    public function unbanUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        if (!$userId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid user ID']);
        }

        if ($this->userModel->unbanUser($userId)) {
            $this->jsonResponse(['success' => true, 'message' => 'User unbanned successfully']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to unban user']);
        }
    }
}
?>
