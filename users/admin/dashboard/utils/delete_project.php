<?php

include '../../../models/Project.php';

$project = new Project();

$projectId = $_GET['id'];

function deleteDirectory($dir) {
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

$projectDirectory = "../../../../Database/Projects/$projectId";
$project->deleteProjectByProjectId($projectId);
deleteDirectory($projectDirectory);

header("location:../dashboard.php#view-projects");