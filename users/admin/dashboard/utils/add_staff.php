<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Staff.php';
include '../../../models/User.php';

$staff = new Staff();
$user = new User();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Staff Add Form</title>
</head>

<body>
    <h2>New Staff Form</h2>
    <a href="../dashboard.php#view-staff" class='btn-back'>Back to dashboard</a>
    <form method="POST" enctype='multipart/form-data'>
        <label for='staff_id'>Staff ID: </label>
        <input type='text' id='staff_id' name='staff_id'>

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

        <label for='qualification'>Qualification: </label>
        <input type='text' name='qualification' id='qualification'>

        <label for='experience_years'>Experience Years: </label>
        <input type='text' name='experience_years' id='experience_years'>

        <label for='designation_id'>Designation: </label>
        <select id='designation_id' name='designation_id'>
            <?php
            $designations = $staff->getAllDesignations();
            foreach ($designations as $designation) {
                echo "<option value='{$designation['designation_id']}'>{$designation['title']}</option>";
            }
            ?>
        </select>

        <label for='role'>Role: </label>
        <select id='role' name='role'>
            <option value='Teaching'>Teaching</option>
            <option value='Non Teaching'>Non Teaching</option>
        </select>

        <label for='gender'>Gender: </label>
        <select id='gender' name='gender'>
            <option value='Male'>Male</option>
            <option value='Female'>Female</option>
            <option value='Other'>Other</option>
        </select>

        <label for='age'>Age: </label>
        <input type='number' name='age' id='age'>

        <label for='mobile_number'>Mobile Number: </label>
        <input type='text' name='mobile_number' id='mobile_number'>

        <label for='alternative_mobile_number'>Alternative Mobile Number: </label>
        <input type='text' name='alternative_mobile_number' id='alternative_mobile_number'>

        <label for='email'>Email: </label>
        <input type='text' name='email' id='email'>

        <label>Profile Picture(Only JPG/JPEG AND < 1MB): </label>
        <input type='file' name='new_profile_picture' id='new_profile_picture'>

        <input type='submit' name='add-staff' value='Add Staff'>
    </form>
    <?php
    if (isset($_POST['add-staff'])) {
        $staffId = $_POST['staff_id'];
        $newStaffDetails = array(
            'staff_id' => $_POST['staff_id'],
            'salutation' => $_POST['salutation'],
            'first_name' => $_POST['first_name'],
            'middle_name' => $_POST['middle_name'],
            'last_name' => $_POST['last_name'],
            'qualification' => $_POST['qualification'],
            'experience_years' => $_POST['experience_years'],
            'designation_id' => $_POST['designation_id'],
            'role' => $_POST['role'],
            'gender' => $_POST['gender'],
            'age' => $_POST['age'],
            'mobile_number' => $_POST['mobile_number'],
            'alternative_mobile_number' => $_POST['alternative_mobile_number'],
            'email' => $_POST['email'],
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
                $fileName = $_POST['staff_id'] . "." . $fileType;
                if (move_uploaded_file($_FILES["new_profile_picture"]["tmp_name"], "../../../../Database/Staff/" . $fileName)) {
                    $success = $staff->addStaff($newStaffDetails);
                    $user->createUser($staffId, $staffId, 'Faculty');
                    $message = $success ? "Staff Added Successfully" : "Staff Addition Failed";
                }
            }
        } else {
            $success = $staff->addStaff($newStaffDetails);
            $user->createUser($staffId, $staffId, 'Faculty');
            $message = $success ? "Staff Added Successfully" : "Staff Addition Failed";
        }

        echo "<script>
                alert('$message');
                window.location.href = '../dashboard.php#view-staff';
              </script>";
    }
    ?>
</body>

</html>