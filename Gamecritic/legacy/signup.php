<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up | GameCritic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<!-- Logo -->
<header class="login-header">
  <div class="container d-flex align-items-center py-3">
    <img src="{{ asset('images/logo.png') }}" alt="GameCritic Logo" class="logo" />
    <h1 class="ms-3 text-white">GameCritic</h1>
  </div>
</header>

<!-- Signup Box -->
<div class="container-fluid login-page">
  <div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5">
      <div class="card login-card shadow-lg">
        <div class="card-body p-4">
          <h2 class="text-center mb-4">Create an Account</h2>

          <form method="POST" action="/register">
            <!-- @csrf -->

            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input
                type="text"
                class="form-control"
                id="username"
                name="username"
                required
                placeholder="Your username"
              />
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                required
                placeholder="you@example.com"
              />
            </div>

            <div class="mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <input
                type="tel"
                class="form-control"
                id="phone"
                name="phone"
                placeholder="+8801234567890"
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
                placeholder="Create a password"
              />
            </div>

            <button type="submit" class="btn btn-danger w-100">Sign Up</button>
          </form>

          <div class="text-center mt-3">
            <a href="login.html" class="text-light"
              >Already have an account? <strong>Login</strong></a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
