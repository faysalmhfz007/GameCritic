<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PollModel.php';

class AdminController extends BaseController {
    private $gameModel;
    private $userModel;
    private $pollModel;

    public function __construct() {
        $this->gameModel = new GameModel();
        $this->userModel = new UserModel();
        $this->pollModel = new PollModel();
        $this->requireAdmin();
    }

    public function dashboard() {
        $games = $this->gameModel->findAll();
        $currentUser = $this->getCurrentUser();
        
        return $this->render('admin/dashboard', [
            'games' => $games,
            'currentUser' => $currentUser
        ]);
    }

    public function addGame() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle file upload
            $coverImage = null;
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/images/';
                $fileExtension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
                        $coverImage = $fileName;
                    }
                }
            }

            $gameData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'genre' => $_POST['genre'] ?? '',
                'platform' => $_POST['platform'] ?? '',
                'release_year' => $_POST['release_year'] ?? '',
                'cover_image' => $coverImage ? '/images/' . $coverImage : '/images/default.jpg'
            ];

            if ($this->gameModel->createGame($gameData)) {
                $this->redirect('/admin/dashboard?success=game_added');
            } else {
                $this->redirect('/admin/add-game?error=creation_failed');
            }
        }

        return $this->render('admin/add-game', [
            'formData' => $_POST ?? [],
            'error' => $_GET['error'] ?? null,
            'success' => $_GET['success'] ?? null
        ]);
    }

    public function editGame($id) {
        $game = $this->gameModel->findById($id);
        
        if (!$game) {
            $this->redirect('/admin/dashboard?error=game_not_found');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle file upload
            $coverImage = $game['cover_image']; // Keep existing image by default
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/images/';
                $fileExtension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
                        // Delete old image if it exists
                        if ($game['cover_image'] && file_exists($uploadDir . basename($game['cover_image']))) {
                            unlink($uploadDir . basename($game['cover_image']));
                        }
                        $coverImage = $fileName;
                    }
                }
            }

            // Determine the final cover image path
            $finalCoverImage = '/images/default.jpg'; // Default fallback
            
            if ($coverImage && $coverImage !== $game['cover_image']) {
                // New image was uploaded
                $finalCoverImage = '/images/' . $coverImage;
            } elseif ($game['cover_image']) {
                // Keep existing image
                $finalCoverImage = $game['cover_image'];
            }
            
            $gameData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'genre' => $_POST['genre'] ?? '',
                'platform' => $_POST['platform'] ?? '',
                'release_year' => $_POST['release_year'] ?? '',
                'cover_image' => $finalCoverImage
            ];

            if ($this->gameModel->updateGame($id, $gameData)) {
                $this->redirect('/admin/dashboard?success=game_updated');
            } else {
                $this->redirect("/admin/edit-game/{$id}?error=update_failed");
            }
        }

        return $this->render('admin/edit-game', [
            'game' => $game,
            'error' => $_GET['error'] ?? null,
            'success' => $_GET['success'] ?? null
        ]);
    }

    public function deleteGame($id) {
        if ($this->gameModel->delete($id)) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to delete game']);
        }
    }

    /**
     * Poll management page
     */
    public function polls() {
        $pollGames = $this->pollModel->getAllPollGames();
        $currentUser = $this->getCurrentUser();
        
        return $this->render('admin/polls', [
            'pollGames' => $pollGames,
            'currentUser' => $currentUser
        ]);
    }

    /**
     * Create poll with 3 games
     */
    public function createPoll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/polls');
        }

        $game1Name = trim($_POST['game1_name'] ?? '');
        $game1Picture = trim($_POST['game1_picture'] ?? '');
        $game2Name = trim($_POST['game2_name'] ?? '');
        $game2Picture = trim($_POST['game2_picture'] ?? '');
        $game3Name = trim($_POST['game3_name'] ?? '');
        $game3Picture = trim($_POST['game3_picture'] ?? '');

        // Validation
        if (empty($game1Name) || empty($game2Name) || empty($game3Name)) {
            $this->redirect('/admin/polls?error=missing_games');
        }

        // Pictures are optional - use default if not provided
        $game1Picture = !empty($game1Picture) ? $game1Picture : '/images/default.jpg';
        $game2Picture = !empty($game2Picture) ? $game2Picture : '/images/default.jpg';
        $game3Picture = !empty($game3Picture) ? $game3Picture : '/images/default.jpg';

        // Check for duplicate game names
        $games = [$game1Name, $game2Name, $game3Name];
        if (count($games) !== count(array_unique($games))) {
            $this->redirect('/admin/polls?error=duplicate_games');
        }

        // Clear existing poll games
        $this->pollModel->clearAllPollGames();

        // Add new poll games
        $success = true;
        $success &= $this->pollModel->addGameToPoll($game1Name, $game1Picture);
        $success &= $this->pollModel->addGameToPoll($game2Name, $game2Picture);
        $success &= $this->pollModel->addGameToPoll($game3Name, $game3Picture);

        if ($success) {
            $this->redirect('/admin/polls?success=poll_created');
        } else {
            $this->redirect('/admin/polls?error=creation_failed');
        }
    }

    /**
     * Clear all polls
     */
    public function clearPolls() {
        if ($this->pollModel->clearAllPollGames()) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to clear polls']);
        }
    }
}
?>



