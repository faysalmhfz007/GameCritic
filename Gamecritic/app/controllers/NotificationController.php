<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class NotificationController extends BaseController {
    private $notificationModel;

    public function __construct() {
        $this->requireLogin();
        $this->notificationModel = new NotificationModel();
    }

    // Get notifications
    public function getNotifications() {
        $currentUser = $this->getCurrentUser();
        $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] == '1';
        $notifications = $this->notificationModel->getNotifications($currentUser['id'], 20, $unreadOnly);
        $this->jsonResponse(['success' => true, 'notifications' => $notifications]);
    }

    // Get unread count
    public function getUnreadCount() {
        $currentUser = $this->getCurrentUser();
        $count = $this->notificationModel->getUnreadCount($currentUser['id']);
        $this->jsonResponse(['success' => true, 'count' => $count]);
    }

    // Mark notification as read
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $notificationId = (int)($_POST['notification_id'] ?? 0);

        if ($this->notificationModel->markAsRead($notificationId, $currentUser['id'])) {
            $this->jsonResponse(['success' => true, 'message' => 'Notification marked as read']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to mark notification as read']);
        }
    }

    // Mark all notifications as read
    public function markAllAsRead() {
        $currentUser = $this->getCurrentUser();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        if ($this->notificationModel->markAllAsRead($currentUser['id'])) {
            $this->jsonResponse(['success' => true, 'message' => 'All notifications marked as read']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to mark notifications as read']);
        }
    }

    // Delete notification
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $notificationId = (int)($_POST['notification_id'] ?? 0);

        if ($this->notificationModel->deleteNotification($notificationId, $currentUser['id'])) {
            $this->jsonResponse(['success' => true, 'message' => 'Notification deleted']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete notification']);
        }
    }
}
?>
