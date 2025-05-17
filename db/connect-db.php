<?php
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'tourism_management_system';

    $conn = mysqli_connect($host, $user, $pass, $db);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>