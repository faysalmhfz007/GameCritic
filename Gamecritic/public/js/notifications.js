// Notifications system
(function() {
    const baseUrl = window.__BASE_URL__ || '';
    const currentUserId = window.__CURRENT_USER_ID__ || null;

    if (!currentUserId) {
        return; // User not logged in
    }

    // Load notifications
    function loadNotifications() {
        fetch(`${baseUrl}/notification/get?unread_only=1`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.notifications.length);
                    updateNotificationsDropdown(data.notifications);
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
    }

    // Update notification badge
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Also fetch unread count
        fetch(`${baseUrl}/notification/get-unread-count`)
            .then(response => response.json())
            .then(data => {
                if (data.success && badge) {
                    const unreadCount = data.count || 0;
                    if (unreadCount > 0) {
                        badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error loading notification count:', error));
    }

    // Update notifications dropdown
    function updateNotificationsDropdown(notifications) {
        const list = document.getElementById('notificationsList');
        const noNotifications = document.getElementById('noNotifications');
        
        if (!list) return;

        // Clear existing items (except header)
        const header = list.querySelector('.dropdown-header').parentElement;
        const divider = list.querySelector('.dropdown-divider');
        list.innerHTML = '';
        list.appendChild(header);
        list.appendChild(divider);

        if (notifications.length === 0) {
            const li = document.createElement('li');
            li.className = 'px-3 py-2 text-center text-muted';
            li.id = 'noNotifications';
            li.textContent = 'No notifications';
            list.appendChild(li);
            return;
        }

        notifications.forEach(notification => {
            const li = document.createElement('li');
            li.className = 'notification-item';
            
            const date = new Date(notification.created_at);
            const timeAgo = getTimeAgo(date);
            
            li.innerHTML = `
                <a class="dropdown-item ${notification.is_read == 0 ? 'fw-bold' : ''}" href="#" data-notification-id="${notification.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="small">${escapeHtml(notification.message)}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${timeAgo}</div>
                        </div>
                        <button class="btn btn-sm btn-link text-danger p-0 ms-2 delete-notification-btn" data-notification-id="${notification.id}" title="Delete">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </a>
            `;
            
            list.appendChild(li);
        });

        // Add mark all as read button if there are unread notifications
        if (notifications.some(n => n.is_read == 0)) {
            const li = document.createElement('li');
            li.innerHTML = '<hr class="dropdown-divider">';
            list.appendChild(li);
            
            const markAllLi = document.createElement('li');
            markAllLi.innerHTML = '<a class="dropdown-item text-center text-primary" href="#" id="markAllReadBtn"><small>Mark all as read</small></a>';
            list.appendChild(markAllLi);
        }

        // Add event listeners
        list.querySelectorAll('.notification-item .dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (!e.target.closest('.delete-notification-btn')) {
                    markNotificationAsRead(this.dataset.notificationId);
                }
            });
        });

        list.querySelectorAll('.delete-notification-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                deleteNotification(this.dataset.notificationId);
            });
        });

        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                markAllNotificationsAsRead();
            });
        }
    }

    // Mark notification as read
    function markNotificationAsRead(notificationId) {
        const formData = new FormData();
        formData.append('notification_id', notificationId);

        fetch(`${baseUrl}/notification/mark-read`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Reload notifications
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    // Mark all notifications as read
    function markAllNotificationsAsRead() {
        fetch(`${baseUrl}/notification/mark-all-read`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Reload notifications
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }

    // Delete notification
    function deleteNotification(notificationId) {
        const formData = new FormData();
        formData.append('notification_id', notificationId);

        fetch(`${baseUrl}/notification/delete`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Reload notifications
            }
        })
        .catch(error => console.error('Error deleting notification:', error));
    }

    // Get time ago string
    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return 'just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        const days = Math.floor(hours / 24);
        if (days < 7) return `${days} day${days > 1 ? 's' : ''} ago`;
        return date.toLocaleDateString();
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Load notifications on page load
    loadNotifications();

    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
})();
