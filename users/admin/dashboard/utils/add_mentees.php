<?php
include "../../../models/Mentoring.php";

$mentoring = new Mentoring();

$mentorId = isset($_POST['mentor_id']) ? $_POST['mentor_id'] : '';

if (isset($_POST['add-mentees'])) {

    $menteeIds = isset($_POST['newMenteeIds']) ? array_map('trim', $_POST['newMenteeIds']) : [];

    if (empty($menteeIds) || in_array('', $menteeIds)) {
        $message = "Error: Mentee IDs cannot be empty.";
    } else {
        $message = $mentoring->assignMentees($mentorId, $menteeIds);
    }

    echo "<script>
            alert(`$message`);
            window.location.href = './edit_mentees.php?id=$mentorId';
        </script>";
}

if ($mentorId == '') {
    header("Location: ../dashboard.php#view-mentoring");
    exit();
}
header("Location: ./edit_mentees.php?id=$mentorId");
exit();
?>