<?php
$title = 'Sign Up | GameCritic';
?>

<div class="container-fluid login-page">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card login-card shadow-lg">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Create Account</h2>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            $error = $_GET['error'];
                            switch ($error) {
                                case 'missing_fields':
                                    echo 'Please fill in all fields.';
                                    break;
                                case 'password_mismatch':
                                    echo 'Passwords do not match.';
                                    break;
                                case 'password_too_short':
                                    echo 'Password must be at least 6 characters long.';
                                    break;
                                case 'email_exists':
                                    echo 'An account with this email already exists.';
                                    break;
                                case 'creation_failed':
                                    echo 'Failed to create account. Please try again.';
                                    break;
                                default:
                                    echo 'An error occurred. Please try again.';
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo $baseUrl; ?>/signup">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">Sign Up</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?php echo $baseUrl; ?>/login" class="text-decoration-none">Already have an account? <strong>Log in</strong></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





