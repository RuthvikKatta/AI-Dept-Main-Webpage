<?php

include '../../../models/Media.php';

$media = new Media();

$imageId = $_GET['id'];
$imageName = $_GET['name'];

$imagePath = "../../../../Database/Carousal Images/" . $imageName;

if(file_exists($imagePath)){
    $media->deleteCarousalImage($imageId);
    unlink($imagePath);
}
header("location:../dashboard.php#view-media");