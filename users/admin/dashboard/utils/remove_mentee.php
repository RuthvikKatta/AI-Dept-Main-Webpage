<?php 

include '../../../models/Mentoring.php';

$mentoring = new Mentoring();

$mentorId = $_GET['mentor_id'];
$menteeId = $_GET['mentee_id'];

if(isset($mentorId) && isset($menteeId)){
    $mentoring->removeMentees($mentorId, $menteeId); 
}

header("Location: ./edit_mentees.php?id=$mentorId");
exit();

?>