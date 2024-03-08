<?php 

include '../../../models/ClassDetails.php';

$classDetails = new ClassDetails();

$classId = $_GET['class_id'];
$subjectId = $_GET['subject_id'];
$taughtBy = $_GET['taught_by'];

if(isset($classId) && isset($subjectId) && isset($taughtBy)){
    $classDetails->removeSubjectsOfClass($classId, $subjectId, $taughtBy); 
}

header("Location: ./edit_classdetails.php?id=$classId");
exit();

?>