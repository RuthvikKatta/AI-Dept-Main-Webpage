<?php

session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    if (isset($_SESSION['adminId'])) {
        header("Location: ./dashboard.php#profile");
        exit();
    }
} else {
    header("Location: ../login/login.php");
    exit();
}