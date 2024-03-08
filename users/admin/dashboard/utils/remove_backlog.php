<?php

include '../../../models/Marks.php';

$marks = new Marks();

$recordId = $_GET['id'];

if (isset($recordId)) {
    $marks->removeRecord($recordId);
}

header("Location: ../dashboard.php#view-backlogs");
exit();

?>