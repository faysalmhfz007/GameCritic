<?php
$title = 'Dashboard | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">🎮 Welcome, <?php echo htmlspecialchars($currentUser['name']); ?>!</h1>
                <div>
                    <a href="<?php echo $baseUrl; ?>/profile" class="btn btn-outline-primary me-2">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                    <a href="<?php echo $baseUrl; ?>/logout" class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <!-- User Stats -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">My Reviews</h5>
                            <h2 class="card-text"><?php echo $userReviews ?? 0; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Favorite Genre</h5>
                            <h2 class="card-text"><?php echo $favoriteGenre ?? 'N/A'; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="<?php echo $baseUrl; ?>/" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-gamepad"></i><br>
                                        Browse Games
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="<?php echo $baseUrl; ?>/profile" class="btn btn-outline-info w-100">
                                        <i class="fas fa-user-edit"></i><br>
                                        Edit Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Reviewed Games -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <h5 class="mb-0">
                                <i class="fas fa-star"></i> My Reviewed Games
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($reviewedGames)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> You haven't reviewed any games yet. <a href="<?php echo $baseUrl; ?>/" class="alert-link">Start exploring and reviewing games!</a>
                                </div>
                            <?php else: ?>
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                    <?php foreach ($reviewedGames as $game): ?>
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
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="badge bg-success"><?php echo number_format($game['overall_score'] ?? 0, 1); ?>/10</span>
                                                                <small class="text-muted">Reviewed</small>
                                                            </div>
                                                            <?php if (!empty($game['comment'])): ?>
                                                                <div class="review-preview">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-quote-left me-1"></i>
                                                                        <?php echo htmlspecialchars(substr($game['comment'], 0, 60)) . (strlen($game['comment']) > 60 ? '...' : ''); ?>
                                                                    </small>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="<?php echo $baseUrl; ?>/my-reviews" class="btn btn-outline-success">
                                        <i class="fas fa-list"></i> View All My Reviews
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
