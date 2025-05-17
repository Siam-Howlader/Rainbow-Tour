<?php
    $logged_in = isset($_SESSION['user']) ? true : false;

    $user_email = $logged_in ? $_SESSION['user'] : '';
    $query = "SELECT * FROM staffs WHERE email = '$user_email'";
    $result = mysqli_query($conn, $query);
    $admin = mysqli_fetch_assoc($result);

    $is_admin = !$admin ? false : true;
?>