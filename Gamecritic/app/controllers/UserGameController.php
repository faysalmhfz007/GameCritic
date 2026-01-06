<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserHiddenGameModel.php';

class UserGameController extends BaseController {
    private $hiddenGameModel;

    public function __construct() {
        $this->requireLogin();
        $this->hiddenGameModel = new UserHiddenGameModel();
    }

    // Hide game (mark as spoiler)
    public function hideGame() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $gameId = (int)($_POST['game_id'] ?? 0);

        if (!$gameId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid game ID']);
        }

        if ($this->hiddenGameModel->hideGame($currentUser['id'], $gameId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Game hidden']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to hide game']);
        }
    }

    // Unhide game
    public function unhideGame() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $currentUser = $this->getCurrentUser();
        $gameId = (int)($_POST['game_id'] ?? 0);

        if (!$gameId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid game ID']);
        }

        if ($this->hiddenGameModel->unhideGame($currentUser['id'], $gameId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Game unhidden']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to unhide game']);
        }
    }
}
?>
