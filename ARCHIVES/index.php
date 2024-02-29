<?php

session_start();

$role = isset($_GET['role']) ? $_GET['role'] : '';

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <link rel="stylesheet" href="./login.style.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../assets/images/favicon-icon.png" type="image/x-icon">
</head>

<body>

    <div class="login-container">
        <form action="./login.php?role=<?php echo $role ?>" method="post">
            <div class="login-card">
                <div class="login-content">
                    <?php echo "<p class='title'>" . ucfirst($role) . " Login</p>" ?>
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
                    <?php if (isset($_SESSION['message'])): ?>
                        <p class="error-message">
                            <?= $_SESSION['message']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="login-footer">
                    <button type="submit" class="login-button">
                        Login
                    </button>
                </div>
            </div>
        </form>
    </div>

</body>

</html>