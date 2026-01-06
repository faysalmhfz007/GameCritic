<?php
$title = 'Edit Game | GameCritic';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Game
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo $baseUrl; ?>/admin/edit-game/<?php echo $game['id']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Game Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($game['title'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="genre" class="form-label">Genre *</label>
                                    <input type="text" class="form-control" id="genre" name="genre" 
                                           value="<?php echo htmlspecialchars($game['genre'] ?? ''); ?>" 
                                           placeholder="e.g., Action, RPG, Strategy, FPS" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="platform" class="form-label">Platform *</label>
                                    <input type="text" class="form-control" id="platform" name="platform" 
                                           value="<?php echo htmlspecialchars($game['platform'] ?? ''); ?>" 
                                           placeholder="e.g., PC, PlayStation 5, Xbox Series X, Nintendo Switch" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="release_year" class="form-label">Release Year *</label>
                                    <input type="number" class="form-control" id="release_year" name="release_year" 
                                           min="1900" max="2030" value="<?php echo $game['release_year'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($game['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                            <div class="form-text">Leave empty to keep current image</div>
                            <?php if (!empty($game['cover_image'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current image:</small><br>
                                    <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($game['cover_image']); ?>" 
                                         alt="Current cover" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo $baseUrl; ?>/admin/dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Game
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>