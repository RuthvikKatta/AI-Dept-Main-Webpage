<?php
include '../../models/Media.php';

$media = new Media();

$message = "";
if (isset($_FILES["image"]) && isset($_POST['submit-image'])) {
    $allowedTypes = ["jpg", "jpeg"];
    $fileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($fileType, $allowedTypes)) {
        $message = "Image Upload Failed. Image should be JPEG.";
    } else if ($_FILES["image"]["size"] > 1 * 1024 * 1024) {
        $message = "Image Upload Failed. Image Size greater than 1MB.";
    } else {
        $fileName = time() . "." . $fileType;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], "../../../Database/Carousal Images/" . $fileName)) {
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
}

echo "<script>
    alert('$message');
    window.location.href = './dashboard.php#view-media';
</script>";
?>