<?php

include '../../../models/Material.php';

$material = new Material();

$fileId = $_GET['id'];
$fileName = $_GET['name'];

$filePath = "../../../../Database/Material/" . $fileName;

if (file_exists($filePath)) {
    $material->deleteMaterial($fileId);
    unlink($filePath);
}

header("location:../dashboard.php#view-material");