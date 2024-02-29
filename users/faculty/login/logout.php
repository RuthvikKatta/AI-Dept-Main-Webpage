<?php
session_start();

if ($_SESSION['loggedIn'] === true && isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: /AI-Main-Page/ai-department/index.html");
    exit();
}

?>