<?php
$title = 'Admin Dashboard | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">🎮 Game Management</h1>
                <div>
                    <a href="<?php echo $baseUrl; ?>/admin/users" class="btn btn-danger me-2">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/polls" class="btn btn-info me-2">
                        <i class="fas fa-poll"></i> Manage Polls
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/add-game" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Game
                    </a>
                </div>
            </div>

            <!-- Games List -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">All Games</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($games)): ?>
                        <div class="text-center p-4">
                            <h5 class="text-muted">No games found</h5>
                            <p class="text-muted">Add your first game to get started!</p>
                            <a href="<?php echo $baseUrl; ?>/admin/add-game" class="btn btn-primary">Add Game</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th>Cover</th>
                                        <th>Title</th>
                                        <th>Genre</th>
                                        <th>Platform</th>
                                        <th>Year</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($games as $game): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($game['cover_image'] ?? '/images/default.jpg'); ?>" 
                                                 alt="<?php echo htmlspecialchars($game['title'] ?? 'Game'); ?>" 
                                                 class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($game['title'] ?? 'Untitled'); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($game['description'] ?? 'No description', 0, 50)) . '...'; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($game['genre'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td><?php echo $game['release_year'] ?? 'N/A'; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo $baseUrl; ?>/admin/edit-game/<?php echo $game['id']; ?>" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="deleteGame(<?php echo $game['id']; ?>, '<?php echo htmlspecialchars($game['title'] ?? 'this game'); ?>')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="gameTitle"></strong>?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Delete Game</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteGame(gameId, gameTitle) {
    document.getElementById('gameTitle').textContent = gameTitle;
    document.getElementById('deleteForm').action = '<?php echo $baseUrl; ?>/admin/delete-game/' + gameId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>