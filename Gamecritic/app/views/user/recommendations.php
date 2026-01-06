<?php
$title = 'Recommendations | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">💡 Recommendations</h1>
                <a href="<?php echo $baseUrl; ?>/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #ffc107, #ff8c00);">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb"></i> Recommended for You
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recommendedGames)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Recommendations Available</h5>
                            <p class="text-muted">Start reviewing games to get personalized recommendations!</p>
                            <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary">
                                <i class="fas fa-gamepad"></i> Browse Games
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <?php foreach ($recommendedGames as $game): ?>
                            <div class="col">
                                <a href="<?php echo $baseUrl; ?>/game/<?php echo $game['id']; ?>" class="text-decoration-none">
                                    <div class="card h-100 game-card shadow-sm">
                                        <?php 
                                          $cover = $game['cover_image'] ?? '/images/default.jpg';
                                          if (strpos($cover, '/images/') === 0) {
                                              $imgSrc = $baseUrl . $cover;
                                          } elseif (strpos($cover, 'images/') === 0) {
                                              $imgSrc = $baseUrl . '/' . $cover;
                                          } else {
                                              $imgSrc = $baseUrl . '/images/' . $cover;
                                          }
                                        ?>
                                        <img src="<?php echo $imgSrc; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($game['title'] ?? ''); ?>" style="height: 200px; object-fit: cover;">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title text-white mb-2"><?php echo htmlspecialchars($game['title'] ?? ''); ?></h6>
                                            <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($game['description'] ?? 'No description available', 0, 80)) . '...'; ?></p>
                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge bg-primary"><?php echo htmlspecialchars($game['genre'] ?? 'N/A'); ?></span>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform'] ?? 'N/A'); ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted"><?php echo $game['release_year'] ?? 'N/A'; ?></small>
                                                    <div class="rating-badge">
                                                        <span class="badge bg-success"><?php echo number_format($game['overall_score'] ?? 0, 1); ?>/10</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Recommendations are based on your review history and favorite genres.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



