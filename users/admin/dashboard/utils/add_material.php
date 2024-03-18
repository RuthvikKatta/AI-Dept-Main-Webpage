<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Material.php';
include '../../../models/Subject.php';

$material = new Material();
$subject = new Subject();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Material Add Form</title>
</head>

<body>
    <h2>Material Form</h2>
    <a href="../dashboard.php#view-material" class='btn-back'>Back to dashboard</a>
    <form method='post' enctype="multipart/form-data">
        <label>Choose File Type:</label>
        <select name="material-type" id="material-type" required>
            <option value="">Choose Material Type</option>
            <option value="AC">Acadmeic Calendar</option>
            <option value="PQP">Previous Question Paper</option>
            <option value="SM">Subject Material</option>
        </select>
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
        <label>Choose File:</label>
        <input type='file' name='material' required class='form-control'>
        <input type='submit' name='submit-material' value='Submit' class='upload-button'>
    </form>
    <?php
    if (isset($_POST['submit-material'])) {
        $materialType = $_POST['material-type'];
        $subjectId = isset($_POST['subject']) ? $_POST['subject'] : null;

        $allowedTypes = ["pdf", "docx", "doc", "pptx", "ppt"];
        $fileType = strtolower(pathinfo($_FILES["material"]["name"], PATHINFO_EXTENSION));
        $fileType = strtolower($fileType);

        if (!in_array($fileType, $allowedTypes)) {
            $message = "File Upload Failed. Invalid File Format. File should be PDF/PPT/DOC.";
        } else if ($_FILES["material"]["size"] > 20 * 1024 * 1024) {
            $message = "File Upload Failed. File Size greater than 20MB.";
        } else {
            $fileName = basename($_FILES["material"]["name"]);
            if (move_uploaded_file($_FILES["material"]["tmp_name"], "../../../../Database/Material/" . $fileName)) {
                if ($materialType === 'AC') {
                    $success = $material->addMaterial($fileName, $materialType);
                } else {
                    $success = $material->addMaterial($fileName, $materialType, $subjectId);
                }
                if ($success) {
                    $message = "File Upload Successfully.";
                } else {
                    $message = "File Upload Failed. Try Again.";
                }
            } else {
                $message = "File Upload Failed. Try Again.";
            }
        }
        echo "
            <script>
                alert('$message');
                window.location.href = '../dashboard.php#view-students';
            </script>";
    }
    ?>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var materialTypeSelect = document.getElementById('material-type');
        var subjectSelect = document.getElementById('subject');

        materialTypeSelect.addEventListener('change', function () {
            if (materialTypeSelect.value === 'AC') {
                subjectSelect.disabled = true;
            } else {
                subjectSelect.disabled = false;
            }
        });
    });
</script>

</html>