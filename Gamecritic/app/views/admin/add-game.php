<?php
$title = 'Add Game | GameCritic';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus"></i> Add New Game
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo $baseUrl; ?>/admin/add-game" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Game Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($formData['title'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="genre" class="form-label">Genre *</label>
                                    <input type="text" class="form-control" id="genre" name="genre" 
                                           value="<?php echo htmlspecialchars($formData['genre'] ?? ''); ?>" 
                                           placeholder="e.g., Action, RPG, Strategy, FPS" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="platform" class="form-label">Platform *</label>
                                    <input type="text" class="form-control" id="platform" name="platform" 
                                           value="<?php echo htmlspecialchars($formData['platform'] ?? ''); ?>" 
                                           placeholder="e.g., PC, PlayStation 5, Xbox Series X, Nintendo Switch" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="release_year" class="form-label">Release Year *</label>
                                    <input type="number" class="form-control" id="release_year" name="release_year" 
                                           min="1900" max="2030" value="<?php echo $formData['release_year'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Game Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Describe the game, its features, gameplay, etc." required><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image" 
                                   accept="image/*">
                            <div class="form-text">Upload a cover image (JPG, PNG, GIF). Max size: 5MB</div>
                        </div>


                        <div class="d-flex justify-content-between">
                            <a href="<?php echo $baseUrl; ?>/admin/dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Add Game
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

