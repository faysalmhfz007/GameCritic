<?php
$title = htmlspecialchars($game['title']) . ' | GameCritic';

// Normalize cover image path
$coverImage = $game['cover_resolved'] ?? $game['cover_image'] ?? '/images/default.jpg';
if (strpos($coverImage, '/images/') === 0) {
    $coverImage = $baseUrl . $coverImage;
} elseif (strpos($coverImage, 'images/') === 0) {
    $coverImage = $baseUrl . '/' . $coverImage;
} elseif (strpos($coverImage, 'http') !== 0) {
    $coverImage = $baseUrl . '/images/' . $coverImage;
}
?>

<div class="container mt-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <img src="<?php echo htmlspecialchars($coverImage); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-3">Community Score</h5>
                    <?php
                      $pos = isset($game['pos_count']) ? (int)$game['pos_count'] : (int)$aggregates['positive'];
                      $neg = isset($game['neg_count']) ? (int)$game['neg_count'] : (int)$aggregates['negative'];
                      $overall10 = isset($game['overall_score']) && $game['overall_score'] !== null && $game['overall_score'] !== ''
                        ? (float)$game['overall_score']
                        : (float)$aggregates['overall10'];
                    ?>
                    <div class="display-6 fw-bold text-white"><?php echo number_format($overall10, 1); ?>/10</div>
                    <div class="text-muted small">Score based on community votes</div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h1 class="mb-3"><?php echo htmlspecialchars($game['title']); ?></h1>
            <p class="mb-2">
                <span class="badge bg-primary me-2"><?php echo htmlspecialchars($game['genre']); ?></span>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
            </p>
            <p class="text-muted mb-3">Released: <?php echo htmlspecialchars($game['release_year']); ?></p>
            <?php if ($currentUser): ?>
                <div class="mb-3">
                    <button class="btn btn-sm btn-outline-warning hide-game-btn" data-game-id="<?php echo $game['id']; ?>" data-is-hidden="<?php echo $isHidden ? '1' : '0'; ?>">
                        <i class="fas fa-<?php echo $isHidden ? 'eye' : 'eye-slash'; ?>"></i> 
                        <?php echo $isHidden ? 'Unhide Game' : 'Hide Game (Spoiler)'; ?>
                    </button>
                </div>
            <?php endif; ?>
            <div class="mb-4">
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($game['description'])); ?></p>
            </div>
            <?php if (!empty($game['review'])): ?>
            <div class="mb-4">
                <h5>Our Review</h5>
                <p class="fst-italic"><?php echo nl2br(htmlspecialchars($game['review'])); ?></p>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <h5>Your Reaction</h5>
                <?php if ($currentUser): ?>
                    <?php 
                    $userVoteUp = $userVote && $userVote['rating'] >= 0.5;
                    $userVoteDown = $userVote && $userVote['rating'] < 0.5;
                    ?>
                    <form method="POST" action="<?php echo $baseUrl; ?>/game/<?php echo (int)$game['id']; ?>/thumb" class="d-inline">
                        <input type="hidden" name="type" value="up">
                        <button class="btn <?php echo $userVoteUp ? 'btn-success active' : 'btn-outline-success'; ?> me-2" type="submit">
                            👍 <?php echo $userVoteUp ? 'Voted Up' : 'Thumbs Up'; ?>
                        </button>
                    </form>
                    <form method="POST" action="<?php echo $baseUrl; ?>/game/<?php echo (int)$game['id']; ?>/thumb" class="d-inline">
                        <input type="hidden" name="type" value="down">
                        <button class="btn <?php echo $userVoteDown ? 'btn-danger active' : 'btn-outline-danger'; ?>" type="submit">
                            👎 <?php echo $userVoteDown ? 'Voted Down' : 'Thumbs Down'; ?>
                        </button>
                    </form>
                    <?php if ($userVote): ?>
                        <div class="mt-2">
                            <small class="text-muted">You can change your vote by clicking again</small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-muted">
                        <a href="<?php echo $baseUrl; ?>/login">Login</a> to vote on this game
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <h5>Write a Comment</h5>
                <form method="POST" action="<?php echo $baseUrl; ?>/game/<?php echo (int)$game['id']; ?>/review">
                    <div class="mb-3">
                        <textarea class="form-control" name="comment" rows="3" placeholder="Share your thoughts..." required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit Comment</button>
                </form>
            </div>

            <div class="mb-4">
                <h5>Comments</h5>
                <?php if (empty($reviews)): ?>
                    <div class="text-muted">No comments yet. Be the first!</div>
                <?php else: ?>
                    <div id="comments-container">
                        <?php foreach ($reviews as $rev): ?>
                            <?php if (empty($rev['comment'])) continue; ?>
                            <div class="comment-item mb-3 p-3 border rounded" style="background: rgba(255,255,255,0.05);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <strong class="text-white me-2"><?php echo htmlspecialchars($rev['username'] ?? 'User'); ?></strong>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($rev['created_at']); ?></span>
                                    </div>
                                    <div class="comment-vote-score">
                                        <span class="badge <?php echo ($rev['vote_score'] ?? 0) >= 0 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ($rev['vote_score'] ?? 0) > 0 ? '+' : ''; ?><?php echo $rev['vote_score'] ?? 0; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="comment-content mb-3">
                                    <?php echo nl2br(htmlspecialchars($rev['comment'])); ?>
                                </div>
                                
                                <div class="comment-actions d-flex align-items-center">
                                    <div class="vote-buttons me-3">
                                        <button class="btn btn-sm btn-outline-success vote-btn" 
                                                data-comment-id="<?php echo $rev['id']; ?>" 
                                                data-vote-type="up"
                                                data-game-id="<?php echo $game['id']; ?>">
                                            <i class="fas fa-arrow-up"></i> <?php echo $rev['upvotes'] ?? 0; ?>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger vote-btn" 
                                                data-comment-id="<?php echo $rev['id']; ?>" 
                                                data-vote-type="down"
                                                data-game-id="<?php echo $game['id']; ?>">
                                            <i class="fas fa-arrow-down"></i> <?php echo $rev['downvotes'] ?? 0; ?>
                                        </button>
                                    </div>
                                    <?php if (isset($rev['rating'])): ?>
                                        <small class="text-muted">Rating: <?php echo number_format((float)$rev['rating'] * 10, 1); ?>/10</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <a href="<?php echo $baseUrl; ?>/" class="btn btn-outline-light">Back to Home</a>
        </div>
    </div>
</div>

<script>
const baseUrl = window.__BASE_URL__ || '';

// Hide/Unhide game functionality
document.querySelectorAll('.hide-game-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const gameId = this.dataset.gameId;
        const isHidden = this.dataset.isHidden === '1';
        const formData = new FormData();
        formData.append('game_id', gameId);

        fetch(`${baseUrl}/game/${isHidden ? 'unhide' : 'hide'}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to ' + (isHidden ? 'unhide' : 'hide') + ' game');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle comment voting
    document.querySelectorAll('.vote-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const voteType = this.dataset.voteType;
            const gameId = this.dataset.gameId;
            
            // Check if user is logged in
            <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Please login to vote on comments');
                return;
            <?php endif; ?>
            
            // Disable button during request
            this.disabled = true;
            
            // Send vote request
            fetch(`<?php echo $baseUrl; ?>/game/${gameId}/comment/${commentId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `vote_type=${voteType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the comments container with new data
                    updateComments(data.comments);
                } else {
                    alert(data.message || 'Error voting on comment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error voting on comment');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
    
    function updateComments(comments) {
        const container = document.getElementById('comments-container');
        if (!container) return;
        
        // Clear existing comments
        container.innerHTML = '';
        
        // Add updated comments
        comments.forEach(comment => {
            if (!comment.comment) return;
            
            const commentHtml = `
                <div class="comment-item mb-3 p-3 border rounded" style="background: rgba(255,255,255,0.05);">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center">
                            <strong class="text-white me-2">${comment.username || 'User'}</strong>
                            <span class="badge bg-secondary">${comment.created_at}</span>
                        </div>
                        <div class="comment-vote-score">
                            <span class="badge ${(comment.vote_score || 0) >= 0 ? 'bg-success' : 'bg-danger'}">
                                ${(comment.vote_score || 0) > 0 ? '+' : ''}${comment.vote_score || 0}
                            </span>
                        </div>
                    </div>
                    
                    <div class="comment-content mb-3">
                        ${comment.comment.replace(/\n/g, '<br>')}
                    </div>
                    
                    <div class="comment-actions d-flex align-items-center">
                        <div class="vote-buttons me-3">
                            <button class="btn btn-sm btn-outline-success vote-btn" 
                                    data-comment-id="${comment.id}" 
                                    data-vote-type="up"
                                    data-game-id="${comment.game_id}">
                                <i class="fas fa-arrow-up"></i> ${comment.upvotes || 0}
                            </button>
                            <button class="btn btn-sm btn-outline-danger vote-btn" 
                                    data-comment-id="${comment.id}" 
                                    data-vote-type="down"
                                    data-game-id="${comment.game_id}">
                                <i class="fas fa-arrow-down"></i> ${comment.downvotes || 0}
                            </button>
                        </div>
                        ${comment.rating ? `<small class="text-muted">Rating: ${(comment.rating * 10).toFixed(1)}/10</small>` : ''}
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', commentHtml);
        });
        
        // Re-attach event listeners to new buttons
        document.querySelectorAll('.vote-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const voteType = this.dataset.voteType;
                const gameId = this.dataset.gameId;
                
                // Check if user is logged in
                <?php if (!isset($_SESSION['user_id'])): ?>
                    alert('Please login to vote on comments');
                    return;
                <?php endif; ?>
                
                // Disable button during request
                this.disabled = true;
                
                // Send vote request
                fetch(`<?php echo $baseUrl; ?>/game/${gameId}/comment/${commentId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `vote_type=${voteType}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the comments container with new data
                        updateComments(data.comments);
                    } else {
                        alert(data.message || 'Error voting on comment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error voting on comment');
                })
                .finally(() => {
                    this.disabled = false;
                });
            });
        });
    }
});
</script>

