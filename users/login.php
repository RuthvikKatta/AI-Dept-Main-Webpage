<?php

session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    if (isset($_SESSION['adminId'])) {
        header("Location: ./admin/dashboard/dashboard.php");
        exit();
    } elseif (isset($_SESSION['facultyId'])) {
        header("Location: ./faculty/dashboard/dashboard.php");
        exit();
    } elseif (isset($_SESSION['studentId'])) {
        header("Location: ./student/dashboard/dashboard.php");
        exit();
    }
}

include './models/User.php';

$user = new User();

$message = "";

if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    $status = $user->exists($username, $password, $role);

    switch ($status) {
        case 'SUCCESS':
            if ($role === 'Admin') {
                $_SESSION['adminId'] = $username;
            } elseif ($role === 'Faculty') {
                $_SESSION['facultyId'] = $username;
            } elseif ($role === 'Student') {
                $_SESSION['studentId'] = $username;
            }
            $_SESSION['adminId'] = $username;
            $_SESSION['loggedIn'] = true;
            $_SESSION['role'] = $role;
            header("Location: ./$role/dashboard/dashboard.php");
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

    <link rel="stylesheet" href="./login.style.css">
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../assets/images/favicon-icon.png" type="image/x-icon">
</head>

<body>

    <div class="login-container">
        <form method="post">
            <div class="login-card">
                <div class="login-content">
                    <p class='title'>Login</p>
                    <div class="input-group">
                        <div>
                            <input type="text" name="username" class="input-field" autofocus required />
                            <label>Username</label>
                        </div>
                        <div>
                            <input type="password" name="password" class="input-field" required />
                            <label>Password</label>
                        </div>
                            <select name="role" id="role" class="input-field" required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="Faculty">Faculty</option>
                                <option value="Student">Student</option>
                            </select>
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