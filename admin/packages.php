<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>
<?php include ROOT_PATH . 'auth/manage-access.php'; ?>

<?php
    if (!$admin) {
        header("Location: " . BASE_URL . "tourist/index.php");
        exit();
    }
?>

<?php
    if (isset($_POST['add_package'])) {
        $name = $conn->real_escape_string($_POST['pkg_name']);
        $duration = (int)$_POST['pkg_duration'];
        $price = (float)$_POST['pkg_price'];
        $imagePath = null;
    
        if (!empty($_FILES['cover_photo']['name'])) {
            $coverTmpName = $_FILES['cover_photo']['tmp_name'];
            $coverOriginalName = basename($_FILES['cover_photo']['name']);
            $coverUniqueName = time() . '_' . $coverOriginalName;
            $coverTargetPath = $PACKAGE_IMAGE_UPLOAD_DIR . $coverUniqueName;
    
            if (move_uploaded_file($coverTmpName, $coverTargetPath)) {
                $imagePath = $PACKAGE_IMAGE_URL . $coverUniqueName;
            }
        }

        $conn->query("INSERT INTO tour_packages (name, duration_in_days, price, image) VALUES ('$name', $duration, $price, '$imagePath')");
    
        $pkg_id = $conn->insert_id;
    
        if (!empty($_POST['destinations'])) {
            foreach ($_POST['destinations'] as $dest_id) {
                $conn->query("INSERT INTO destination_packages (destination_id, package_id) VALUES ($dest_id, $pkg_id)");
            }
        }
    
        echo "<script>location.href='packages.php';</script>";
        exit;
    }    

    if (isset($_POST['update_package'])) {
        $id = (int)$_POST['pkg_id'];
        $name = $conn->real_escape_string($_POST['pkg_name']);
        $duration = (int)$_POST['pkg_duration'];
        $price = (float)$_POST['pkg_price'];
    
        $imagePath = null;
    
        if (!empty($_FILES['cover_photo']['name'])) {
            $coverTmpName = $_FILES['cover_photo']['tmp_name'];
            $coverOriginalName = basename($_FILES['cover_photo']['name']);
            $coverUniqueName = time() . '_' . $coverOriginalName;
            $coverTargetPath = $PACKAGE_IMAGE_UPLOAD_DIR . $coverUniqueName;
    
            if (move_uploaded_file($coverTmpName, $coverTargetPath)) {
                $imagePath = $PACKAGE_IMAGE_URL . $coverUniqueName;
            }
        }
    
        $conn->query("UPDATE tour_packages SET name='$name', duration_in_days=$duration, price=$price, image='$imagePath' WHERE id=$id");
        $conn->query("DELETE FROM destination_packages WHERE package_id = $id");
    
        if (!empty($_POST['destinations'])) {
            foreach ($_POST['destinations'] as $dest_id) {
                $conn->query("INSERT INTO destination_packages (destination_id, package_id) VALUES ($dest_id, $id)");
            }
        }
    
        echo "<script>location.href='packages.php';</script>";
        exit;
    }    

    if (isset($_GET['delete_package'])) {
        $id = (int) $_GET['delete_package'];
    
        $conn->query("DELETE FROM destination_packages WHERE package_id = $id");
        $schedulesResult = $conn->query("SELECT id, transportation_id FROM schedules WHERE package_id = $id");

        while ($schedule = $schedulesResult->fetch_assoc()) {
            $schedule_id = $schedule['id'];
            $transportation_id = $schedule['transportation_id'];
    
            $conn->query("DELETE FROM hotel_bookings WHERE schedule_id = $schedule_id");

            $bookingsResult = $conn->query("SELECT id FROM bookings WHERE schedule_id = $schedule_id");
            while ($booking = $bookingsResult->fetch_assoc()) {
                $booking_id = $booking['id'];
                $conn->query("DELETE FROM payments WHERE booking_id = $booking_id");
            }

            $conn->query("DELETE FROM bookings WHERE schedule_id = $schedule_id");
            $conn->query("DELETE FROM schedules WHERE id = $schedule_id");
            $conn->query("DELETE FROM transportations WHERE id = $transportation_id");
        }
    
        $conn->query("DELETE FROM reviews WHERE package_id = $id");
        $conn->query("DELETE FROM destination_packages WHERE package_id = $id");
        $conn->query("DELETE FROM tour_packages WHERE id = $id");
    
        echo "<script>location.href='packages.php';</script>";
        exit;
    }
?>

<body class="bg-info">
    <?php include ROOT_PATH . 'template/navigation.php'; ?>

    <div class="container admin-container my-5">
        <div class="d-flex mb-4">
            <h2>Admin Panel</h2>
            
            <div class="d-flex ms-auto justify-content-center">
                <a href="dashboard.php" class="btn-dashboard d-flex align-items-center">
                    <i class="fas fa-qrcode me-2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Packages</h4>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal-packages">
                <i class="fas fa-plus"> Add Package</i>
            </button>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>SL.</th>
                    <th>Name</th>
                    <th>Duration (Days)</th>
                    <th>Price</th>
                    <th>Destinations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $pkgResult = $conn->query("SELECT * FROM tour_packages");
                while ($row = $pkgResult->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['duration_in_days'] ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td style="max-width: 300px;">
                        <?php
                        $destinations = $conn->query("SELECT d.name FROM destinations d
                            JOIN destination_packages dp ON dp.destination_id = d.id 
                            WHERE dp.package_id = {$row['id']} ORDER BY d.name");
                        while ($dest = $destinations->fetch_assoc()):
                        ?>
                            <span class="badge bg-primary activity-badge"><?= htmlspecialchars($dest['name']) ?></span>
                        <?php endwhile; ?>
                    </td>
                    <td>
                        <div class="d-flex align-items-center h-100 gap-2">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-packages<?= $row['id'] ?>">
                                <i class="fas fa-pencil-alt"> Edit</i>
                            </button>

                            <a href="?delete_package=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this package?')">
                                <i class="fas fa-trash-alt"> Delete</i>
                            </a>
                        </div>
                    </td>
                </tr>

                <div class="modal fade" id="editModal-packages<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="pkg_id" value="<?= $row['id'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Package</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Package Name*</label>
                                        <input type="text" name="pkg_name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Select Cover Photo</label>
                                        <input type="file" name="cover_photo" class="form-control" accept="image/*">
                                    </div>

                                    <div class="mb-3">
                                        <label>Duration (Days)*</label>
                                        <input type="number" name="pkg_duration" min="1" value="<?= $row['duration_in_days'] ?>" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Price*</label>
                                        <input type="number" name="pkg_price" min="0" value="<?= $row['price'] ?>" class="form-control" step="0.01" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Select Destinations</label>
                                        <div class="d-flex flex-wrap gap-2 border rounded p-3">
                                            <?php
                                            $selectedDestinations = [];
                                            $getDests = $conn->query("SELECT destination_id FROM destination_packages WHERE package_id = {$row['id']}");
                                            while ($dest = $getDests->fetch_assoc()) {
                                                $selectedDestinations[] = $dest['destination_id'];
                                            }
                                            $destinationsList = $conn->query("SELECT DISTINCT id, name FROM destinations ORDER BY name");
                                            while ($dest = $destinationsList->fetch_assoc()):
                                            ?>
                                                <label class="activity-label">
                                                    <input type="checkbox" name="destinations[]" value="<?= $dest['id'] ?>" class="d-none" <?= in_array($dest['id'], $selectedDestinations) ? 'checked' : '' ?>>
                                                    <span class="btn btn-sm <?= in_array($dest['id'], $selectedDestinations) ? 'btn-primary text-white' : 'btn-outline-primary' ?>">
                                                        <?= htmlspecialchars($dest['name']) ?>
                                                    </span>
                                                </label>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button name="update_package" class="btn btn-primary">Update</button>
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="modal fade" id="addModal-packages" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Package</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Package Name*</label>
                                <input type="text" name="pkg_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Select Cover Photo</label>
                                <input type="file" name="cover_photo" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label>Duration (Days)*</label>
                                <input type="number" name="pkg_duration" class="form-control" value="1" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label>Price*</label>
                                <input type="number" name="pkg_price" class="form-control" min="0" step="0.01" required>
                            </div>

                            <div class="mb-3">
                                <label>Select Destinations</label>
                                <div class="d-flex flex-wrap gap-2 border rounded p-3">
                                    <?php
                                    $destinations = $conn->query("SELECT DISTINCT id, name FROM destinations ORDER BY name");
                                    while ($dest = $destinations->fetch_assoc()):
                                    ?>
                                        <label class="activity-label">
                                            <input type="checkbox" name="destinations[]" value="<?= $dest['id'] ?>" class="d-none">
                                            <span class="btn btn-sm btn-outline-primary"><?= htmlspecialchars($dest['name']) ?></span>
                                        </label>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name="add_package" class="btn btn-success">Add</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    document.querySelectorAll('.activity-label input').forEach(input => {
        input.addEventListener('change', function () {
            const span = this.nextElementSibling;
            if (this.checked) {
                span.classList.remove('btn-outline-primary');
                span.classList.add('btn-primary', 'text-white');
            } else {
                span.classList.remove('btn-primary', 'text-white');
                span.classList.add('btn-outline-primary');
            }
        });
    });
</script>
