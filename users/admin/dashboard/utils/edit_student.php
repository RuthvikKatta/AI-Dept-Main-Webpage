<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Student.php';

$student = new Student();

$studentId = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Student Edit Form</title>
</head>

<body>
    <h2>Student Details</h2>
    <a href="../dashboard.php#view-students" class='btn-back'>Back to dashboard</a>
    <form method="POST" enctype='multipart/form-data'>
        <?php
        $studentDetails = $student->getStudentDetails($studentId);
        if (count($studentDetails) > 0) {
            ?>
            <label for='student_id'>Student ID: </label>
            <input type='text' id='student_id' value='<?php echo $studentDetails['student_id']; ?>' readonly>

            <label for='salutation'>Salutation: </label>
            <select id='salutation' name='salutation'>
                <option value='Mr' <?php echo ($studentDetails['salutation'] == 'Mr') ? 'selected' : ''; ?>>Mr</option>
                <option value='Ms' <?php echo ($studentDetails['salutation'] == 'Ms') ? 'selected' : ''; ?>>Ms</option>
                <option value='Mrs' <?php echo ($studentDetails['salutation'] == 'Mrs') ? 'selected' : ''; ?>>Mrs</option>
            </select>

            <label for='first_name'>First Name: </label>
            <input type='text' name='first_name' id='first_name' value='<?php echo $studentDetails['first_name']; ?>'>

            <label for='middle_name'>Middle Name: </label>
            <input type='text' name='middle_name' id='middle_name' value='<?php echo $studentDetails['middle_name']; ?>'>

            <label for='last_name'>Last Name: </label>
            <input type='text' name='last_name' id='last_name' value='<?php echo $studentDetails['last_name']; ?>'>

            <label for='gender'>Gender: </label>
            <select id='gender' name='gender'>
                <option value='Male' <?php echo ($studentDetails['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value='Female' <?php echo ($studentDetails['gender'] == 'Female') ? 'selected' : ''; ?>>Female
                </option>
                <option value='Other' <?php echo ($studentDetails['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <label for='date_of_birth'>Date of Birth: </label>
            <input type='text' name='date_of_birth' id='date_of_birth'
                value='<?php echo $studentDetails['date_of_birth']; ?>'>

            <label for='email'>Email: </label>
            <input type='text' name='email' id='email' value='<?php echo $studentDetails['email']; ?>'>

            <label for='year'>Year: </label>
            <select id='year' name='year'>
                <option value='I' <?php echo ($studentDetails['year'] == 'I') ? 'selected' : ''; ?>>I</option>
                <option value='II' <?php echo ($studentDetails['year'] == 'II') ? 'selected' : ''; ?>>II</option>
                <option value='III' <?php echo ($studentDetails['year'] == 'III') ? 'selected' : ''; ?>>III</option>
                <option value='IV' <?php echo ($studentDetails['year'] == 'IV') ? 'selected' : ''; ?>>IV</option>
            </select>

            <label for='section'>Section: </label>
            <select id='section' name='section'>
                <option value='A' <?php echo ($studentDetails['section'] == 'A') ? 'selected' : ''; ?>>A</option>
                <option value='B' <?php echo ($studentDetails['section'] == 'B') ? 'selected' : ''; ?>>B</option>
            </select>

            <label>Profile Picture: </label>
            <?php
            $imageExtensions = ['jpg', 'jpeg'];
            foreach ($imageExtensions as $extension) {
                $imagePath = "../../../../Database/Student/{$studentId}.{$extension}";

                if (file_exists($imagePath)) {
                    echo "<img src='$imagePath' alt='Profile Picture' width='100'>";
                    break;
                }
            } ?>

            <label for='new_profile_picture'>New Profile Picture(JPG, JPEG AND < 1MB): </label>
            <input type='file' name='new_profile_picture' id='new_profile_picture'>

            <input type='submit' name='update-details' value='Update Details'>
            <?php
        }
        ?>
    </form>
    <?php
    if (isset($_POST['update-details'])) {
        $updatedDetails = array(
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
                    $success = $student->updateStudentDetails($studentId, $updatedDetails);
                    $message = $success ? "Updated Successfully" : "Update Failed";
                }
            }
        } else {
            $success = $student->updateStudentDetails($studentId, $updatedDetails);
            $message = $success ? "Updated Successfully" : "Update Failed";
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