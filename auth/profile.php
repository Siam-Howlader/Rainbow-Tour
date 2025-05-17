<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>
<?php include ROOT_PATH . 'auth/manage-access.php'; ?>

<?php
    if (!$logged_in) {
        header("Location: login.php");
        exit();
    }
    
    $query = "SELECT id, name, nid, email, dob, phone, address, image
              FROM tourists WHERE email = '$user_email' 
              UNION 
              SELECT id, name, nid, email, dob, phone, address, image
              FROM staffs 
              WHERE email = '$user_email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "<script>alert('Something went wrong. Please try again.');</script>";

        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $nid = mysqli_real_escape_string($conn, $_POST['nid']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $imagePath = null;
    
        if (!empty($_FILES['profile_image']['name'])) {
            $coverTmpName = $_FILES['profile_image']['tmp_name'];
            $coverOriginalName = basename($_FILES['profile_image']['name']);
            $coverUniqueName = time() . '_' . $coverOriginalName;
            $coverTargetPath = $PROFILE_IMAGE_UPLOAD_DIR . $coverUniqueName;

            if (!is_dir($PROFILE_IMAGE_UPLOAD_DIR)) {
                mkdir($PROFILE_IMAGE_UPLOAD_DIR, 0755, true);
            }
    
            if (move_uploaded_file($coverTmpName, $coverTargetPath)) {
                $imagePath = $PROFILE_IMAGE_URL . $coverUniqueName;
            }
        }

        $update_query = $is_admin ? "UPDATE staffs " : "UPDATE tourists ";

        $update_query .= "SET 
                            name = '$name',
                            nid = '$nid',
                            email = '$email',
                            dob = '$dob',
                            phone = '$phone',
                            address = '$address'";

        if (!empty($imagePath)) {
            $update_query .= ", image = '$imagePath'";
        }

        $update_query .= " WHERE email = '$user_email'";

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Profile updated successfully.";
            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating profile.";
        }
    }
?>

<body class="profile">
    <?php include ROOT_PATH . 'template/navigation.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="p-4">
                    <h3 class="text-center mb-4">Booked Tours</h3>
                    <?php
                        $userId = $user['id']; 

                        $query = "
                            SELECT tp.name, s.start_date, s.end_date, s.package_id, b.timestamp, p.status, p.amount, b.persons
                            FROM bookings b
                            INNER JOIN schedules s 
                            ON s.id = b.schedule_id
                            INNER JOIN tour_packages tp 
                            ON tp.id = s.package_id
                            INNER JOIN payments p 
                            ON p.booking_id = b.id
                            WHERE b.tourist_id = '$userId'";

                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $startDate = $row['start_date'];
                                $endDate = $row['end_date'];
                                $packageId = $row['package_id'];
                                $packageName = $row['name'];
                                $timestamp = $row['timestamp'];
                                $paymentStatus = $row['status'];
                                $amount = $row['amount'];
                                $persons = $row['persons'];
                                ?>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="card shadow-lg mb-4">
                                            <div class="card-body">
                                                <h3 class="card-title text-center text-primary mb-3"><?php echo $packageName; ?></h3>
                                                <p><strong>Start Date:</strong> <?php echo $startDate; ?></p>
                                                <p><strong>End Date:</strong> <?php echo $endDate; ?></p>
                                                <p><strong>Number of Participants:</strong> <?php echo $persons; ?></p>
                                                <p><strong>Booking Date:</strong> <?php echo $timestamp; ?></p>
                                                <p><strong>Payment Status:</strong> <?php echo $paymentStatus; ?></p>
                                                <p><strong>Amount:</strong> <?php echo $amount; ?> TK</p>
                                                <a href="<?= BASE_URL ?>tourist/package-details.php?id=<?php echo $packageId; ?>" class="btn btn-primary w-100 mt-3">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                            }
                        } else {
                            echo '<p class="text-center">You have no booked tours.</p>';
                        }
                    ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="text-center mb-4 position-relative">
                            <div class="profile-picture-container mx-auto mb-3 position-relative" style="width: 150px; height: 150px;">
                                <img 
                                    src="<?php echo !empty($user['image']) ? BASE_URL . $user['image'] : BASE_URL . 'images/banners/default-user.png'; ?>" 
                                    alt="Profile Picture" 
                                    class="rounded-circle border border-3 shadow" 
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                >
                                <div class="upload-overlay d-flex align-items-center justify-content-center rounded-circle text-white fw-semibold">
                                    Upload Photo
                                    <input type="file" name="profile_image" class="profile-picture position-absolute w-100 h-100" onchange="this.form.submit()">
                                </div>
                            </div>
                            <h3 class="fw-bold">Profile Information</h3>
                        </div>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="nid" class="form-label fw-semibold">NID</label>
                            <input type="text" class="form-control" id="nid" name="nid" value="<?php echo $user['nid']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $user['dob']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success px-4">Update Profile</button>
                            <a href="logout.php" class="btn btn-outline-danger px-4">Logout</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . 'template/footer.php'; ?>
</body>
