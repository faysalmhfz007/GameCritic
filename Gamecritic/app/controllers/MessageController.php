<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class MessageController extends BaseController {
    private $messageModel;
    private $userModel;
    private $notificationModel;

    public function __construct() {
        $this->requireLogin();
        $this->messageModel = new MessageModel();
        $this->userModel = new UserModel();
        $this->notificationModel = new NotificationModel();
    }

    // Send a message
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if (!$receiverId || empty($message)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid message data']);
        }

        // Check if receiver exists
        $receiver = $this->userModel->findById($receiverId);
        if (!$receiver) {
            $this->jsonResponse(['success' => false, 'message' => 'User not found']);
        }

        // Send message
        if ($this->messageModel->sendMessage($currentUser['id'], $receiverId, $message)) {
            // Create notification for receiver
            $this->notificationModel->createNotification(
                $receiverId,
                'message',
                "New message from {$currentUser['name']}",
                $currentUser['id'],
                'user'
            );

            $this->jsonResponse(['success' => true, 'message' => 'Message sent']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to send message']);
        }
    }

    // Get conversation
    public function getConversation() {
        $currentUser = $this->getCurrentUser();
        $otherUserId = (int)($_GET['user_id'] ?? 0);

        if (!$otherUserId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid user']);
        }

        // Mark messages as read
        $this->messageModel->markAsRead($otherUserId, $currentUser['id']);

        $messages = $this->messageModel->getConversation($currentUser['id'], $otherUserId);
        $this->jsonResponse(['success' => true, 'messages' => $messages]);
    }

    // Get all conversations
    public function getConversations() {
        $currentUser = $this->getCurrentUser();
        $conversations = $this->messageModel->getConversations($currentUser['id']);
        $this->jsonResponse(['success' => true, 'conversations' => $conversations]);
    }

    // Get unread count
    public function getUnreadCount() {
        $currentUser = $this->getCurrentUser();
        $count = $this->messageModel->getUnreadCount($currentUser['id']);
        $this->jsonResponse(['success' => true, 'count' => $count]);
    }

    // Chat page
    public function chat() {
        $currentUser = $this->getCurrentUser();
        $otherUserId = (int)($_GET['user_id'] ?? 0);
        
        $otherUser = null;
        $messages = [];
        
        if ($otherUserId) {
            $otherUser = $this->userModel->findById($otherUserId);
            if ($otherUser) {
                $messages = $this->messageModel->getConversation($currentUser['id'], $otherUserId);
                // Mark messages as read
                $this->messageModel->markAsRead($otherUserId, $currentUser['id']);
            }
        }

        $conversations = $this->messageModel->getConversations($currentUser['id']);

        return $this->render('user/chat', [
            'currentUser' => $currentUser,
            'otherUser' => $otherUser,
            'messages' => $messages,
            'conversations' => $conversations
        ]);
    }
}
?>
