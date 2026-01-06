<?php
$title = 'Chat | GameCritic';
?>
<div class="container-fluid mt-4">
    <div class="row" style="height: calc(100vh - 200px);">
        <!-- Conversations List -->
        <div class="col-md-4 border-end">
            <div class="d-flex flex-column h-100">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">Messages</h5>
                </div>
                <div class="flex-grow-1 overflow-auto" id="conversationsList">
                    <!-- Conversations will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 d-flex flex-column h-100">
            <?php if ($otherUser): ?>
                <!-- Chat Header -->
                <div class="p-3 border-bottom d-flex align-items-center">
                    <?php if (!empty($otherUser['profile_picture'])): ?>
                        <img src="<?php echo $baseUrl . htmlspecialchars($otherUser['profile_picture']); ?>" 
                             class="rounded-circle me-3" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h6 class="mb-0"><?php echo htmlspecialchars($otherUser['username']); ?></h6>
                        <small class="text-muted"><?php echo htmlspecialchars($otherUser['email']); ?></small>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-grow-1 overflow-auto p-3" id="messagesArea" style="background-color: #f8f9fa;">
                    <?php foreach ($messages as $message): ?>
                        <div class="mb-3 d-flex <?php echo $message['sender_id'] == $currentUser['id'] ? 'justify-content-end' : 'justify-content-start'; ?>">
                            <div class="card" style="max-width: 70%;">
                                <div class="card-body p-2">
                                    <p class="mb-1"><?php echo htmlspecialchars($message['message']); ?></p>
                                    <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Message Input -->
                <div class="p-3 border-top">
                    <form id="messageForm">
                        <input type="hidden" id="receiverId" value="<?php echo $otherUser['id']; ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" id="messageInput" placeholder="Type a message...">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center text-muted">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>Select a conversation to start chatting</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const baseUrl = window.__BASE_URL__ || '';
const currentUserId = <?php echo $currentUser['id']; ?>;
const otherUserId = <?php echo $otherUser['id'] ?? 'null'; ?>;

// Load conversations
function loadConversations() {
    fetch(`${baseUrl}/message/get-conversations`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayConversations(data.conversations);
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayConversations(conversations) {
    const listDiv = document.getElementById('conversationsList');
    if (conversations.length === 0) {
        listDiv.innerHTML = '<div class="p-3 text-center text-muted">No conversations yet</div>';
        return;
    }

    let html = '';
    conversations.forEach(conv => {
        const profilePic = conv.profile_picture 
            ? `<img src="${baseUrl}${conv.profile_picture}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">`
            : `<div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;"><i class="fas fa-user text-white"></i></div>`;
        
        const unreadBadge = conv.unread_count > 0 
            ? `<span class="badge bg-primary rounded-pill">${conv.unread_count}</span>`
            : '';
        
        const isActive = otherUserId && conv.id == otherUserId ? 'bg-light' : '';
        
        html += `
            <a href="${baseUrl}/chat?user_id=${conv.id}" class="list-group-item list-group-item-action ${isActive}">
                <div class="d-flex align-items-center">
                    ${profilePic}
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">${conv.username}</h6>
                            ${unreadBadge}
                        </div>
                        <small class="text-muted">${conv.last_message || 'No messages yet'}</small>
                    </div>
                </div>
            </a>
        `;
    });
    listDiv.innerHTML = html;
}

// Send message
if (document.getElementById('messageForm')) {
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const receiverId = document.getElementById('receiverId').value;
        const message = document.getElementById('messageInput').value.trim();
        
        if (!message) {
            return;
        }

        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('message', message);

        fetch(`${baseUrl}/message/send`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('messageInput').value = '';
                location.reload();
            } else {
                alert(data.message || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
}

// Load conversations on page load
loadConversations();

// Auto-refresh messages every 5 seconds
if (otherUserId) {
    setInterval(function() {
        fetch(`${baseUrl}/message/get-conversation?user_id=${otherUserId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateMessages(data.messages);
                }
            })
            .catch(error => console.error('Error:', error));
    }, 5000);
}

function updateMessages(messages) {
    const messagesArea = document.getElementById('messagesArea');
    messagesArea.innerHTML = '';
    
    messages.forEach(message => {
        const isOwn = message.sender_id == currentUserId;
        const alignClass = isOwn ? 'justify-content-end' : 'justify-content-start';
        messagesArea.innerHTML += `
            <div class="mb-3 d-flex ${alignClass}">
                <div class="card" style="max-width: 70%;">
                    <div class="card-body p-2">
                        <p class="mb-1">${message.message}</p>
                        <small class="text-muted">${new Date(message.created_at).toLocaleString()}</small>
                    </div>
                </div>
            </div>
        `;
    });
    messagesArea.scrollTop = messagesArea.scrollHeight;
}
</script>
