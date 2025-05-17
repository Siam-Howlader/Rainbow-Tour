<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php';
    include ROOT_PATH . 'db/connect-db.php';
    include ROOT_PATH . 'auth/connect-session.php';

    $schedule_id = $_POST['schedule_id'];
    $tourist_id = $_POST['tourist_id'] ?? '';

    if (empty($tourist_id)) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit;
    }

    $persons = $_POST['persons'];
    $price = $_POST['price'] * $persons;
    $payment_method = $_POST['payment_method'];

    $query_booking = "INSERT INTO bookings (tourist_id, schedule_id, persons, status, timestamp) 
                    VALUES ('$tourist_id', '$schedule_id', '$persons', 'Pending', NOW())";

    if (mysqli_query($conn, $query_booking)) {
        $booking_id = mysqli_insert_id($conn);
        $query_payment = "INSERT INTO payments (booking_id, method, status, amount, timestamp) 
                        VALUES ('$booking_id', '$payment_method', 'Pending', '$price', NOW())";

        if (mysqli_query($conn, $query_payment)) {
            $previous_page = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header("Location: $previous_page");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
?>
