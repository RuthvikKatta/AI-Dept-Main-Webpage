<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Marks.php';
include '../../../models/Subject.php';

$marks = new Marks();
$subject = new Subject();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Backlogs Add Form</title>
</head>

<body>
    <a href="../dashboard.php#view-backlogs" class='btn-back'>Back to dashboard</a>
    <form method='post'>
        <label for="student">Enter Student Id: </label>
        <input type="text" name="student" id="student" required>

        <label for="subject">Choose Subject: </label>
        <select name="subject" id="subject">
            <option value="">Choose Subject</option>
            <?php
            $rows = $subject->getAllSubjects();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    echo "<option value=" . $row['subject_id'] . ">" . $row['name'] . "</option>";
                }
            }
            ?>
        </select>

        <label for="year">Select Year of Backlog Subject: </label>
        <select id="year" name="year" required>
            <option value="">Select Year</option>
            <option value="I">I</option>
            <option value="II">II</option>
            <option value="III">III</option>
            <option value="IV">IV</option>
            ?>
        </select>
        
        <label for="semester">Select Semester of Backlog Subject</label>
        <select id="semester" name="semester" required>
            <option value="">Select Semester</option>
            <option value="1">1</option>
            <option value="2">2</option>
            ?>
        </select>

        <input type='submit' name='submit-records' value='Submit' class='upload-button'>
    </form>
    
    <?php
    if (isset($_POST['submit-records'])) {
        $recordDetails[] = array(
            'student_id' => $_POST['student'],
            'subject_id' => $_POST['subject'],
            'year' => $_POST['year'],
            'semester' => $_POST['semester']
        );

        $success = $marks->addBacklogRecords($recordDetails);
        $message = $success ? "Added Succesfully" : "Adding Failed";

        echo "
            <script>
                alert('$message');
                window.location.href = '../dashboard.php#view-backlogs';
            </script>";
    }
    ?>
</body>
<script>

</script>

</html>