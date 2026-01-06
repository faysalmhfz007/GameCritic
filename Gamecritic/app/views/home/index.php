<?php
$title = 'GameCritic – Discover Games';
?>

<!-- Featured Games Carousel -->
<div id="featuredGames" class="carousel slide mt-3" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="<?php echo $baseUrl; ?>/images/godofwar.jpg" class="d-block w-100 carousel-img" alt="God of War">
            <div class="carousel-caption d-none d-md-block">
                <h5>God of War: Ragnarök</h5>
                <p>Rated 9.8 – Epic Norse adventure continues</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?php echo $baseUrl; ?>/images/eldenring.jpg" class="d-block w-100 carousel-img" alt="Elden Ring">
            <div class="carousel-caption d-none d-md-block">
                <h5>Elden Ring</h5>
                <p>FromSoftware's masterpiece of exploration</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?php echo $baseUrl; ?>/images/zelda.jpg" class="d-block w-100 carousel-img" alt="Zelda">
            <div class="carousel-caption d-none d-md-block">
                <h5>Zelda: Tears of the Kingdom</h5>
                <p>A magical return to Hyrule</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#featuredGames" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#featuredGames" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Top Rated Games -->
<?php if (!empty($topRatedGames)): ?>
<div class="container mt-5">
    <h2 class="mb-4">Top Rated Games</h2>
    <div class="row row-cols-1 row-cols-md-4 g-3">
        <?php foreach ($topRatedGames as $game): ?>
        <div class="col">
            <a href="<?php echo $baseUrl; ?>/game/<?php echo (int)$game['id']; ?>" class="text-decoration-none">
                <div class="card h-100 game-card">
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
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title text-white"><?php echo htmlspecialchars($game['title']); ?></h5>
                        <p class="meta mb-2">
                            <span class="badge bg-primary me-1"><?php echo htmlspecialchars($game['genre']); ?></span>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating-badge">
                                <span class="badge bg-success fs-6"><?php echo number_format($game['overall_score'], 1); ?>/10</span>
                            </div>
                            <small class="text-muted">
                                <?php echo (int)$game['pos_count']; ?> 👍 <?php echo (int)$game['neg_count']; ?> 👎
                            </small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Things You May Like -->
<div class="container mt-5">
    <h2 class="mb-4">Things You May Like</h2>
    <?php if ($currentUser): ?>
        <?php if (!empty($recommendedGames)): ?>
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
                                            <span class="badge bg-success"><?php echo number_format($game['overall_score'] ?? 0, 1); ?>/10</span>
                                            <small class="text-muted"><?php echo $game['release_year'] ?? 'N/A'; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="#all-games" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-gamepad me-2"></i>See More Games
                </a>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted mb-3">Start Exploring Games!</h4>
                        <p class="text-muted mb-4">Review some games to get personalized recommendations based on your preferences.</p>
                        <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary btn-lg">
                            <i class="fas fa-gamepad me-2"></i>Browse All Games
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h4 class="text-primary mb-3">Join GameCritic!</h4>
                    <p class="text-muted mb-4">Sign up to get personalized game recommendations based on your reviews and preferences.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?php echo $baseUrl; ?>/register" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Sign Up
                        </a>
                        <a href="<?php echo $baseUrl; ?>/login" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Game Polls Section - Grand Design -->
<?php if (!empty($pollGames)): ?>
<div class="container-fluid mt-5 mb-5 poll-section-grand" style="background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460); padding: 60px 0; border-radius: 20px; position: relative; overflow: hidden;">
    <!-- Background Pattern -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"25\" cy=\"25\" r=\"1\" fill=\"%23ffffff\" opacity=\"0.1\"/><circle cx=\"75\" cy=\"75\" r=\"1\" fill=\"%23ffffff\" opacity=\"0.1\"/><circle cx=\"50\" cy=\"10\" r=\"0.5\" fill=\"%23ffffff\" opacity=\"0.1\"/><circle cx=\"10\" cy=\"60\" r=\"0.5\" fill=\"%23ffffff\" opacity=\"0.1\"/><circle cx=\"90\" cy=\"40\" r=\"0.5\" fill=\"%23ffffff\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>'); opacity: 0.3;"></div>
    
    <div class="container position-relative">
        <!-- Section Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-white mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                <i class="fas fa-vote-yea text-warning me-3"></i>
                Community Polls
            </h1>
            <p class="lead text-light">Vote for the most anticipated upcoming games!</p>
            <div class="d-flex justify-content-center">
                <div class="badge bg-warning text-dark fs-6 px-4 py-2">
                    <i class="fas fa-fire me-2"></i>Hot Topic
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Poll Games - Large Cards -->
            <div class="col-lg-8">
                <div class="row g-4">
                    <?php foreach ($pollGames as $index => $pollGame): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-lg poll-card-grand" style="background: linear-gradient(145deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05)); backdrop-filter: blur(10px); border-radius: 20px; transition: all 0.3s ease; transform: perspective(1000px) rotateX(0deg);">
                                <div class="card-body p-0 position-relative overflow-hidden" style="border-radius: 20px;">
                                    <!-- Game Image -->
                                    <div class="position-relative" style="height: 250px; overflow: hidden; border-radius: 20px 20px 0 0;">
                                        <img src="<?php echo $baseUrl; ?>/images/<?php echo htmlspecialchars($pollGame['game_picture'] ?? 'default.jpg'); ?>" 
                                             class="w-100 h-100" alt="<?php echo htmlspecialchars($pollGame['game_name']); ?>" 
                                             style="object-fit: cover; transition: transform 0.3s ease;">
                                        <!-- Overlay Gradient -->
                                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 60%; background: linear-gradient(transparent, rgba(0,0,0,0.8));"></div>
                                        
                                        <!-- Vote Count Badge -->
                                        <div class="position-absolute top-3 end-3">
                                            <span class="badge bg-warning text-dark fs-5 px-3 py-2 vote-count-badge" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.3);">
                                                <i class="fas fa-vote-yea me-1"></i><?php echo (int)$pollGame['votes']; ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Game Name -->
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3">
                                            <h4 class="text-white fw-bold mb-0" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                                                <?php echo htmlspecialchars($pollGame['game_name']); ?>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Area -->
                                    <div class="p-4 text-center">
                                        <?php if ($currentUser && !$hasVoted): ?>
                                            <form method="POST" action="<?php echo $baseUrl; ?>/poll/vote">
                                                <input type="hidden" name="game_name" value="<?php echo htmlspecialchars($pollGame['game_name']); ?>">
                                                <button type="submit" class="btn btn-warning btn-lg px-4 py-2 fw-bold poll-vote-button" style="border-radius: 25px; box-shadow: 0 4px 15px rgba(255,193,7,0.4); transition: all 0.3s ease;">
                                                    <i class="fas fa-thumbs-up me-2"></i>Vote Now
                                                </button>
                                            </form>
                                        <?php elseif ($currentUser && $hasVoted): ?>
                                            <div class="text-success">
                                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                                <div class="fw-bold">Voted!</div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-light">
                                                <i class="fas fa-sign-in-alt fa-2x mb-2"></i>
                                                <div>Login to vote</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Most Anticipated Game - Hero Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg h-100 most-anticipated-hero" style="background: linear-gradient(145deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05)); backdrop-filter: blur(15px); border-radius: 25px; overflow: hidden;">
                    <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #ffd700, #ffed4e); border: none;">
                        <h3 class="mb-0 text-dark fw-bold">
                            <i class="fas fa-crown me-2"></i>Most Anticipated
                        </h3>
                    </div>
                    <div class="card-body text-center p-4">
                        <?php if ($mostAnticipatedGame): ?>
                            <div class="position-relative mb-4">
                                <img src="<?php echo $baseUrl; ?>/images/<?php echo htmlspecialchars($mostAnticipatedGame['game_picture'] ?? 'default.jpg'); ?>" 
                                     class="img-fluid rounded-3 shadow" alt="<?php echo htmlspecialchars($mostAnticipatedGame['game_name']); ?>" 
                                     style="max-height: 300px; object-fit: cover; border: 3px solid #ffd700;">
                                
                                <!-- Winner Badge -->
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <div class="badge bg-warning text-dark fs-4 px-3 py-2 winner-badge" style="border-radius: 20px; box-shadow: 0 4px 15px rgba(255,215,0,0.5);">
                                        <i class="fas fa-trophy me-1"></i>Winner
                                    </div>
                                </div>
                            </div>
                            
                            <h2 class="text-white fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                <?php echo htmlspecialchars($mostAnticipatedGame['game_name']); ?>
                            </h2>
                            
                            <div class="vote-stats">
                                <div class="badge bg-success fs-3 px-4 py-3 mb-3" style="border-radius: 20px; box-shadow: 0 4px 15px rgba(40,167,69,0.4);">
                                    <i class="fas fa-fire me-2"></i><?php echo $mostAnticipatedGame['votes']; ?> Votes
                                </div>
                                
                                <div class="progress mb-3" style="height: 15px; border-radius: 10px; background: rgba(255,255,255,0.2);">
                                    <div class="progress-bar bg-warning" style="width: 100%; border-radius: 10px; box-shadow: 0 2px 10px rgba(255,193,7,0.5);"></div>
                                </div>
                                
                                <div class="text-light">
                                    <i class="fas fa-star me-1"></i>Community Favorite
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-light">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <h5>No poll data available yet.</h5>
                                <p>Be the first to vote!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Decoration -->
        <div class="text-center mt-5">
            <div class="d-flex justify-content-center align-items-center">
                <div style="width: 100px; height: 2px; background: linear-gradient(90deg, transparent, #ffd700, transparent);"></div>
                <i class="fas fa-gem text-warning mx-3 fa-lg"></i>
                <div style="width: 100px; height: 2px; background: linear-gradient(90deg, transparent, #ffd700, transparent);"></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- All Games -->
<div id="all-games" class="container mt-5">
    <h2 class="mb-4">All Games</h2>
    
    <!-- Search Results Info -->
    <?php if (!empty($searchQuery)): ?>
        <div class="alert alert-info">
            Search results for: "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
            <a href="/" class="float-end">Clear Search</a>
        </div>
    <?php endif; ?>
    
    <!-- Filter Info -->
    <?php if (isset($filterGenre) || isset($filterPlatform)): ?>
        <div class="alert alert-info">
            Filtered by: 
            <?php if (isset($filterGenre)): ?>
                <strong>Genre: <?php echo htmlspecialchars($filterGenre); ?></strong>
            <?php endif; ?>
            <?php if (isset($filterPlatform)): ?>
                <strong>Platform: <?php echo htmlspecialchars($filterPlatform); ?></strong>
            <?php endif; ?>
            <a href="/" class="float-end">Clear Filters</a>
        </div>
    <?php endif; ?>
    
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
            <div class="col">
                <a href="<?php echo $baseUrl; ?>/game/<?php echo (int)$game['id']; ?>" class="text-decoration-none">
                    <div class="card h-100 game-card">
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
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title text-white"><?php echo htmlspecialchars($game['title']); ?></h5>
                            <p class="meta mb-2">
                                <span class="badge bg-primary me-1"><?php echo htmlspecialchars($game['genre']); ?></span>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
                            </p>
                            <p class="card-text text-truncate"><?php echo htmlspecialchars($game['description']); ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <h4>No games found</h4>
                    <p>Try adjusting your search criteria or browse all games.</p>
                    <a href="/" class="btn btn-primary">View All Games</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>



