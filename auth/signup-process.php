<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php';
    include ROOT_PATH . 'db/connect-db.php';
    include ROOT_PATH . 'auth/connect-session.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $nid = mysqli_real_escape_string($conn, $_POST['nid']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: signup.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $email_query = "SELECT email FROM tourists WHERE email = '$email' UNION SELECT email FROM staffs WHERE email = '$email'";
        $email_result = mysqli_query($conn, $email_query);

        if ($email_result && mysqli_num_rows($email_result) > 0) {
            $_SESSION['error'] = "An account with this email already exists.";
            header("Location: signup.php");
            exit();
        }

        $nid_query = "SELECT * FROM tourists WHERE nid = '$nid'";
        $nid_result = mysqli_query($conn, $nid_query);

        if ($nid_result && mysqli_num_rows($nid_result) > 0) {
            $_SESSION['error'] = "An account with this NID already exists.";
            header("Location: signup.php");
            exit();
        }

        $joining_date = date("Y-m-d");

        if (isset($_POST['is_staff']) && $_POST['is_staff'] == 'on') {
            $insert_query = "INSERT INTO staffs (name, nid, email, dob, phone, address, password, role, joining_date) 
                             VALUES ('$name', '$nid', '$email', '$dob', '$phone', '$address', '$hashed_password', 'Guide', '$joining_date')";
        } else {
            $insert_query = "INSERT INTO tourists (name, nid, email, dob, phone, address, password) 
                             VALUES ('$name', '$nid', '$email', '$dob', '$phone', '$address', '$hashed_password')";
        }

        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['success'] = "Registration successful. Please log in.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "There was an error registering your account. Please try again.";
            header("Location: signup.php");
            exit();
        }
    }
?>
