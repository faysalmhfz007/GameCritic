<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';

class GameController extends BaseController {
    private $gameModel;
    private $reviewModel;

    public function __construct() {
        $this->gameModel = new GameModel();
        $this->reviewModel = new ReviewModel();
    }

    public function show($id) {
        $game = $this->gameModel->findById((int)$id);
        if (!$game) {
            http_response_code(404);
            return 'Game not found';
        }

        // Normalize cover image path
        $cover = $game['cover_image'] ?? '';
        if ($cover !== '') {
            if (strpos($cover, '/images/') === 0) {
                $game['cover_resolved'] = $this->baseUrl() . $cover;
            } elseif (strpos($cover, 'images/') === 0) {
                $game['cover_resolved'] = $this->baseUrl() . '/' . $cover;
            } else {
                $game['cover_resolved'] = $cover;
            }
        } else {
            $game['cover_resolved'] = $this->baseUrl() . '/images/default.jpg';
        }

        $aggregates = $this->reviewModel->getAggregates((int)$id);
        $reviews = $this->reviewModel->getCommentsForGameSorted((int)$id);
        
        // Get current user's vote status if logged in
        $userVote = null;
        $isHidden = false;
        if (isset($_SESSION['user_id'])) {
            $userVote = $this->reviewModel->getUserVote((int)$id, (int)$_SESSION['user_id']);
            require_once __DIR__ . '/../models/UserHiddenGameModel.php';
            $hiddenGameModel = new UserHiddenGameModel();
            $isHidden = $hiddenGameModel->isHidden((int)$_SESSION['user_id'], (int)$id);
        }

        return $this->render('game/show', [
            'game' => $game,
            'currentUser' => $this->getCurrentUser(),
            'aggregates' => $aggregates,
            'reviews' => $reviews,
            'userVote' => $userVote,
            'isHidden' => $isHidden,
        ]);
    }

    public function thumb($id) {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        $isUp = isset($_POST['type']) && $_POST['type'] === 'up';
        $ok = $this->reviewModel->addThumb((int)$id, (int)$_SESSION['user_id'], $isUp);
        if ($ok) {
            // Recalculate all counts from reviews table and update games table
            $this->gameModel->recomputeAllCountsFromReviews((int)$id);
            $this->redirect('/game/' . (int)$id);
        } else {
            http_response_code(400);
            return 'Unable to save thumb';
        }
    }

    public function review($id) {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        // Comments do not influence score; store neutral rating
        $ratingStore = 0.0;
        $comment = trim($_POST['comment'] ?? '');
        $ok = $this->reviewModel->addReview((int)$id, (int)$_SESSION['user_id'], $ratingStore, $comment);
        if ($ok) {
            // Also append to games.comments log field
            $username = $_SESSION['user_name'] ?? '';
            $safeComment = substr($comment, 0, 2000);
            $this->gameModel->appendCommentToGame((int)$id, $username, $safeComment);
            $this->redirect('/game/' . (int)$id);
        } else {
            http_response_code(400);
            return 'Unable to save review';
        }
    }

    public function voteComment($gameId, $commentId) {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login to vote']);
        }

        $voteType = $_POST['vote_type'] ?? '';
        if (!in_array($voteType, ['up', 'down'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid vote type']);
        }

        $result = $this->reviewModel->voteComment($commentId, $_SESSION['user_id'], $voteType);
        
        if ($result['success']) {
            // Get updated comment data
            $comments = $this->reviewModel->getCommentsForGameSorted($gameId);
            $this->jsonResponse([
                'success' => true, 
                'action' => $result['action'],
                'comments' => $comments
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Unable to vote']);
        }
    }
}
?>


