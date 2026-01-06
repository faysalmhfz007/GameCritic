<?php
$title = 'Poll Management | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">📊 Poll Management</h1>
                <a href="<?php echo $baseUrl; ?>/admin/dashboard" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php if ($_GET['success'] === 'poll_created'): ?>
                        Poll created successfully!
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                    switch($_GET['error']) {
                        case 'missing_games':
                            echo 'Please fill in all game names.';
                            break;
                        case 'duplicate_games':
                            echo 'Please use different game names.';
                            break;
                        case 'creation_failed':
                            echo 'Failed to create poll. Please try again.';
                            break;
                        default:
                            echo 'An error occurred. Please try again.';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Create Poll Form -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Create New Poll</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo $baseUrl; ?>/admin/create-poll">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="game1_name" class="form-label">Game 1 Name *</label>
                                <input type="text" class="form-control" id="game1_name" name="game1_name" 
                                       value="<?php echo htmlspecialchars($_POST['game1_name'] ?? ''); ?>" 
                                       placeholder="e.g., Cyberpunk 2077" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="game1_picture" class="form-label">Game 1 Picture URL</label>
                                <input type="url" class="form-control" id="game1_picture" name="game1_picture" 
                                       value="<?php echo htmlspecialchars($_POST['game1_picture'] ?? ''); ?>" 
                                       placeholder="https://example.com/image.jpg (optional)">
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-label">&nbsp;</div>
                                <div id="game1_preview" class="text-center">
                                    <img src="" alt="Game 1 Preview" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="game2_name" class="form-label">Game 2 Name *</label>
                                <input type="text" class="form-control" id="game2_name" name="game2_name" 
                                       value="<?php echo htmlspecialchars($_POST['game2_name'] ?? ''); ?>" 
                                       placeholder="e.g., Elden Ring" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="game2_picture" class="form-label">Game 2 Picture URL</label>
                                <input type="url" class="form-control" id="game2_picture" name="game2_picture" 
                                       value="<?php echo htmlspecialchars($_POST['game2_picture'] ?? ''); ?>" 
                                       placeholder="https://example.com/image.jpg (optional)">
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-label">&nbsp;</div>
                                <div id="game2_preview" class="text-center">
                                    <img src="" alt="Game 2 Preview" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="game3_name" class="form-label">Game 3 Name *</label>
                                <input type="text" class="form-control" id="game3_name" name="game3_name" 
                                       value="<?php echo htmlspecialchars($_POST['game3_name'] ?? ''); ?>" 
                                       placeholder="e.g., Starfield" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="game3_picture" class="form-label">Game 3 Picture URL</label>
                                <input type="url" class="form-control" id="game3_picture" name="game3_picture" 
                                       value="<?php echo htmlspecialchars($_POST['game3_picture'] ?? ''); ?>" 
                                       placeholder="https://example.com/image.jpg (optional)">
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-label">&nbsp;</div>
                                <div id="game3_preview" class="text-center">
                                    <img src="" alt="Game 3 Preview" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> Creating a new poll will replace any existing poll games. 
                                    Users can vote for one game only. Game pictures are optional - if not provided, a default image will be used.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Create Poll
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Poll Games -->
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Current Poll Games</h5>
                    <?php if (!empty($pollGames)): ?>
                        <button type="button" class="btn btn-danger btn-sm" onclick="clearPolls()">
                            <i class="fas fa-trash"></i> Clear All
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($pollGames)): ?>
                        <div class="text-center p-4">
                            <h5 class="text-muted">No poll games found</h5>
                            <p class="text-muted">Create a poll above to get started!</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Game Picture</th>
                                        <th>Game Name</th>
                                        <th>Votes</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalVotes = array_sum(array_column($pollGames, 'votes'));
                                    foreach ($pollGames as $index => $game): 
                                        $percentage = $totalVotes > 0 ? ($game['votes'] / $totalVotes) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">#<?php echo $index + 1; ?></span>
                                        </td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($game['game_picture']); ?>" 
                                                 alt="<?php echo htmlspecialchars($game['game_name']); ?>" 
                                                 class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($game['game_name']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning"><?php echo $game['votes']; ?> votes</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-info" style="width: <?php echo $percentage; ?>%">
                                                    <?php echo number_format($percentage, 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
function setupImagePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    
    input.addEventListener('input', function() {
        if (this.value) {
            img.src = this.value;
            img.style.display = 'block';
            img.onerror = function() {
                this.style.display = 'none';
            };
        } else {
            img.style.display = 'none';
        }
    });
}

// Setup previews for all game inputs
setupImagePreview('game1_picture', 'game1_preview');
setupImagePreview('game2_picture', 'game2_preview');
setupImagePreview('game3_picture', 'game3_preview');

// Clear polls function
function clearPolls() {
    if (confirm('Are you sure you want to clear all poll games? This action cannot be undone.')) {
        fetch('<?php echo $baseUrl; ?>/admin/clear-polls', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to clear polls: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while clearing polls.');
        });
    }
}
</script>
