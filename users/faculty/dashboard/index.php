<?php

session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    if (isset($_SESSION['facultyId'])) {
        header("Location: ./dashboard.php");
        exit();
    }
} else {
    header("Location: ../login/login.php");
    exit();
}