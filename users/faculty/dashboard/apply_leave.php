<?php

session_start();

include '../../models/Leave.php';

$leave = new Leave();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appliedBy = $_SESSION['facultyId'];
    $appliedFrom = $_POST['fromDate'];
    $appliedTo = $_POST['toDate'];
    $totalDays = $_POST['totalDays'];
    $reason = $_POST['reason'];

    $leaveId = $leave->addRecord($appliedBy, $appliedFrom, $appliedTo, $totalDays, $reason);
    
    $adjustment_dates = $_POST['adjustment_date'];
    $adjustment_years = $_POST['adjustment_year'];
    $adjustment_sections = $_POST['adjustment_section'];
    $adjustment_subjects = $_POST['adjustment_subject'];
    $adjustment_hours = $_POST['adjustment_hour'];
    $adjustment_faculty = $_POST['adjustment_faculty'];

    for ($i = 0; $i < count($adjustment_dates); $i++) {
        $adjustment_data = array(
            'date' => $adjustment_dates[$i],
            'year' => $adjustment_years[$i],
            'section' => $adjustment_sections[$i],
            'subject' => $adjustment_subjects[$i],
            'hour' => $adjustment_hours[$i],
            'faculty' => $adjustment_faculty[$i]
        );

        $leave->addLeaveAdjustment($leaveId, $adjustment_data);
    }
}

header("Location: ./dashboard.php#leave");
exit();

?>