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
    if (isset($_POST['add_hotel'])) {
        $name = $conn->real_escape_string($_POST['hotel_name']);
        $location = $conn->real_escape_string($_POST['hotel_location']);
        $contact = $conn->real_escape_string($_POST['hotel_contact']);
        $rating = (int) $_POST['hotel_rating'];

        $conn->query("INSERT INTO hotels (name, location, contact, rating) VALUES ('$name', '$location', '$contact', $rating)");
        $hotel_id = $conn->insert_id;

        echo "<script>location.href='hotels.php';</script>";
        exit;
    }

    if (isset($_POST['update_hotel'])) {
        $id = (int) $_POST['hotel_id'];
        $name = $conn->real_escape_string($_POST['hotel_name']);
        $location = $conn->real_escape_string($_POST['hotel_location']);
        $contact = $conn->real_escape_string($_POST['hotel_contact']);
        $rating = (int) $_POST['hotel_rating'];

        $conn->query("UPDATE hotels SET name='$name', location='$location', contact='$contact', rating=$rating WHERE id=$id");
        
        echo "<script>location.href='hotels.php';</script>";
        exit;
    }

    if (isset($_POST['book_hotel'])) {
        $schedule_id = (int) $_POST['schedule_id'];
        $hotel_id = (int) $_POST['hotel_id'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $status = 'Pending';

        $conn->query("INSERT INTO hotel_bookings (schedule_id, hotel_id, check_in, check_out, status) VALUES ($schedule_id, $hotel_id, '$check_in', '$check_out', '$status')");
        
        echo "<script>location.href='hotels.php';</script>";
        exit;
    }

    if (isset($_GET['delete_hotel'])) {
        $id = (int) $_GET['delete_hotel'];
        $conn->query("DELETE FROM hotels WHERE id = $id");
        echo "<script>location.href='hotels.php';</script>";
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

        <div class="d-flex align-items-center mb-3">
            <h4 class="mb-0">Hotels</h4>

            <button class="ms-auto btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal-hotels">
                <i class="fas fa-plus"> Add Hotel</i>
            </button>

            <button class="btn btn-success btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#bookModal-hotels">
                <i class="fas fa-plus"> Book Hotel</i>
            </button>
        </div>

        <table class="table table-bordered table-striped mb-5">
            <thead class="table-dark">
                <tr>
                    <th>SL.</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Contact</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    $hotelResult = $conn->query("SELECT * FROM hotels");
                    while ($row = $hotelResult->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <?= $i++ ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['name']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['location']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['contact']) ?>
                        </td>
                        <td>
                            <?= $row['rating'] ?> <i class="bi bi-star-fill text-warning"></i>
                        </td>
                        <td class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                data-bs-target="#editHotel<?= $row['id'] ?>">
                                <i class="fas fa-pencil-alt"> Edit</i>
                            </button>
                            <a href="?delete_hotel=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this hotel?')">
                                <i class="fas fa-trash-alt"> Delete</i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="editHotel<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST">
                                    <input type="hidden" name="hotel_id" value="<?= $row['id'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Hotel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label>Name</label>
                                                <input type="text" name="hotel_name"
                                                    value="<?= htmlspecialchars($row['name']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Location</label>
                                                <input type="text" name="hotel_location"
                                                    value="<?= htmlspecialchars($row['location']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Contact</label>
                                                <input type="text" name="hotel_contact"
                                                    value="<?= htmlspecialchars($row['contact']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Rating</label>
                                                <input type="number" name="hotel_rating" class="form-control"
                                                    min="1" max="5" value="<?= $row['rating'] ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button name="update_hotel" class="btn btn-primary">Update</button>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="modal fade" id="addModal-hotels" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Hotel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" name="hotel_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Location</label>
                                    <input type="text" name="hotel_location" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Contact</label>
                                    <input type="text" name="hotel_contact" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Rating</label>
                                    <input type="number" name="hotel_rating" class="form-control" min="1" max="5" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name="add_hotel" class="btn btn-success">Add</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="bookModal-hotels" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Book Hotel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Schedule</label>
                                    <select name="schedule_id" class="form-control" required>
                                        <?php
                                            $schedules = $conn->query("SELECT * FROM schedules");
                                            while ($schedule = $schedules->fetch_assoc()):
                                        ?>
                                        <option value="<?= $schedule['id'] ?>"><?= htmlspecialchars($schedule['id']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Hotel</label>
                                    <select name="hotel_id" class="form-control" required>
                                        <?php
                                            $hotels = $conn->query("SELECT * FROM hotels");
                                            while ($hotel = $hotels->fetch_assoc()):
                                        ?>
                                        <option value="<?= $hotel['id'] ?>"><?= htmlspecialchars($hotel['name']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Check-in Date</label>
                                    <input type="date" name="check_in" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Check-out Date</label>
                                    <input type="date" name="check_out" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name="book_hotel" class="btn btn-success">Book</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
