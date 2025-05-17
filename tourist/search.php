<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php';
    include ROOT_PATH . 'db/connect-db.php';

    $query_string1 = "SELECT tp.id, COUNT(b.id) AS booking_count
                      FROM tour_packages tp
                      INNER JOIN schedules s
                      ON s.package_id = tp.id
                      INNER JOIN bookings b 
                      ON b.schedule_id = s.id
                      WHERE b.timestamp >= NOW() - INTERVAL 15 DAY
                      AND s.status = 'Ongoing'
                      GROUP BY tp.id
                      ORDER BY booking_count DESC;";

    $query1 = mysqli_query($conn, $query_string1);
    $package_list = [];

    while ($row = mysqli_fetch_assoc($query1)) {
       $package_list[] = $row;
    }

    $query_string2 = "SELECT ROUND(COUNT(*) * 0.2) AS popular_count FROM tour_packages";
    $query2 = mysqli_query($conn, $query_string2);
    $row2 = mysqli_fetch_assoc($query2);
    $popular_count = $row2['popular_count'];

    $popular_packages = array_slice($package_list, 0, $popular_count);

    $destination = $_GET['destination'] ?? '';
    $activities = $_GET['activities'] ?? [];
    $departure_date = $_GET['departure_date'] ?? '';
    $duration = $_GET['duration'] ?? [];
    $transportation = $_GET['transportation'] ?? [];
    $price_from = $_GET['price_from'] ?? '';
    $price_to = $_GET['price_to'] ?? '';
    
    $order_by = isset($_GET['order_by']) ? (array) $_GET['order_by'] : []; 

    if (in_array('low-high', $order_by)) {
        $order_by = array_diff($order_by, ['high-low']);
    }

    if (in_array('high-low', $order_by)) {
        $order_by = array_diff($order_by, ['low-high']);
    }

    if (in_array('a-z', $order_by)) {
        $order_by = array_diff($order_by, ['z-a']);
    }

    if (in_array('z-a', $order_by)) {
        $order_by = array_diff($order_by, ['a-z']);
    }

    if (in_array('shortest', $order_by)) {
        $order_by = array_diff($order_by, ['longest']);
    }

    if (in_array('longest', $order_by)) {
        $order_by = array_diff($order_by, ['shortest']);
    }

    $query = "SELECT DISTINCT tp.id, tp.name, tp.image, tp.price, tp.duration_in_days, 
                     GROUP_CONCAT(DISTINCT d.name SEPARATOR ', ') AS destinations,
                     GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS activities,
                     GROUP_CONCAT(DISTINCT t.transport_type SEPARATOR ', ') AS transport
              FROM tour_packages tp
              LEFT JOIN schedules s 
              ON tp.id = s.package_id
              LEFT JOIN destination_packages pd 
              ON tp.id = pd.package_id
              LEFT JOIN destinations d 
              ON pd.destination_id = d.id
              LEFT JOIN destination_activities da 
              ON d.id = da.destination_id
              LEFT JOIN activities a 
              ON da.activity_id = a.id
              LEFT JOIN transportations t 
              ON s.transportation_id = t.id
              WHERE 1=1";

    if (!empty($destination)) {
        $query .= " AND (d.name LIKE '%$destination%' OR tp.name LIKE '%$destination%')";
    }

    if (!empty($activities)) {
        $query .= " AND a.name IN ('" . implode("','", $activities) . "')";
    }

    if (!empty($departure_date)) {
        $query .= " AND s.start_date = '$departure_date'";
    }

    if (!empty($duration)) {
        $duration_conditions = [];
        if (in_array('1-3', $duration)) $duration_conditions[] = "tp.duration_in_days BETWEEN 1 AND 3";
        if (in_array('4-7', $duration)) $duration_conditions[] = "tp.duration_in_days BETWEEN 4 AND 7";
        if (in_array('8+', $duration)) $duration_conditions[] = "tp.duration_in_days >= 8";
        $query .= " AND (" . implode(" OR ", $duration_conditions) . ")";
    }

    if (!empty($transportation)) {
        $query .= " AND t.transport_type IN ('" . implode("','", $transportation) . "')";
    }

    if (!empty($price_from)) {
        $query .= " AND tp.price >= $price_from";
    }

    if (!empty($price_to)) {
        $query .= " AND tp.price <= $price_to";
    }

    $query .= " GROUP BY tp.id";

    $order_clauses = [];

    if (in_array('low-high', $order_by)) {
        $order_clauses[] = "tp.price ASC";
    }
    if (in_array('high-low', $order_by)) {
        $order_clauses[] = "tp.price DESC";
    }

    if (in_array('a-z', $order_by)) {
        $order_clauses[] = "tp.name ASC";
    }
    if (in_array('z-a', $order_by)) {
        $order_clauses[] = "tp.name DESC";
    }

    if (in_array('shortest', $order_by)) {
        $order_clauses[] = "tp.duration_in_days ASC";
    }
    if (in_array('longest', $order_by)) {
        $order_clauses[] = "tp.duration_in_days DESC";
    }

    if (!empty($order_clauses)) {
        $query .= " ORDER BY " . implode(", ", $order_clauses);
    }

    $result = mysqli_query($conn, $query);
?>

<div class="container package-container mt-4">
    <div class="d-flex align-items-center pb-2 border-bottom">
        <h5 class="text-secondary">
            Search results
            <?php if (!empty($destination)) : ?>
                for "<strong class="text-primary"><?= htmlspecialchars($destination) ?></strong>"
            <?php endif; ?>
        </h5>
        <div class="ms-auto">
            <span class="badge bg-danger px-3 py-2 fs-6 shadow-sm">
                <?= mysqli_num_rows($result) ?> Results
            </span>
        </div>

    </div>

    <div class="d-flex justify-content-between align-items-center my-3">
        <span class="text-muted">SORT BY</span>
        <div class="d-flex gap-2">
            <?php 
                $sorting_options = [
                    'low-high' => 'Price: Low-High',
                    'high-low' => 'Price: High-Low',
                    'a-z' => 'A-Z',
                    'z-a' => 'Z-A',
                    'shortest' => 'Shortest',
                    'longest' => 'Longest'
                ];
                foreach ($sorting_options as $key => $label) {
                    $temp_order_by = $order_by;
                    if (in_array($key, $order_by)) {
                        $temp_order_by = array_diff($temp_order_by, [$key]);
                    } else {
                        if ($key == 'low-high') {
                            $temp_order_by = array_diff($temp_order_by, ['high-low']);
                        } elseif ($key == 'high-low') {
                            $temp_order_by = array_diff($temp_order_by, ['low-high']);
                        } elseif ($key == 'a-z') {
                            $temp_order_by = array_diff($temp_order_by, ['z-a']);
                        } elseif ($key == 'z-a') {
                            $temp_order_by = array_diff($temp_order_by, ['a-z']);
                        } elseif ($key == 'shortest') {
                            $temp_order_by = array_diff($temp_order_by, ['longest']);
                        } elseif ($key == 'longest') {
                            $temp_order_by = array_diff($temp_order_by, ['shortest']);
                        }
                        $temp_order_by[] = $key;
                    }
                    $selected = in_array($key, $order_by) ? 'btn-dark text-white' : 'btn-light text-secondary';
                    echo "<a href='?" . http_build_query(array_merge($_GET, ['order_by' => $temp_order_by])) . "' 
                            class='btn btn-sm $selected rounded-pill'>$label</a>";
                }
            ?>
        </div>
    </div>
    
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100 border-0">
                    <div class="position-relative">
                        <div class="badge-container">
                            <?php
                                $is_popular = in_array($row['id'], array_column($popular_packages, 'id'));
                                if ($is_popular) : ?>
                                    <span class="badge bg-warning">Popular</span>
                            <?php endif; ?>

                            <span class="badge bg-dark"><?= htmlspecialchars($row['duration_in_days']) ?> days</span>
                            <span class="badge bg-primary">৳<?= htmlspecialchars($row['price']) ?>/per person</span>
                        </div>

                        <?php
                            $imagePath = BASE_URL . (!empty($row['image']) ? $row['image'] : 'images/banners/default-package-cover.png');
                        ?>
                        <img src="<?= htmlspecialchars($imagePath) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                    </div>

                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title fw-bold text-info"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="mb-1"><strong class="text-dark">Destinations:</strong> <?= htmlspecialchars($row['destinations']) ?></p>
                        <p class="mb-1"><strong class="text-dark">Activities:</strong> <?= htmlspecialchars($row['activities']) ?></p>
                        <p class="mb-1"><strong class="text-dark">Transport:</strong> <?= htmlspecialchars($row['transport']) ?></p>
                        
                        <a href="package-details.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-dark btn-sm mt-auto">
                            <i class="fas fa-ticket-alt me-1"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if (mysqli_num_rows($result) == 0) : ?>
        <p class="text-danger">No results found.</p>
    <?php endif; ?>
</div>
