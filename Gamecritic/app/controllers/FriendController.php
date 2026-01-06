<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/FriendModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../config/database.php';

class FriendController extends BaseController {
    private $friendModel;
    private $userModel;
    private $notificationModel;

    public function __construct() {
        $this->requireLogin();
        $this->friendModel = new FriendModel();
        $this->userModel = new UserModel();
        $this->notificationModel = new NotificationModel();
    }

    // Send friend request
    public function sendRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $receiverId = (int)($_POST['receiver_id'] ?? 0);

        if (!$receiverId || $receiverId == $currentUser['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid user']);
        }

        // Check if receiver exists
        $receiver = $this->userModel->findById($receiverId);
        if (!$receiver) {
            $this->jsonResponse(['success' => false, 'message' => 'User not found']);
        }

        // Send friend request
        if ($this->friendModel->sendFriendRequest($currentUser['id'], $receiverId)) {
            // Create notification for receiver
            $this->notificationModel->createNotification(
                $receiverId,
                'friend_request',
                "{$currentUser['name']} sent you a friend request",
                $currentUser['id'],
                'user'
            );

            $this->jsonResponse(['success' => true, 'message' => 'Friend request sent']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to send friend request or request already exists']);
        }
    }

    // Accept friend request
    public function acceptRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $requestId = (int)($_POST['request_id'] ?? 0);

        // Get request details
        $request = $this->friendModel->findById($requestId);
        if (!$request || $request['receiver_id'] != $currentUser['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }

        // Accept request
        if ($this->friendModel->acceptFriendRequest($requestId, $request['sender_id'], $request['receiver_id'])) {
            // Create notification for sender
            $sender = $this->userModel->findById($request['sender_id']);
            $this->notificationModel->createNotification(
                $request['sender_id'],
                'friend_accepted',
                "{$currentUser['name']} accepted your friend request",
                $currentUser['id'],
                'user'
            );

            $this->jsonResponse(['success' => true, 'message' => 'Friend request accepted']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to accept friend request']);
        }
    }

    // Reject friend request
    public function rejectRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $requestId = (int)($_POST['request_id'] ?? 0);

        // Get request details
        $request = $this->friendModel->findById($requestId);
        if (!$request || $request['receiver_id'] != $currentUser['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }

        if ($this->friendModel->rejectFriendRequest($requestId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Friend request rejected']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to reject friend request']);
        }
    }

    // Cancel friend request
    public function cancelRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $requestId = (int)($_POST['request_id'] ?? 0);

        // Get request details
        $request = $this->friendModel->findById($requestId);
        if (!$request || $request['sender_id'] != $currentUser['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }

        if ($this->friendModel->cancelFriendRequest($requestId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Friend request cancelled']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to cancel friend request']);
        }
    }

    // Get friend requests page
    public function requests() {
        $currentUser = $this->getCurrentUser();
        $pendingRequests = $this->friendModel->getPendingRequests($currentUser['id']);
        $sentRequests = $this->friendModel->getSentRequests($currentUser['id']);

        return $this->render('user/friend-requests', [
            'currentUser' => $currentUser,
            'pendingRequests' => $pendingRequests,
            'sentRequests' => $sentRequests
        ]);
    }

    // Get friends list
    public function friends() {
        $currentUser = $this->getCurrentUser();
        $friends = $this->friendModel->getFriends($currentUser['id']);

        return $this->render('user/friends', [
            'currentUser' => $currentUser,
            'friends' => $friends
        ]);
    }

    // Search users for friend requests
    public function searchUsers() {
        $currentUser = $this->getCurrentUser();
        $searchTerm = trim($_GET['q'] ?? '');

        if (empty($searchTerm)) {
            $this->jsonResponse(['success' => true, 'users' => []]);
        }

        // Use UserModel to search users (excludes banned users and current user)
        $users = $this->userModel->searchUsers($searchTerm, $currentUser['id']);

        // Check friendship status for each user
        foreach ($users as &$user) {
            $user['is_friend'] = $this->friendModel->areFriends($currentUser['id'], $user['id']);
            $request = $this->friendModel->findRequest($currentUser['id'], $user['id']);
            $user['has_pending_request'] = ($request && $request['status'] == 'pending');
        }

        $this->jsonResponse(['success' => true, 'users' => $users]);
    }
}
?>
