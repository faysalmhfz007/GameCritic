<?php
// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Temporary debug fast-paths (safe to keep; only run when query present)
if (isset($_GET['__ping']) && $_GET['__ping'] === '1') {
    header('Content-Type: text/plain');
    echo 'front-ok';
    exit;
}

// Include the router (use absolute path for reliability)
require_once __DIR__ . '/../app/config/Router.php';

// Create router instance
$router = new Router();

// Define routes
$router->get('/', 'Home', 'index');
$router->get('/search', 'Home', 'search');
$router->get('/filter', 'Home', 'filter');
$router->get('/ping', 'Home', 'ping');
$router->get('/game/{id}', 'Game', 'show');
$router->post('/game/{id}/thumb', 'Game', 'thumb');
$router->post('/game/{id}/review', 'Game', 'review');
$router->post('/game/{gameId}/comment/{commentId}/vote', 'Game', 'voteComment');
$router->get('/debug', 'Home', 'ping'); // Debug route

// Auth routes
$router->get('/login', 'Auth', 'login');
$router->post('/login', 'Auth', 'loginProcess');
$router->get('/signup', 'Auth', 'signup');
$router->post('/signup', 'Auth', 'signupProcess');
$router->get('/logout', 'Auth', 'logout');

// User routes
$router->get('/dashboard', 'User', 'dashboard');
$router->get('/profile', 'User', 'profile');
$router->post('/profile', 'User', 'updateProfile');

// Admin routes
$router->get('/admin/dashboard', 'Admin', 'dashboard');
$router->get('/admin/add-game', 'Admin', 'addGame');
$router->post('/admin/add-game', 'Admin', 'addGame');
$router->get('/admin/edit-game/{id}', 'Admin', 'editGame');
$router->post('/admin/edit-game/{id}', 'Admin', 'editGame');
$router->post('/admin/delete-game/{id}', 'Admin', 'deleteGame');

// Poll routes
$router->get('/admin/polls', 'Admin', 'polls');
$router->post('/admin/create-poll', 'Admin', 'createPoll');
$router->post('/admin/clear-polls', 'Admin', 'clearPolls');
$router->post('/poll/vote', 'Poll', 'vote');

// Friend routes
$router->get('/friend/requests', 'Friend', 'requests');
$router->get('/friend/friends', 'Friend', 'friends');
$router->get('/friend/search-users', 'Friend', 'searchUsers');
$router->post('/friend/send-request', 'Friend', 'sendRequest');
$router->post('/friend/accept-request', 'Friend', 'acceptRequest');
$router->post('/friend/reject-request', 'Friend', 'rejectRequest');
$router->post('/friend/cancel-request', 'Friend', 'cancelRequest');

// Message routes
$router->get('/chat', 'Message', 'chat');
$router->post('/message/send', 'Message', 'send');
$router->get('/message/get-conversation', 'Message', 'getConversation');
$router->get('/message/get-conversations', 'Message', 'getConversations');
$router->get('/message/get-unread-count', 'Message', 'getUnreadCount');

// Notification routes
$router->get('/notification/get', 'Notification', 'getNotifications');
$router->get('/notification/get-unread-count', 'Notification', 'getUnreadCount');
$router->post('/notification/mark-read', 'Notification', 'markAsRead');
$router->post('/notification/mark-all-read', 'Notification', 'markAllAsRead');
$router->post('/notification/delete', 'Notification', 'delete');

// Admin user management routes
$router->get('/admin/users', 'AdminUser', 'listUsers');
$router->post('/admin/ban-user', 'AdminUser', 'banUser');
$router->post('/admin/unban-user', 'AdminUser', 'unbanUser');

// User game hide/spoiler routes
$router->post('/game/hide', 'UserGame', 'hideGame');
$router->post('/game/unhide', 'UserGame', 'unhideGame');

// Dispatch the request and output any returned content
$output = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
if (is_string($output)) {
    echo $output;
}
?>



