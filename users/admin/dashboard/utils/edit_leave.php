<?php

include '../../../models/Leave.php';

$leave = new Leave();

$leaveId = $_GET['id'];
$status = $_GET['status'];

if (isset($leaveId) && isset($status)) {
    $message = '';
    if ($status == 1) {
        $message = $leave->updateLeaveStatus($leaveId, 'Approved');
    } else if ($status == 2) {
        $message = $leave->updateLeaveStatus($leaveId, 'Rejected');
    }
    echo "
        <script>
            alert('$message');
            window.location.href = '../dashboard.php#view-leaves';
        </script>";
}

header("Location: ../dashboard.php#view-leaves");
exit();
?>