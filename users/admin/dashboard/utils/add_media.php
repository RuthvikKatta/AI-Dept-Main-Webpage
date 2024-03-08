<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Media.php';

$media = new Media();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Media Add Form</title>
</head>

<body>
    <h2>Media Form</h2>
    <a href="../dashboard.php#view-media" class='btn-back'>Back to dashboard</a>
    <form method='post' enctype='multipart/form-data'>
        <h2 class='form-title'>Media Adding form</h2>
        <label>Choose Image</label>
        <input type='file' name='image' required class='form-control'>
        <input type='submit' name='submit-media' value='Upload' class='upload-button'>
    </form>

    <?php
    $message = "";
    if (isset($_POST['submit-media'])) {
        $allowedTypes = ["jpg", "jpeg"];
        $fileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

        list($width, $height) = getimagesize($_FILES["image"]["tmp_name"]);
        $aspectRatio = $width / $height;

        if (!in_array($fileType, $allowedTypes)) {
            $message = "Image Upload Failed. Image should be JPEG.";
        } else if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
            $message = "Image Upload Failed. Image Size greater than 5MB.";
        } else if(abs($aspectRatio - (16 / 9)) > 0.01) {
            $message = "Image Upload Failed. Image Should be 16:9 Aspect Ratio";
        } else {
            $fileName = time() . "." . $fileType;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "../../../../Database/Carousal Images/" . $fileName)) {
                $success = $media->addCarousalImage($fileName);
                if ($success) {
                    $message = "Image Upload Successfully.";
                } else {
                    $message = "Image Upload Failed. Try Again.";
                }
            } else {
                $message = "Image Upload Failed. Try Again.";
            }
        }

        echo "
        <script>
            alert('$message');
            window.location.href = '../dashboard.php#view-media';
        </script>";
    }
    ?>
</body>

</html>