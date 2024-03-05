<?php
session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && isset($_GET['logout'])) {
    unset($_SESSION['adminId']);
}
header("Location: /AI-Main-Page/ai-department/index.php");
exit();

?>