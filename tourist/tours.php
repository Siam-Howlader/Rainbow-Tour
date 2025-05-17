<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>

<?php 
    $selected_destination    = $_GET['destination'] ?? ''; 
    $selected_activities     = $_GET['activities'] ?? []; 
    $selected_departure_date = $_GET['departure_date'] ?? ''; 
    $selected_duration       = $_GET['duration'] ?? []; 
    $selected_transportation = $_GET['transportation'] ?? []; 
    $selected_price_from     = $_GET['price_from'] ?? ''; 
    $selected_price_to       = $_GET['price_to'] ?? ''; 
?>

<body>
    <?php include ROOT_PATH . 'template/navigation.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 bg-white p-3 sidebar">
                <form method="GET" action="tours.php">
                    <div class="d-flex">
                        <h3 class="fw-bold text-uppercase text-black small-title px-3">Find a Trip</h3>
                        <div class="text-end ms-auto">
                            <a href="tours.php" class="btn btn-outline-danger btn-sm border">Reset</a>
                        </div>
                    </div>

                    <hr class="mt-1 mb-2 border-dark">

                    <div class="accordion" id="tripFilters">
                        <div class="accordion-item border-0 bg-white">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button bg-white text-black fw-bold text-uppercase border-0 small-text py-2"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#destinations"
                                    aria-expanded="true">
                                    Destinations and packages
                                </button>
                            </h2>
                            <div id="destinations"
                                class="accordion-collapse collapse <?php echo !empty($selected_destination) ? 'show' : ''; ?>">
                                <div class="accordion-body">
                                    <input type="text" name="destination" class="form-control text-black w-100"
                                        placeholder="Search destinations or packages..."
                                        value="<?= htmlspecialchars($selected_destination) ?>">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-1 mb-2 border-dark">

                        <div class="accordion-item border-0 bg-white">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button bg-white text-black fw-bold text-uppercase border-0 small-text py-2"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#activities"
                                    aria-expanded="true">
                                    Activities
                                </button>
                            </h2>
                            <div id="activities"
                                class="accordion-collapse collapse <?php echo !empty($selected_activities) ? 'show' : ''; ?>">
                                <div class="accordion-body">
                                    <?php 
                                    $activities = ["Hiking", "Camping", "Skiing", "Surfing", "Fishing", "Snorkeling", "Scuba Diving", "Horse Riding", 
                                                   "Paragliding", "Hot Air Balloon", "Cycling", "Wildlife Safari", "Rock Climbing", "Kayaking", "Rafting", 
                                                   "Desert Safari", "Snowboarding", "Mountaineering", "Bird Watching"];
                                    
                                    foreach ($activities as $activity) {
                                        $checked = in_array($activity, $selected_activities) ? 'checked' : '';
                                        echo "<label><input type='checkbox' name='activities[]' value='$activity' $checked> $activity</label><br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-1 mb-2 border-dark">

                        <div class="accordion-item border-0 bg-white">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button bg-white text-black fw-bold text-uppercase border-0 small-text py-2"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#departureDate"
                                    aria-expanded="true">
                                    Departure Date
                                </button>
                            </h2>
                            <div id="departureDate"
                                class="accordion-collapse collapse <?php echo !empty($selected_departure_date) ? 'show' : ''; ?>">
                                <div class="accordion-body">
                                    <input type="date" name="departure_date" class="form-control text-black"
                                        value="<?= htmlspecialchars($selected_departure_date) ?>">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-1 mb-2 border-dark">

                        <div class="accordion-item border-0 bg-white">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button bg-white text-black fw-bold text-uppercase border-0 small-text py-2"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#tripDuration"
                                    aria-expanded="true">
                                    Trip Length
                                </button>
                            </h2>
                            <div id="tripDuration"
                                class="accordion-collapse collapse <?php echo !empty($selected_duration) ? 'show' : ''; ?>">
                                <div class="accordion-body">
                                    <?php 
                                    $durations = ["1-3" => "1-3 Days", "4-7" => "4-7 Days", "8+" => "8+ Days"];
                                    foreach ($durations as $key => $label) {
                                        $checked = in_array($key, $selected_duration) ? 'checked' : '';
                                        echo "<label><input type='checkbox' name='duration[]' value='$key' $checked> $label</label><br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-1 mb-2 border-dark">

                        <div class="accordion-item border-0 bg-white">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button bg-white text-black fw-bold text-uppercase border-0 small-text py-2"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#transportation"
                                    aria-expanded="true">
                                    Transportation
                                </button>
                            </h2>
                            <div id="transportation"
                                class="accordion-collapse collapse <?php echo !empty($selected_transportation) ? 'show' : ''; ?>">
                                <div class="accordion-body">
                                    <?php 
                                    $transport_options = ["Bus", "Train", "Ship", "Microbus", "Airplane"];
                                    foreach ($transport_options as $option) {
                                        $checked = in_array($option, $selected_transportation) ? 'checked' : '';
                                        echo "<label><input type='checkbox' name='transportation[]' value='$option' $checked> $option</label><br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-1 mb-2 border-dark">

                        <div class="accordion-item border-0 bg-white">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button bg-white text-black fw-bold text-uppercase border-0 small-text py-2"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#priceRange"
                                    aria-expanded="true">
                                    Price
                                </button>
                            </h2>
                            <div id="priceRange"
                                class="accordion-collapse collapse <?php echo (!empty($selected_price_from) || !empty($selected_price_to)) ? 'show' : ''; ?>">
                                <div class="accordion-body">
                                    <label>From:</label>
                                    <input type="number" name="price_from" class="form-control text-black mb-2"
                                        placeholder="Min Price" value="<?= htmlspecialchars($selected_price_from) ?>">
                                    <label>To:</label>
                                    <input type="number" name="price_to" class="form-control text-black"
                                        placeholder="Max Price" value="<?= htmlspecialchars($selected_price_to) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-9 p-4">
                <?php include 'search.php'; ?>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . 'template/footer.php'; ?>
</body>