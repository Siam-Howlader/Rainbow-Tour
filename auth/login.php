<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>

<body class="d-flex align-items-center justify-content-center vh-100 login-cover" style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)),
                url('../images/destinations/maldives.jpg') center/cover no-repeat;">
    <div class="card text-white bg-info p-4 shadow-lg" style="max-width: 400px;">
        <h2 class="text-center fw-bold">Sign In</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="login-process.php" method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text bg-white text-info border-0"><i class="fas fa-user"></i></span>
                <input type="email" class="form-control border-0" name="email" placeholder="Email" required>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text bg-white text-info border-0"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control border-0" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-key"></i> Login
            </button>

            <div class="text-center mt-2">
                <a href="#" class="text-danger text-decoration-none fw-bold">Forgot password?</a>
            </div>

            <div class="text-center mt-5">
                <div class="text-primary fw-bold">Not a user already?</div>
                <a href="signup.php" class="btn btn-outline-dark w-100">
                    <i class="fas fa-user-plus"></i> Sign Up
                </a>
            </div>
        </form>
    </div>
</body>
