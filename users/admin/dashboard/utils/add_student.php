<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Student.php';
include '../../../models/User.php';

$student = new Student();
$user = new User();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Student Add Form</title>
</head>

<body>
    <h2>New Student Form</h2>
    <a href="../dashboard.php#view-students" class='btn-back'>Back to dashboard</a>
    <form method="POST" enctype='multipart/form-data'>
        <label for='student_id'>Student ID: </label>
        <input type='text' id='student_id' name='student_id'>

        <label for='salutation'>Salutation: </label>
        <select id='salutation' name='salutation'>
            <option value='Mr'>Mr</option>
            <option value='Ms'>Ms</option>
            <option value='Mrs'>Mrs</option>
        </select>

        <label for='first_name'>First Name: </label>
        <input type='text' name='first_name' id='first_name'>

        <label for='middle_name'>Middle Name: </label>
        <input type='text' name='middle_name' id='middle_name'>

        <label for='last_name'>Last Name: </label>
        <input type='text' name='last_name' id='last_name'>

        <label for='gender'>Gender: </label>
        <select id='gender' name='gender'>
            <option value='Male'>Male</option>
            <option value='Female'>Female</option>
            <option value='Other'>Other</option>
        </select>

        <label for='date_of_birth'>Date of Birth: </label>
        <input type='date' name='date_of_birth' id='date_of_birth'>

        <label for='email'>Email: </label>
        <input type='text' name='email' id='email'>

        <label for='year'>Year: </label>
        <select id='year' name='year'>
            <option value='I'>I</option>
            <option value='II'>II</option>
            <option value='III'>III</option>
            <option value='IV'>IV</option>
        </select>

        <label for='section'>Section: </label>
        <select id='section' name='section'>
            <option value='A'>A</option>
            <option value='B'>B</option>
        </select>

        <label for='new_profile_picture'>Profile Picture(Only JPG/JPEG): </label>
        <input type='file' name='new_profile_picture' id='new_profile_picture'>

        <input type='submit' name='add-student' value='Add Student'>
    </form>
    <?php
    if (isset($_POST['add-student'])) {
        $studentId = $_POST['student_id'];
        $studentDetails = array(
            'student_id' => $_POST['student_id'],
            'first_name' => $_POST['first_name'],
            'middle_name' => $_POST['middle_name'],
            'last_name' => $_POST['last_name'],
            'salutation' => $_POST['salutation'],
            'gender' => $_POST['gender'],
            'date_of_birth' => $_POST['date_of_birth'],
            'email' => $_POST['email'],
            'year' => $_POST['year'],
            'section' => $_POST['section'],
        );

        if (isset($_FILES["new_profile_picture"]) && $_FILES["new_profile_picture"]["error"] == 0) {
            $message = "";
            $allowedTypes = ["jpg", "jpeg"];
            $fileType = strtolower(pathinfo($_FILES["new_profile_picture"]["name"], PATHINFO_EXTENSION));

            if (!in_array($fileType, $allowedTypes)) {
                $message = "Image Upload Failed. Image should be JPG/JPEG.";
            } else if ($_FILES["new_profile_picture"]["size"] > 1 * 1024 * 1024) {
                $message = "Image Upload Failed. Image Size greater than 1MB.";
            } else {
                $oldImagePath = "../../../../Database/Student/{$studentId}.jpg";
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                $fileName = $studentId . "." . $fileType;
                if (move_uploaded_file($_FILES["new_profile_picture"]["tmp_name"], "../../../../Database/Student/" . $fileName)) {
                    $success = $student->addStudent($studentDetails);
                    $user->createUser($studentId, $studentId, 'Student');
                    $message = $success ? "Added Successfully" : "Adding Failed";
                }
            }
        } else {
            $success = $student->addStudent($studentDetails);
            $user->createUser($studentId, $studentId, 'Student');
            $message = $success ? "Added Successfully" : "Adding Failed";
        }
        echo "
            <script>
                alert('$message');
                window.location.href = '../dashboard.php#view-students';
            </script>";
    }
    ?>
</body>

</html>