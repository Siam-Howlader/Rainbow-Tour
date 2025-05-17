<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>

<?php
    if (isset($_SESSION['error'])) {
        $error_message = $_SESSION['error'];
        unset($_SESSION['error']);
    }
?>

<body class="d-flex align-items-center justify-content-center vh-100 signup-cover" style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), 
                url('<?= BASE_URL ?>images/destinations/tanguar-haor-1.jpeg') center/cover no-repeat;">
    <div class="card text-white bg-info p-4 shadow-lg" style="max-width: 600px;">
        <h2 class="text-center fw-bold">Sign Up</h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="signup-process.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control border-0" name="name" placeholder="Full Name" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control border-0" name="nid" placeholder="National ID (NID)" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control border-0" name="email" placeholder="Email" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" class="form-control border-0" name="dob" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-phone"></i></span>
                        <input type="text" class="form-control border-0" name="phone" placeholder="Phone Number" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-map-marker-alt"></i></span>
                        <textarea class="form-control border-0" name="address" placeholder="Address" rows="2" required></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control border-0" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-info border-0"><i class="fas fa-check-circle"></i></span>
                        <input type="password" class="form-control border-0" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_staff" id="is_staff">
                        <label class="form-check-label" for="is_staff">
                            Sign up as Staff
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus"></i> Sign Up
                    </button>
                </div>

                <div class="col-12 text-center mt-3">
                    <div class="text-primary fw-bold">Already have an account?</div>
                    <a href="login.php" class="btn btn-outline-dark w-100">
                        <i class="fas fa-key"></i> Login
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>
