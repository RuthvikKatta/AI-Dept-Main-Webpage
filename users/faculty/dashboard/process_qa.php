<?php 

session_start();

include '../../models/Mentoring.php';

$Mentoring = new Mentoring();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $mentor_id = $_GET['mentor_id'];
    $mentee_id = $_GET['mentee_id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    $qId = $Mentoring->addQuestion($mentor_id, $mentee_id, $question);
    $Mentoring->addAnswer($mentor_id, $mentee_id, $answer, $qId);

    header("Location: ./dashboard.php#mentoring");
    exit();  
}