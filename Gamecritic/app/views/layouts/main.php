<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'GameCritic'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css?v=<?php echo time(); ?>">
    
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $baseUrl; ?>/">🎮 GameCritic</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/filter?genre=action">Action</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/filter?genre=rpg">RPG</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/filter?genre=strategy">Strategy</a></li>
                </ul>
                
                <form class="d-flex me-2" autocomplete="off">
                    <input id="searchInput" class="form-control me-2" type="search" placeholder="Search games...">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                    <div id="searchDropdown" class="list-group position-absolute bg-white text-dark"></div>
                </form>

                <?php if (isset($currentUser)): ?>
                    <div class="d-flex align-items-center me-3">
                        <!-- Notifications Dropdown -->
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;">
                                    0
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" id="notificationsList" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="px-3 py-2 text-center text-muted" id="noNotifications">No notifications</li>
                            </ul>
                        </div>

                        <!-- Chat Link -->
                        <a href="<?php echo $baseUrl; ?>/chat" class="btn btn-outline-light me-2" title="Messages">
                            <i class="fas fa-comments"></i>
                        </a>

                        <!-- Friends Link -->
                        <a href="<?php echo $baseUrl; ?>/friend/friends" class="btn btn-outline-light me-2" title="Friends">
                            <i class="fas fa-users"></i>
                        </a>

                        <span class="navbar-text text-light me-2">Hello, <?php echo htmlspecialchars($currentUser['username'] ?? $currentUser['name']); ?></span>
                        <a href="<?php echo $baseUrl; ?>/profile" class="text-decoration-none">
                            <?php if (!empty($currentUser['profile_picture'])): ?>
                                <img src="<?php echo $baseUrl . htmlspecialchars($currentUser['profile_picture']); ?>" 
                                     alt="Profile Picture" 
                                     class="rounded-circle" 
                                     style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px; border: 2px solid #fff;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>
                    <?php if ($currentUser['is_admin']): ?>
                        <a href="<?php echo $baseUrl; ?>/admin/dashboard" class="btn btn-warning me-2">Admin</a>
                    <?php else: ?>
                        <a href="<?php echo $baseUrl; ?>/dashboard" class="btn btn-primary me-2">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?php echo $baseUrl; ?>/logout" class="btn btn-outline-light">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $baseUrl; ?>/login" class="btn btn-danger me-2">Login</a>
                    <a href="<?php echo $baseUrl; ?>/signup" class="btn btn-outline-light">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 p-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <strong>GameCritic</strong> © 2025 | All rights reserved.
            </div>
            <div>
                <a href="/about" class="text-white me-3">About</a>
                <a href="/contact" class="text-white me-3">Contact</a>
                <a href="https://facebook.com" class="text-white me-2">
                    <img src="<?php echo $baseUrl; ?>/images/facebook.png" height="24" alt="Facebook">
                </a>
                <a href="https://youtube.com" class="text-white">
                    <img src="<?php echo $baseUrl; ?>/images/youtube.png" height="24" alt="YouTube">
                </a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
      window.__BASE_URL__ = '<?php echo $baseUrl; ?>';
      <?php if (isset($currentUser)): ?>
      window.__CURRENT_USER_ID__ = <?php echo $currentUser['id']; ?>;
      <?php endif; ?>
    </script>
    <script src="<?php echo $baseUrl; ?>/js/search.js"></script>
    <?php if (isset($currentUser)): ?>
    <script src="<?php echo $baseUrl; ?>/js/notifications.js"></script>
    <?php endif; ?>
    
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>


