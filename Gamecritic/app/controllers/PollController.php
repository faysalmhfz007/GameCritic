<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PollModel.php';

class PollController extends BaseController {
    private $pollModel;

    public function __construct() {
        $this->pollModel = new PollModel();
    }

    /**
     * Handle poll voting
     */
    public function vote() {
        $this->ensureSessionStarted();
        
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Please login to vote']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method']);
        }

        $gameName = trim($_POST['game_name'] ?? '');

        if (empty($gameName)) {
            $this->jsonResponse(['success' => false, 'error' => 'Please select a game to vote for']);
        }

        // Check if user already voted
        if ($this->pollModel->hasUserVoted()) {
            $this->jsonResponse(['success' => false, 'error' => 'You have already voted!']);
        }

        // Cast vote
        if ($this->pollModel->voteForGame($gameName)) {
            $this->pollModel->markUserAsVoted();
            $this->jsonResponse(['success' => true, 'message' => 'Vote recorded successfully!']);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to record vote']);
        }
    }
}
?>
