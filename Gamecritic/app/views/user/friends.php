<?php
$title = 'My Friends | GameCritic';
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">My Friends</h2>

            <?php if (empty($friends)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">You don't have any friends yet.</p>
                        <a href="<?php echo $baseUrl; ?>/friend/requests" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Find Friends
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($friends as $friend): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <?php if (!empty($friend['profile_picture'])): ?>
                                        <img src="<?php echo $baseUrl . htmlspecialchars($friend['profile_picture']); ?>" 
                                             class="rounded-circle mb-3" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-user fa-3x text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <h5 class="card-title"><?php echo htmlspecialchars($friend['username']); ?></h5>
                                    <p class="text-muted small"><?php echo htmlspecialchars($friend['email']); ?></p>
                                    <a href="<?php echo $baseUrl; ?>/chat?user_id=<?php echo $friend['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-comments"></i> Message
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
