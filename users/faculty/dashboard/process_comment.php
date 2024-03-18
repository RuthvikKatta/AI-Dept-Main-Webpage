<?php 

session_start();

include '../../models/Mentoring.php';

$Mentoring = new Mentoring();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $mentor_id = $_GET['mentor_id'];
    $mentee_id = $_GET['mentee_id'];
    $comment = $_POST['newComment'];

    $Mentoring->addMentorComment($mentor_id, $mentee_id, $comment);
    header("Location: ./dashboard.php#mentoring");
    exit();  
}