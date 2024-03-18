<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Subject.php';

$subject = new Subject();

$subjectId = $_GET['id'];
if(empty($subjectId)) {
    header("Location: ../dashboard.php#view-subjects");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Subjects Edit Form</title>
</head>

<body>
    <h2>Subjects Details</h2>
    <a href="../dashboard.php#view-subjects" class='btn-back'>Back to dashboard</a>
    <form method="POST">
        <?php
        $subjectDetails = $subject->getSubjectDetails($subjectId);
        if (count($subjectDetails) > 0) {
            ?>
            <label for='subject_id'>Subject ID: </label>
            <input type='text' id='subject_id' value='<?php echo $subjectDetails['subject_id']; ?>' readonly>

            <label for='name'>Subject Name: </label>
            <input type='text' name='name' id='name' value='<?php echo $subjectDetails['name']; ?>'>

            <label for='type'>Subject Type: </label>
            <select id='type' name='type'>
                <option value='Theory' <?php echo ($subjectDetails['type'] == 'Theory') ? 'selected' : ''; ?>>Theory</option>
                <option value='Lab' <?php echo ($subjectDetails['type'] == 'Lab') ? 'selected' : ''; ?>>Lab
                </option>
            </select>

            <label for='credits'>Credits: </label>
            <input type='number' name='credits' id='credits' value='<?php echo $subjectDetails['credits']; ?>'>

            <input type='submit' name='update-details' value='Update Details'>
            <?php
        }
        ?>
    </form>
    <?php
    if (isset($_POST['update-details'])) {
        $updatedDetails = [
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'credits' => $_POST['credits'],
        ];

        $success = $subject->updateSubjectDetails($subjectId, $updatedDetails);

        $message = $success ? "Subject details updated successfully" : "Failed to update subject details";

        echo "
        <script>
            alert('$message');
            window.location.href = '../dashboard.php#view-subjects';
        </script>";
    }
    ?>
</body>

</html>