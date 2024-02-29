<?php

session_start();

include '../../models/Leave.php';

$Leave = new Leave();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $appliedby = $_SESSION['facultyId'];
    $appliedfrom = $_POST['fromDate'];
    $appliedto = $_POST['toDate'];
    $totaldays = $_POST['totalDays'];
    $reason = $_POST['reason'];
    $adjustedWith = $_POST['adjustedWith'];

    $Leave->addRecord($appliedby, $appliedfrom, $appliedto, $totaldays, $reason, $adjustedWith);
    
    header("Location: ./dashboard.php#leave");
    exit();
}