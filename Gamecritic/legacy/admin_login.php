<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login | GameCritic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<!-- Logo -->
<header class="login-header">
  <div class="container d-flex align-items-center py-3">
    <img src="images/logo.png" alt="GameCritic Logo" class="logo" />
    <h1 class="ms-3 text-white">GameCritic Admin</h1>
  </div>
</header>

<!-- Admin Login Box -->
<div class="container-fluid login-page">
  <div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5">
      <div class="card login-card shadow-lg">
        <div class="card-body p-4">
            <?php
            session_start();
            if (!empty($_SESSION['error'])) {
                echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error']).'</div>';
                unset($_SESSION['error']);
            }
            ?>

          <h2 class="text-center mb-4">Admin Login</h2>
          
          <form method="POST" action="admin_login_process.php">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                required
                placeholder="admin@example.com"
              />
            </div>
            
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                required
                placeholder="Enter your password"
              />
            </div>
            
            <button type="submit" class="btn btn-danger w-100">Log In</button>
          </form>
          
          <div class="text-center mt-3">
            <a href="landing.php" class="text-light">&larr; Back to Home</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
