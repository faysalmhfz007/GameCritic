<?php
$title = 'Friend Requests | GameCritic';
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Friend Requests</h2>

            <!-- Search Users -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Search Users</h5>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="userSearchInput" placeholder="Search by username or email...">
                        <button class="btn btn-primary" type="button" id="searchUsersBtn">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <div id="searchResults" class="mt-3"></div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pending Friend Requests</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingRequests)): ?>
                        <p class="text-muted">No pending friend requests.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($pendingRequests as $request): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($request['sender_profile_picture'])): ?>
                                            <img src="<?php echo $baseUrl . htmlspecialchars($request['sender_profile_picture']); ?>" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($request['sender_username']); ?></h6>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($request['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-success btn-sm accept-request-btn" data-request-id="<?php echo $request['id']; ?>">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                        <button class="btn btn-danger btn-sm reject-request-btn" data-request-id="<?php echo $request['id']; ?>">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sent Requests -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sent Friend Requests</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($sentRequests)): ?>
                        <p class="text-muted">No sent friend requests.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($sentRequests as $request): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($request['receiver_profile_picture'])): ?>
                                            <img src="<?php echo $baseUrl . htmlspecialchars($request['receiver_profile_picture']); ?>" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($request['receiver_username']); ?></h6>
                                            <small class="text-muted">Sent <?php echo date('M d, Y', strtotime($request['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    <button class="btn btn-warning btn-sm cancel-request-btn" data-request-id="<?php echo $request['id']; ?>">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const baseUrl = window.__BASE_URL__ || '';

// Search users
document.getElementById('searchUsersBtn').addEventListener('click', function() {
    const searchTerm = document.getElementById('userSearchInput').value.trim();
    if (!searchTerm) {
        alert('Please enter a search term');
        return;
    }

    fetch(`${baseUrl}/friend/search-users?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySearchResults(data.users);
            }
        })
        .catch(error => console.error('Error:', error));
});

function displaySearchResults(users) {
    const resultsDiv = document.getElementById('searchResults');
    if (users.length === 0) {
        resultsDiv.innerHTML = '<p class="text-muted">No users found.</p>';
        return;
    }

    let html = '<div class="list-group">';
    users.forEach(user => {
        const profilePic = user.profile_picture 
            ? `<img src="${baseUrl}${user.profile_picture}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">`
            : `<div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;"><i class="fas fa-user text-white"></i></div>`;
        
        let actionBtn = '';
        if (user.is_friend) {
            actionBtn = '<span class="badge bg-success">Friends</span>';
        } else if (user.has_pending_request) {
            actionBtn = '<span class="badge bg-warning">Request Sent</span>';
        } else {
            actionBtn = `<button class="btn btn-primary btn-sm send-request-btn" data-user-id="${user.id}"><i class="fas fa-user-plus"></i> Send Request</button>`;
        }

        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    ${profilePic}
                    <div>
                        <h6 class="mb-0">${user.username}</h6>
                        <small class="text-muted">${user.email}</small>
                    </div>
                </div>
                ${actionBtn}
            </div>
        `;
    });
    html += '</div>';
    resultsDiv.innerHTML = html;

    // Add event listeners for send request buttons
    document.querySelectorAll('.send-request-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            sendFriendRequest(this.dataset.userId);
        });
    });
}

// Send friend request
function sendFriendRequest(receiverId) {
    const formData = new FormData();
    formData.append('receiver_id', receiverId);

    fetch(`${baseUrl}/friend/send-request`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Friend request sent!');
            location.reload();
        } else {
            alert(data.message || 'Failed to send friend request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Accept friend request
document.querySelectorAll('.accept-request-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const requestId = this.dataset.requestId;
        const formData = new FormData();
        formData.append('request_id', requestId);

        fetch(`${baseUrl}/friend/accept-request`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Friend request accepted!');
                location.reload();
            } else {
                alert(data.message || 'Failed to accept friend request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
});

// Reject friend request
document.querySelectorAll('.reject-request-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to reject this friend request?')) {
            return;
        }
        const requestId = this.dataset.requestId;
        const formData = new FormData();
        formData.append('request_id', requestId);

        fetch(`${baseUrl}/friend/reject-request`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to reject friend request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
});

// Cancel friend request
document.querySelectorAll('.cancel-request-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to cancel this friend request?')) {
            return;
        }
        const requestId = this.dataset.requestId;
        const formData = new FormData();
        formData.append('request_id', requestId);

        fetch(`${baseUrl}/friend/cancel-request`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to cancel friend request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
});
</script>
