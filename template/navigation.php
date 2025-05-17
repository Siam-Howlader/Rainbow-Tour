<?php include ROOT_PATH . 'auth/manage-access.php'; ?>

<?php
    $redirect_url = $logged_in ? BASE_URL . 'auth/profile.php' : BASE_URL . 'auth/login.php';
?>

<div class="top-nav d-flex align-items-center justify-content-between w-100">
    <div class="basic-info d-flex gap-4 flex-wrap ps-3">
        <div class="top-nav-item d-flex">
            <div class="top-nav-item-icon text-info">
                <i class="fas fa-envelope"></i>
            </div>
            <a href="mailto:rashi2327@cseku.ac.bd" class="top-nav-item-text text-light ms-2">
                rashi2327@cseku.ac.bd
            </a>
        </div>

        <div class="top-nav-item d-flex">
            <div class="top-nav-item-icon text-info">
                <i class="fas fa-phone"></i>
            </div>
            <a href="tel:+880 1831-805575" class="top-nav-item-text text-light ms-2">
                +880 1831-805575
            </a>
        </div>

        <div class="top-nav-item d-flex">
            <div class="top-nav-item-icon text-info">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <a href="https://www.google.com/maps/place/Khulna/" class="top-nav-item-text text-light ms-2">
                Khulna, Bangladesh
            </a>
        </div>
    </div>

    <div class="social-info d-flex ms-auto me-5">
        <a href="#" class="top-nav-item d-flex justify-content-center align-items-center p-2">
            <div class="top-nav-item-icon text-light">
                <i class="fab fa-facebook-f"></i>
            </div>
        </a>

        <a href="#" class="top-nav-item d-flex justify-content-center align-items-center p-2">
            <div class="top-nav-item-icon text-light">
                <i class="fab fa-whatsapp"></i>
            </div>
        </a>

        <a href="#" class="top-nav-item d-flex justify-content-center align-items-center p-2">
            <div class="top-nav-item-icon text-light">
                <i class="fab fa-instagram"></i>
            </div>
        </a>
    </div>

    <a href="<?php echo $redirect_url; ?>" class="text-light p-3">
        <i class="far fa-user-circle fa-2x text-info"></i>
    </a>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light px-5">
    <div class="container-fluid">
        <img class="img-fluid navbar-logo" src="<?= BASE_URL ?>images/banners/logo.png">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <?php if ($is_admin): ?>
                    <li class="nav-item p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="<?= BASE_URL ?>admin/dashboard.php"><b>Dashboard</b></a>
                    </li>
                <?php endif; ?>

                <li class="nav-item p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= BASE_URL ?>tourist/index.php"><b>Home</b></a>
                </li>

                <li class="nav-item p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'destinations.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= BASE_URL ?>tourist/destinations.php"><b>Destinations</b></a>
                </li>

                <li class="nav-item p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'tours.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= BASE_URL ?>tourist/tours.php"><b>Tours</b></a>
                </li>
            </ul>
        </div>
    </div>
</nav>