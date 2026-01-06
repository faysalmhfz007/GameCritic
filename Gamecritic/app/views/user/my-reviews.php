<?php
$title = 'My Reviews | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">⭐ My Reviews</h1>
                <a href="<?php echo $baseUrl; ?>/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Your Game Reviews</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Reviews Yet</h5>
                            <p class="text-muted">You haven't reviewed any games yet. Start exploring and share your thoughts!</p>
                            <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary">
                                <i class="fas fa-gamepad"></i> Browse Games
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($reviews as $review): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($review['cover_image'] ?? '/images/default.jpg'); ?>" 
                                                 class="img-fluid rounded-start h-100" 
                                                 style="object-fit: cover; height: 150px;" 
                                                 alt="<?php echo htmlspecialchars($review['title']); ?>">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($review['title']); ?></h6>
                                                <div class="mb-2">
                                                    <span class="badge bg-primary"><?php echo htmlspecialchars($review['genre']); ?></span>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($review['platform']); ?></span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Your Rating:</strong>
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                        <?php endfor; ?>
                                                        <span class="ms-2"><?php echo $review['rating']; ?>/5</span>
                                                    </div>
                                                </div>
                                                <?php if (!empty($review['comment'])): ?>
                                                    <p class="card-text">
                                                        <small class="text-muted">"<?php echo htmlspecialchars(substr($review['comment'], 0, 100)) . (strlen($review['comment']) > 100 ? '...' : ''); ?>"</small>
                                                    </p>
                                                <?php endif; ?>
                                                <small class="text-muted">
                                                    Reviewed on <?php echo date('M j, Y', strtotime($review['review_date'])); ?>
                                                </small>
                                                <div class="mt-2">
                                                    <a href="<?php echo $baseUrl; ?>/game/<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        View Game
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



