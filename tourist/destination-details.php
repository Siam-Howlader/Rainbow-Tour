<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>

<body>
    <?php include ROOT_PATH . 'template/navigation.php'; ?>

    <?php
        $item = isset($_GET['item']) ? htmlspecialchars($_GET['item']) : '';

        if ($item == '') {
            header('Location: destinations.php');
            exit;
        }

        $item = mysqli_real_escape_string($conn, $item);

        $query_string ="SELECT d.*, dg.image
                        FROM destinations d
                        LEFT JOIN destinations_gallery dg
                        ON d.id = dg.destination_id
                        AND dg.id = (SELECT MIN(id)
                                     FROM destinations_gallery dg
                                     WHERE dg.destination_id = d.id)
                        WHERE d.name = '$item'";

        $destination = mysqli_fetch_object(mysqli_query($conn, $query_string));
    ?>

    <?php
        if (empty($destination)) {
            ?>
            <div class="not-found display-2 fw-bold">
                Page not found!
            </div>
            <?php
            exit;
        }
    ?>

    <div class="hero-container d-flex align-items-center justify-content-center text-center text-white"
        style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), 
           url('<?= BASE_URL ?><?php echo $destination->image; ?>') center/cover no-repeat;">
        <div class="hero-content">
            <h1 class="display-3 fw-bold"><?php echo $item; ?></h1>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <div class="title mb-5">
                    <h2 class="text-primary"><?php echo $destination->name ?></h2>
                    <i class="text-muted"><?php echo $destination->location ?></i>
                </div>
                
                <p class="text-muted text-justify"><?php echo nl2br(htmlspecialchars($destination->description)); ?></p>
                
                <h3 class="mt-4 text-primary">Gallery</h3>
                <?php
                    $query = mysqli_query($conn, "SELECT * FROM destinations_gallery WHERE destination_id = $destination->id");

                    if (mysqli_num_rows($query) > 0) {
                        echo '<div class="row gallery">';

                        while ($row = mysqli_fetch_object($query)) {
                            echo '<div class="col-md-4 mb-3">
                                    <a href="' . BASE_URL . htmlspecialchars($row->image) . '" target="_blank">
                                        <img src="' . BASE_URL . htmlspecialchars($row->image) . '" class="img-fluid rounded" alt="' . '">
                                    </a>
                                </div>';
                        }
                        echo '</div>';
                    } else {
                        echo 'No images found for this destination.';
                    }
                ?>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Activities</h5>
                    </div>
                    <div class="card-body">
                        <?php
                            $activities_query = mysqli_query($conn, "SELECT DISTINCT a.name
                                                                     FROM activities a
                                                                     INNER JOIN destination_activities da
                                                                     ON da.activity_id = a.id
                                                                     WHERE da.destination_id = '$destination->id'");
                            
                            if (mysqli_num_rows($activities_query) > 0) {
                                echo '<ul class="list-group">';
                                while ($activity = mysqli_fetch_object($activities_query)) {
                                    echo '<li class="list-group-item border-0 border-bottom text-center">' . htmlspecialchars($activity->name) . '</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>No activities listed for this destination.</p>';
                            }
                        ?>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Find This Destination in Packages?</h5>
                    </div>
                    <div class="card-body">
                        <form action="tours.php" method="get">
                            <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination->name); ?>">
                            <button type="submit" class="btn btn-block btn-primary w-100">
                                <i class="fas fa-search"></i> Search Packages
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . 'template/footer.php'; ?>
</body>
