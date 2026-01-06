<?php
$title = 'Manage Users | GameCritic';
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">👥 User Management</h1>
                <a href="<?php echo $baseUrl; ?>/admin/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Users with Reviews</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($users)): ?>
                        <div class="text-center p-4">
                            <p class="text-muted">No users with reviews found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Reviews</th>
                                        <th>Status</th>
                                        <th>Ban Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr class="<?php echo (isset($user['banned']) && $user['banned'] == 1) ? 'table-danger' : ''; ?>">
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $user['review_count'] ?? count($user['reviews'] ?? []); ?></span>
                                        </td>
                                        <td>
                                            <?php if (isset($user['banned']) && $user['banned'] == 1): ?>
                                                <span class="badge bg-danger">Banned</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($user['banned_reason'])): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($user['banned_reason']); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($user['banned']) && $user['banned'] == 1): ?>
                                                <button class="btn btn-success btn-sm unban-user-btn" data-user-id="<?php echo $user['id']; ?>">
                                                    <i class="fas fa-unlock"></i> Unban
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-danger btn-sm ban-user-btn" data-user-id="<?php echo $user['id']; ?>">
                                                    <i class="fas fa-ban"></i> Ban
                                                </button>
                                            <?php endif; ?>
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

<!-- Ban User Modal -->
<div class="modal fade" id="banUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Ban User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="banUserForm">
                <div class="modal-body">
                    <input type="hidden" id="banUserId" name="user_id">
                    <div class="mb-3">
                        <label for="banReason" class="form-label">Ban Reason</label>
                        <textarea class="form-control" id="banReason" name="reason" rows="3" required>Violation of community guidelines</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Ban User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const baseUrl = window.__BASE_URL__ || '';

// Ban user
document.querySelectorAll('.ban-user-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const userId = this.dataset.userId;
        document.getElementById('banUserId').value = userId;
        new bootstrap.Modal(document.getElementById('banUserModal')).show();
    });
});

// Unban user
document.querySelectorAll('.unban-user-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to unban this user?')) {
            return;
        }
        const userId = this.dataset.userId;
        const formData = new FormData();
        formData.append('user_id', userId);

        fetch(`${baseUrl}/admin/unban-user`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User unbanned successfully');
                location.reload();
            } else {
                alert(data.message || 'Failed to unban user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
});

// Submit ban form
document.getElementById('banUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(`${baseUrl}/admin/ban-user`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User banned successfully');
            location.reload();
        } else {
            alert(data.message || 'Failed to ban user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});
</script>
