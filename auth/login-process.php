<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php';
    include ROOT_PATH . 'db/connect-db.php';
    include ROOT_PATH . 'auth/connect-session.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $tourist_query = "SELECT * FROM tourists WHERE email = '$email'";
        $tourist_result = mysqli_query($conn, $tourist_query);

        $staff_query = "SELECT * FROM staffs WHERE email = '$email'";
        $staff_result = mysqli_query($conn, $staff_query);

        if (mysqli_num_rows($tourist_result) > 0 && mysqli_num_rows($staff_result) > 0) {
            $_SESSION['error'] = "The email address is associated with both a tourist and a staff account.";
            header("Location: login.php");
            exit();
        }

        if (mysqli_num_rows($tourist_result) > 0) {
            $user = mysqli_fetch_assoc($tourist_result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['email'];
                header("Location: " . BASE_URL . "tourist/index.php");
                exit();
            } else {
                $_SESSION['error'] = "Invalid password.";
                header("Location: login.php");
                exit();
            }
        }

        if (mysqli_num_rows($staff_result) > 0) {
            $staff = mysqli_fetch_assoc($staff_result);
            if (password_verify($password, $staff['password'])) {
                $_SESSION['user'] = $staff['email'];
                header("Location: " . BASE_URL . "admin/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Invalid password.";
                header("Location: login.php");
                exit();
            }
        }

        $_SESSION['error'] = "No account exists for the given email.";
        header("Location: login.php");
        exit();
    }
?>
