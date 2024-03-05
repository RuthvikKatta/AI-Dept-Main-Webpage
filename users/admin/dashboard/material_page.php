<?php
include '../../models/Material.php';

$material = new Material();

$message = "";
if (isset($_FILES["material"]) && isset($_POST['submit-material'])) {
    $materialType = $_POST['material-type'];
    $subjectId = isset($_POST['subject']) ? $_POST['subject'] : null;

    $allowedTypes = ["pdf", "docx", "doc", "pptx"];
    $fileType = strtolower(pathinfo($_FILES["material"]["name"], PATHINFO_EXTENSION));
    $fileType = strtolower($fileType);

    if (!in_array($fileType, $allowedTypes)) {
        $message = "File Upload Failed. Invalid File Format. File should be PDF/PPT/DOC.";
    } else if ($_FILES["material"]["size"] > 20 * 1024 * 1024) {
        $message = "File Upload Failed. File Size greater than 20MB.";
    } else {
        $fileName = basename($_FILES["material"]["name"]);
        if (move_uploaded_file($_FILES["material"]["tmp_name"], "../../../Database/Material/" . $fileName)) {
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
}

echo "<script>
    alert('$message');
    window.location.href = './dashboard.php#view-material';
</script>";