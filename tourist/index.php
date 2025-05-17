<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>

<?php
    $query = "SELECT name, email, phone, image, role FROM staffs WHERE availability = 1";
    $staffs = mysqli_query($conn, $query);
?>

<body>
    <?php include ROOT_PATH . 'template/navigation.php'; ?>

    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="carousel-image-container">
                    <img src="<?= BASE_URL ?>images/banners/banner-1.jpeg" class="d-block w-100" alt="Image 1">
                    <div class="overlay"></div>
                    <div class="carousel-caption d-flex align-items-center justify-content-center">
                        <div class="text-center text-light">
                            <h1 class="display-4">Explore and travel</h1>
                            <p class="lead">Make your vacation wonderful and worthwhile</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="carousel-item">
                <div class="carousel-image-container">
                    <img src="<?= BASE_URL ?>images/banners/banner-2.jpg" class="d-block w-100" alt="Image 2">
                    <div class="overlay"></div>
                    <div class="carousel-caption d-flex align-items-center justify-content-center">
                        <div class="text-center text-light">
                            <h1 class="display-4">Relax and enjoy</h1>
                            <p class="lead">Spend the best time with friends and family</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container us-container py-5">
        <div class="row text-center mb-5">
            <h2 class="display-4 text-info fw-bolder">Why Choose Us?</h2>
            <p class="lead text-muted">We provide the best experience for your travels. Here's why!</p>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-0">
                    <img src="<?= BASE_URL ?>images/banners/why-us-banner-1.jpg" class="card-img-top" alt="Icon 1">
                    <div class="card-body">
                        <h5 class="card-title">Expert Travel Guides</h5>
                        <p class="card-text">Our experienced and friendly guides ensure you have an unforgettable journey.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-0">
                    <img src="<?= BASE_URL ?>images/banners/why-us-banner-2.jpg" class="card-img-top" alt="Icon 2">
                    <div class="card-body">
                        <h5 class="card-title">Affordable Packages</h5>
                        <p class="card-text">We offer competitive prices for all our tour packages, without compromising quality.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-0">
                    <img src="<?= BASE_URL ?>images/banners/why-us-banner-3.jpg" class="card-img-top" alt="Icon 3">
                    <div class="card-body">
                        <h5 class="card-title">24/7 Customer Support</h5>
                        <p class="card-text">We are available around the clock to assist you with any travel-related concerns.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container guide-container py-5">
        <div class="row text-center mb-5">
            <h2 class="display-4 text-info fw-bolder">Meet Our Expert Team</h2>
            <p class="lead text-muted">Our guides are here to ensure your trip is unforgettable and enriching. Get to know the faces behind the expertise!</p>
        </div>

        <div class="row">
            <?php while($staff = mysqli_fetch_assoc($staffs)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                    <img src="<?= BASE_URL . (!empty($staff['image']) ? $staff['image'] : 'images/banners/default-user.png') ?>" class="guide-image img-fluid w-100" alt="Guide Image">
                        <div class="card-body text-center">
                            <h4 class="card-title text-primary fw-bold"><?php echo $staff['name']; ?></h4>
                            <h6 class="card-title text-muted"><?php echo $staff['role']; ?></h6>
                            <div class="d-flex justify-content-center mb-2">
                                <a href="mailto:<?php echo $staff['email']; ?>" class="text-muted text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i> <small><?php echo $staff['email']; ?></small>
                                </a>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="tel:<?php echo $staff['phone']; ?>" class="text-muted text-decoration-none">
                                    <i class="fas fa-phone me-1"></i> <small><?php echo $staff['phone']; ?></small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include ROOT_PATH . 'template/footer.php'; ?>
</body>

</html>