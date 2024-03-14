<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Staff.php';

$staff = new Staff();

$staffId = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Staff Edit Form</title>
</head>

<body>
    <h2>Staff Details</h2>
    <a href="../dashboard.php#view-staff" class='btn-back'>Back to dashboard</a>
    <form method="POST" enctype='multipart/form-data'>
        <?php
        $staffDetails = $staff->getStaffDetails($staffId);
        if (count($staffDetails) > 0) {
            ?>
            <label for='staff_id'>Staff ID: </label>
            <input type='text' id='staff_id' value='<?php echo $staffDetails['staff_id']; ?>' readonly>

            <label for='salutation'>Salutation: </label>
            <select id='salutation' name='salutation'>
                <option value='Mr' <?php echo ($staffDetails['salutation'] == 'Mr') ? 'selected' : ''; ?>>Mr</option>
                <option value='Ms' <?php echo ($staffDetails['salutation'] == 'Ms') ? 'selected' : ''; ?>>Ms</option>
                <option value='Mrs' <?php echo ($staffDetails['salutation'] == 'Mrs') ? 'selected' : ''; ?>>Mrs</option>
            </select>

            <label for='first_name'>First Name: </label>
            <input type='text' name='first_name' id='first_name' value='<?php echo $staffDetails['first_name']; ?>'>

            <label for='middle_name'>Middle Name: </label>
            <input type='text' name='middle_name' id='middle_name' value='<?php echo $staffDetails['middle_name']; ?>'>

            <label for='last_name'>Last Name: </label>
            <input type='text' name='last_name' id='last_name' value='<?php echo $staffDetails['last_name']; ?>'>

            <label for='qualification'>Qualification: </label>
            <input type='text' name='qualification' id='qualification'
                value='<?php echo $staffDetails['qualification']; ?>'>

            <label for='role'>Role: </label>
            <select id='role' name='role'>
                <option value='Teaching' <?php echo ($staffDetails['role'] == 'Teaching') ? 'selected' : ''; ?>>Teaching
                </option>
                <option value='Non Teaching' <?php echo ($staffDetails['role'] == 'Non Teaching') ? 'selected' : ''; ?>>Non
                    Teaching</option>
            </select>

            <label for='designation_id'>Designation: </label>
            <select id='designation_id' name='designation_id'>
                <?php
                $designations = $staff->getAllDesignations();
                foreach ($designations as $d) {
                    echo "<option value='{$d['designation_id']}' " . ($staffDetails['designation_id'] == $d['designation_id'] ? 'selected' : '') . ">{$d['title']}</option>";
                }
                ?>
            </select>

            <label for='experience_years'>Experience Years: </label>
            <input type='text' name='experience_years' id='experience_years'
                value='<?php echo $staffDetails['experience_years']; ?>'>

            <label for='gender'>Gender: </label>
            <select id='gender' name='gender'>
                <option value='Male' <?php echo ($staffDetails['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value='Female' <?php echo ($staffDetails['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value='Other' <?php echo ($staffDetails['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <label for='age'>Age: </label>
            <input type='number' name='age' id='age' value='<?php echo $staffDetails['age']; ?>'>

            <label for='mobile_number'>Mobile Number: </label>
            <input type='text' name='mobile_number' id='mobile_number'
                value='<?php echo $staffDetails['mobile_number']; ?>'>

            <label for='alternative_mobile_number'>Alternative Mobile Number: </label>
            <input type='text' name='alternative_mobile_number' id='alternative_mobile_number'
                value='<?php echo $staffDetails['alternative_mobile_number']; ?>'>

            <label for='email'>Email: </label>
            <input type='text' name='email' id='email' value='<?php echo $staffDetails['email']; ?>'>

            <label>Profile Picture: </label>
            <?php
            $imageExtensions = ['jpg', 'jpeg'];
            foreach ($imageExtensions as $extension) {
                $imagePath = "../../../../Database/Staff/{$staffId}.{$extension}";
                if (file_exists($imagePath)) {
                    echo "<img src='$imagePath' alt='Profile Picture' width='100'>";
                    break;
                }
            } ?>

            <label for='new_profile_picture'>New Profile Picture(Only JPG): </label>
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
            'qualification' => $_POST['qualification'],
            'role' => $_POST['role'],
            'designation_id' => $_POST['designation_id'],
            'experience_years' => $_POST['experience_years'],
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
                foreach ($allowedTypes as $allowedType) {
                    $oldImagePath = "../../../../Database/Staff/{$staffId}.{$allowedType}";
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                        break;
                    }
                }
                $fileName = $staffId . "." . $fileType;
                if (move_uploaded_file($_FILES["new_profile_picture"]["tmp_name"], "../../../../Database/Staff/" . $fileName)) {
                    $success = $staff->updateStaffDetails($staffId, $updatedDetails);
                    $message = $success ? "Updated Successfully" : "Update Failed";
                }
            }
        } else {
            $success = $staff->updateStaffDetails($staffId, $updatedDetails);
            $message = $success ? "Updated Successfully" : "Update Failed";
        }
        echo "
            <script>
                alert('$message');
                window.location.href = '../dashboard.php#view-staff';
            </script>";
    }
    ?>
</body>

</html>