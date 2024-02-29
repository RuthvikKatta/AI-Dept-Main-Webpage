<?php

session_start();

include '../../models/User.php';

$user = new User();

$message = "";

if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = 'Admin';

    $status = $user->exists($username, $password, $role);

    switch ($status) {
        case 'SUCCESS':
            $_SESSION['adminId'] = $username;
            $_SESSION['loggedIn'] = true;
            $_SESSION['role'] = 'admin';
            header("Location: ../dashboard/dashboard.php");
            exit();

        case 'USERNAME_NOT_EXIST':
            $message = 'User does not exist!';
            break;

        case 'PASSWORD_NOT_MATCH':
            $message = 'Incorrect Password.';
            break;

        case 'SERVER_ERROR':
            $message = 'Internal Ser Error.';
            break;

        default:
            $message = 'Unknown Error Occurred.';
    }

}

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Page</title>

    <link rel="stylesheet" href="../../models/login.style.css">
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../assets/images/favicon-icon.png" type="image/x-icon">
</head>

<body>

    <div class="login-container">
        <form method="post">
            <div class="login-card">
                <div class="login-content">
                    <p class='title'>Admin Login</p>
                    <div class="input-group">
                        <div>
                            <input type="text" name="username" class="input-field" autofocus required />
                            <label>Username</label>
                        </div>
                        <div>
                            <input type="password" name="password" class="input-field" required />
                            <label>Password</label>
                        </div>
                    </div>
                    <?php echo '<p class="error-message">' . $message . '</p>' ?>
                </div>
                <div class="login-footer">
                    <input type="submit" name="login" value="Login" class="login-button">
                </div>
            </div>
        </form>
    </div>

</body>

</html>