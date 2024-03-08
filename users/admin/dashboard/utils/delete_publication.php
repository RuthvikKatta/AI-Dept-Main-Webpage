<?php

include '../../../models/Publication.php';

$publication = new Publication();

$publicationId = $_GET['id'];

function deleteDirectory($dir)
{
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), array('.'));

    foreach ($files as $file) {
        $path = "$dir/$file";
        unlink($path);
    }

    rmdir($dir);
}

$publicationDirectory = "../../../../Database/Publications/$publicationId";
$publication->deletePublicationByPublicationId($publicationId);
deleteDirectory($publicationDirectory);

header("location:../dashboard.php#view-publications");