<?php
session_start();

if (isset($_SESSION['loggedIn']) && isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: /AI-Main-Page/ai-department/index.php");
    exit();
}