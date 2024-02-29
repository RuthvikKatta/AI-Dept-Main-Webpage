<?php
session_start();

$inactive = 600;

$session_life = time() - $_SESSION['timeout'];

if ($session_life > $inactive) {
    session_destroy();
    header("Location: index.php");
}

$_SESSION['timeout'] = time();