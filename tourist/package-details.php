<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php';
    include ROOT_PATH . 'db/connect-db.php';
    include ROOT_PATH .'auth/connect-session.php';

    $package_id = $_GET['id'] ?? ''; 
    $query_package = "SELECT * FROM tour_packages WHERE id = '$package_id'";
    $result_package = mysqli_query($conn, $query_package);
    $package = mysqli_fetch_assoc($result_package);
    $user_email = $_SESSION['user'] ?? '';
    $query_tourist = "SELECT * FROM tourists WHERE email = '$user_email'";
    $result_tourist = mysqli_query($conn, $query_tourist);
    $tourist = mysqli_fetch_assoc($result_tourist);
    $tourist_id = $tourist['id'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);
        $rating = $_POST['rating'] ?? 0;

        if ($tourist_id) {
            $query_insert_review = "
                INSERT INTO reviews (tourist_id, package_id, comment, rating, timestamp)
                VALUES ('$tourist_id', '$package_id', '$comment', '$rating', NOW())
            ";
            $result_insert = mysqli_query($conn, $query_insert_review);

            if ($result_insert) {
                header("Location: package-details.php?id=$package_id");
                exit();
            } else {
                echo "<script>alert('There was an error adding your comment. Please try again.');</script>";
            }
        } else {
            header("Location: " . BASE_URL . "auth/login.php");
        }
    }
    
    include ROOT_PATH . 'template/header.php';
?>

<body>
    <?php include ROOT_PATH . 'template/navigation.php'; ?>

    <div class="container package-container mt-4">
        <div class="row">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <h1 class="mb-3 text-primary text-uppercase fw-bolder me-auto" style="font-size: 1.75rem;">
                    <?php echo htmlspecialchars($package['name']); ?>
                </h1>

                <h6 class="fst-italic fs-5">
                    <i class="fas fa-user text-info"></i>
                    <?php
                        $bookings_count = mysqli_query($conn, "SELECT COUNT(b.id) AS count
                                                               FROM bookings b
                                                               INNER JOIN schedules s
                                                               ON b.schedule_id = s.id
                                                               WHERE s.package_id = '$package_id'");
                        echo mysqli_fetch_assoc($bookings_count)['count']
                    ?>
                </h6>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h2 class="mt-5 mb-3" style="font-size: 1.5rem;">Destinations Covered</h2>
                <div class="row my-3">
                    <?php
                        $query_destinations = "
                            SELECT d.name, dg.image
                            FROM destinations d
                            LEFT JOIN destinations_gallery dg
                            ON d.id = dg.destination_id
                            AND dg.id = (
                                SELECT MIN(id)
                                FROM destinations_gallery dg
                                WHERE dg.destination_id = d.id
                            )
                            INNER JOIN destination_packages dp ON dp.destination_id = d.id
                            WHERE dp.package_id = '$package_id'
                        ";
                        $result_destinations = mysqli_query($conn, $query_destinations);
                        
                        if ($result_destinations && mysqli_num_rows($result_destinations) > 0) {
                            while ($destination = mysqli_fetch_assoc($result_destinations)) {
                                $destination_name = $destination['name'];
                                $gallery_url = BASE_URL . $destination['image'];
                                echo '
                                <div class="col-lg-4 col-md-6 d-flex justify-content-center my-3">
                                    <a href="destination-details.php?item='.urlencode($destination_name).'" class="text-decoration-none">
                                        <div class="circle-item" style="background-image: url('.$gallery_url.');">
                                            <div class="overlay"></div>
                                            <h1 class="text-white text-center position-absolute w-100" style="font-size: 1.2rem;">'.$destination_name.'</h1>
                                        </div>
                                    </a>
                                </div>';
                            }
                        } else {
                            echo '<p>This package covers no destination</p>';
                        }
                    ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm p-3 mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-money-bill-wave"></i> Price: ৳
                        <?php 
                            if (isset($package['price'])) {
                                echo '<span class="fw-bold">' . number_format($package['price'], 2) . '/person</span>';
                            } else {
                                echo '<span class="text-muted">Not Available</span>';
                            }
                        ?>
                    </h6>

                    <h6 class="text-success mb-3">
                        <i class="fas fa-calendar-alt"></i> Duration: 
                        <?php
                            if (isset($package['duration_in_days'])) {
                                echo '<span class="fw-bold">' . $package['duration_in_days'] . ' days</span>';
                            } else {
                                echo '<span class="text-muted">Not Available</span>';
                            }
                        ?>
                    </h6>

                    <h6 class="text-info mb-3">
                        <i class="fas fa-clipboard-list"></i> Activities:
                    </h6>
                    <ul class="list-group mb-3">
                        <?php
                            $query_activities = "
                                SELECT DISTINCT a.name
                                FROM activities a
                                INNER JOIN destination_activities da 
                                ON a.id = da.activity_id
                                INNER JOIN destination_packages dp
                                ON dp.destination_id = da.destination_id
                                WHERE dp.package_id = '$package_id'
                                ORDER BY a.name ASC";

                            $result_activities = mysqli_query($conn, $query_activities);
                            if ($result_activities && mysqli_num_rows($result_activities) > 0) {
                                while ($activity = mysqli_fetch_assoc($result_activities)) {
                                    echo "<li class='list-group-item d-flex align-items-center'>
                                            <i class='fas fa-check-circle text-success me-2'></i> 
                                            " . htmlspecialchars($activity['name']) . "
                                        </li>";
                                }
                            } else {
                                echo "<li class='list-group-item text-muted'>No activities listed</li>";
                            }
                        ?>
                    </ul>

                    <h6 class="text-warning mb-3">
                        <i class="fas fa-bus"></i> Transportation:
                        <?php
                            $query_transportation = "
                                SELECT DISTINCT t.transport_type
                                FROM tour_packages tp
                                INNER JOIN schedules s ON tp.id = s.package_id
                                INNER JOIN transportations t ON s.transportation_id = t.id
                                WHERE tp.id = '$package_id'";
                            $result_transportation = mysqli_query($conn, $query_transportation);
                            if ($result_transportation && mysqli_num_rows($result_transportation) > 0) {
                                $transport = mysqli_fetch_assoc($result_transportation);
                                echo '<span class="fw-bold">' . htmlspecialchars($transport['transport_type']) . '</span>';
                            } else {
                                echo '<span class="text-muted">Not Available</span>';
                            }
                        ?>
                    </h6>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h2 class="mt-5 mb-3" style="font-size: 1.5rem;">Upcoming Schedules</h2>
                <div class="row my-3">
                    <?php
                        $schedule_query = "SELECT s.*, g.name AS guide_name, g.email AS guide_email, g.phone AS guide_phone,
                               t.transport_type, t.company AS transport_company, t.driver_contact, t.departure_location, t.capacity,
                               h.name AS hotel_name, h.location AS hotel_location, h.contact AS hotel_contact, h.rating AS hotel_rating
                                           FROM schedules s
                                           INNER JOIN staffs g 
                                           ON s.staff_id = g.id
                                           LEFT JOIN transportations t 
                                           ON t.id = s.transportation_id
                                           LEFT JOIN hotel_bookings hb 
                                           ON hb.schedule_id = s.id
                                           LEFT JOIN hotels h 
                                           ON h.id = hb.hotel_id
                                           WHERE s.package_id = '$package_id' 
                                           AND s.start_date >= CURDATE()
                                           AND s.status = 'Upcoming'
                                           ORDER BY s.start_date";
                    
                        $schedules = mysqli_query($conn, $schedule_query);

                        if (mysqli_num_rows($schedules) === 0) {
                            echo "<p>No upcoming schedules</p>";
                        } else {
                            while ($row = mysqli_fetch_assoc($schedules)) {
                                $start_date = new DateTime($row['start_date']);
                                $end_date = new DateTime($row['end_date']);
                                $today = new DateTime();
                                $interval = $today->diff($start_date)->days;
                                $countdown = ($interval == 1) ? "Tomorrow" : (($interval == 0) ? "Today" : "$interval days left");
    
                                $booking_query = "SELECT COUNT(id) AS booked_count FROM bookings WHERE schedule_id = " . $row['id'];
                                $booking_result = mysqli_query($conn, $booking_query);
                                $booking_row = mysqli_fetch_assoc($booking_result);
                                $availability = $row['capacity'] - $booking_row['booked_count'];
                        ?>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-lg">
                                <div class="card-header text-white text-center fw-bold py-3" style="background-color: #007bff; font-size: 1.2rem;">
                                    <?= $countdown ?>
                                </div>
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row g-3">
                                            <div class="col-md-6 d-flex">
                                                <div class="p-3 border rounded w-100 d-flex flex-column">
                                                    <h5 class="text-primary">Schedule Details</h5>
                                                    <p><strong>Start:</strong> <?= $start_date->format('F j, Y') ?></p>
                                                    <p><strong>End:</strong> <?= $end_date->format('F j, Y') ?></p>
                                                    <div class="mt-auto"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 d-flex">
                                                <div class="p-3 border rounded w-100 d-flex flex-column">
                                                    <h5 class="text-success">Guide Info</h5>
                                                    <p><strong>Name:</strong> <?= $row['guide_name'] ?></p>
                                                    <p><strong>Email:</strong> <?= $row['guide_email'] ?></p>
                                                    <p><strong>Phone:</strong> <?= $row['guide_phone'] ?></p>
                                                    <div class="mt-auto"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 d-flex">
                                                <div class="p-3 border rounded w-100 d-flex flex-column">
                                                    <h5 class="text-warning">Transport</h5>
                                                    <p><strong>Type:</strong> <?= $row['transport_type'] ?></p>
                                                    <p><strong>Company:</strong> <?= $row['transport_company'] ?></p>
                                                    <p><strong>Driver:</strong> <?= $row['driver_contact'] ?></p>
                                                    <p><strong>Departure:</strong> <?= $row['departure_location'] ?></p>
                                                    <p><strong>Availablity:</strong> <?= $availability ?></p>
                                                    <div class="mt-auto"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 d-flex">
                                                <?php if (!empty($row['hotel_name'])): ?>
                                                    <div class="p-3 border rounded w-100 d-flex flex-column">
                                                        <h5 class="text-danger">Hotel Info</h5>
                                                        <p><strong>Name:</strong> <?= $row['hotel_name'] ?></p>
                                                        <p><strong>Location:</strong> <?= $row['hotel_location'] ?></p>
                                                        <p><strong>Contact:</strong> <?= $row['hotel_contact'] ?></p>
                                                        <p><strong>Rating:</strong> ⭐<?= $row['hotel_rating'] ?>/5</p>
                                                        <div class="mt-auto"></div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div> 
                                    </div>
                                </div>

                                <div class="card-footer text-center">
                                    <label for="open-popup" class="btn btn-primary w-100 py-3 fw-bold" style="font-size: 1.2rem; cursor: pointer;">
                                        Book Now
                                    </label>

                                    <input type="radio" id="open-popup" name="popup-toggle" class="d-none">
                                    <input type="radio" id="close-popup" name="popup-toggle" class="d-none">

                                    <div class="popup">
                                        <div class="popup-content">
                                            <label for="close-popup" class="close">&times;</label>

                                            <form method="POST" action="booking-process.php">
                                                <h5 class="text-center mb-3">Book Your Tour</h5>

                                                <input type="hidden" name="schedule_id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="tourist_id" value="<?= $tourist_id ?>">
                                                <input type="hidden" name="price" value="<?= $package['price'] ?>">

                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Select Persons:</strong></label>
                                                    <div class="person-selection d-flex align-items-center justify-content-center">
                                                        <button type="button" class="btn btn-outline-secondary px-3 fw-bold" onclick="document.getElementById('persons').stepDown()">−</button>
                                                        <input type="number" name="persons" id="persons" value="1" min="1" max="<?= $availability ?>" class="form-control text-center mx-2" style="width: 60px;" readonly>
                                                        <button type="button" class="btn btn-outline-secondary px-3 fw-bold" onclick="document.getElementById('persons').stepUp()">+</button>
                                                    </div>
                                                    <small class="text-muted d-block text-center mt-1">Max: <?= $availability ?></small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Select Payment Method:</strong></label>
                                                    <div>
                                                        <input type="radio" name="payment_method" value="Bkash" checked> Bkash<br>
                                                        <input type="radio" name="payment_method" value="Nagad"> Nagad<br>
                                                        <input type="radio" name="payment_method" value="Bank Transfer"> Bank Transfer<br>
                                                        <input type="radio" name="payment_method" value="Cheque"> Cheque<br>
                                                        <input type="radio" name="payment_method" value="Cash"> Cash<br>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-success w-100">Confirm</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }}?>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h2 style="font-size: 1.25rem;">Rate this package</h2>
                        <p>Tell others what you think</p>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <div class="rating">
                                    <input type="radio" id="star5" name="rating" value="5" />
                                    <label for="star5"><i class="fas fa-star"></i></label>

                                    <input type="radio" id="star4" name="rating" value="4" />
                                    <label for="star4"><i class="fas fa-star"></i></label>

                                    <input type="radio" id="star3" name="rating" value="3" />
                                    <label for="star3"><i class="fas fa-star"></i></label>

                                    <input type="radio" id="star2" name="rating" value="2" />
                                    <label for="star2"><i class="fas fa-star"></i></label>

                                    <input type="radio" id="star1" name="rating" value="1" required />
                                    <label for="star1"><i class="fas fa-star"></i></label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label" style="font-size: 0.875rem;">Your
                                    Comment</label>
                                <textarea id="comment" name="comment" class="form-control" rows="3" placeholder="Describe your experience (optional)"></textarea>
                            </div>

                            <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                            <button type="submit" class="btn btn-primary" style="font-size: 0.875rem;">Submit
                                Comment</button>
                        </form>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h2 class="mt-5" style="font-size: 1.5rem;">Reviews</h2>
                        <div class="row">
                            <?php
                                $query_reviews = "
                                    SELECT r.comment, r.rating, r.timestamp, t.name AS tourist_name 
                                    FROM reviews r
                                    INNER JOIN tourists t ON t.id = r.tourist_id
                                    WHERE r.package_id = '$package_id'
                                    ORDER BY r.timestamp DESC";
                                $result_reviews = mysqli_query($conn, $query_reviews);
                                
                                if ($result_reviews && mysqli_num_rows($result_reviews) > 0) {
                                    while ($review = mysqli_fetch_assoc($result_reviews)) {
                                        $comment = htmlspecialchars($review['comment']);
                                        $rating = $review['rating'];
                                        $tourist_name = htmlspecialchars($review['tourist_name']);
                                        $timestamp = $review['timestamp'];

                                        $stars = '';
                                        for ($i = 1; $i <= 5; $i++) {
                                            $star_class = ($i <= $rating) ? 'text-warning' : 'text-muted';
                                            $stars .= "<span class='star $star_class'><i class='fas fa-star'></i></span>";
                                        }

                                        echo '
                                        <div class="col-12 mb-4">
                                            <div class="review-item">
                                                <h5 style="font-size: 1rem;">'.$tourist_name.' <span class="text-muted" style="font-size: 0.85rem;">('.$timestamp.')</span></h5>
                                                <p>'.$stars.'</p>
                                                <p style="font-size: 0.875rem;">'.$comment.'</p>
                                            </div>
                                        </div>';
                                    }
                                } else {
                                    echo '<p class="pb-5">No reviews yet!</p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . 'template/footer.php'; ?>
</body>