<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>

<body>
    <?php include ROOT_PATH . 'template/navigation.php'; ?>

    <div class="hero-container d-flex align-items-center justify-content-center text-center text-white"
        style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), 
           url('<?= BASE_URL ?>images/banners/destinations-banner.jpg') center/cover no-repeat;">
        <div class="hero-content">
            <h1 class="display-3 fw-bold">Destinations</h1>
        </div>
    </div>

    <div class="container-fluid my-5 px-4">
        <div class="row g-5 justify-content-center">
        <?php 
            $query_string ="SELECT d.name, dg.image
                            FROM destinations d
                            LEFT JOIN destinations_gallery dg
                            ON d.id = dg.destination_id
                            AND dg.id = (SELECT MIN(id)
                                         FROM destinations_gallery dg
                                         WHERE dg.destination_id = d.id);";
                                                          
            $query = mysqli_query($conn, $query_string);

            if ($query) {
                if (mysqli_num_rows($query) > 0) {
                    while ($row = mysqli_fetch_assoc($query)) {
                        $destination_name = $row['name'];
                        $gallery_url = $row['image'];
                        echo '
                        <div class="col-lg-4 col-md-6 d-flex justify-content-center">
                            <a href="destination-details.php?item='.urlencode($destination_name).'" class="text-decoration-none">
                                <div class="circle-item position-relative" style="background-image: url(' . BASE_URL . $gallery_url . ');">
                                    <div class="overlay"></div>
                                    <h1 class="text-white text-center position-absolute w-100">'.$destination_name.'</h1>
                                </div>
                            </a>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12 text-center"><h5>No data found!</h5></div>';
                }
            } else {
                echo "Error fetching data.";
            }
        ?>
        </div>
    </div>

    <?php include ROOT_PATH . 'template/footer.php'; ?>
</body>