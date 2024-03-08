<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Subject.php';

$subject = new Subject();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Add Subject</title>
</head>

<body>
    <h2>Add Subject</h2>
    <a href="../dashboard.php#view-subjects" class='btn-back'>Back to dashboard</a>
    <form method="POST">
        <label for='subject_id'>Subject Id: </label>
        <input type='text' name='subject_id' id='subject_id' required>

        <label for='name'>Subject Name: </label>
        <input type='text' name='name' id='name' required>

        <label for='type'>Subject Type: </label>
        <select id='type' name='type' required>
            <option value='Theory'>Theory</option>
            <option value='Lab'>Lab</option>
        </select>

        <label for='credits'>Credits: </label>
        <input type='number' name='credits' id='credits' required>

        <input type='submit' name='add-subject' value='Add Subject'>
    </form>
    <?php
    if (isset($_POST['add-subject'])) {
        $subjectData = [
            'subject_id' => $_POST['subject_id'],
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'credits' => $_POST['credits'],
        ];

        $success = $subject->addSubject($subjectData);

        $message = $success === true ? "Subject added successfully" : $success;

        echo "
        <script>
            alert('$message');
            window.location.href = '../dashboard.php#view-subjects';
        </script>";
    }
    ?>
</body>

</html>