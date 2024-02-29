<?php
include '../users/models/Project.php';

$project = new Project();

$projectId = $_GET['id'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Page</title>

    <link rel="stylesheet" href="./project.style.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../assets/favicon-icon.png" type="image/x-icon">

    <script src="../CustomElements/AppHeaderElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <div class="project-details">
        <?php
        $row = $project->getProjectById($projectId);

        if (is_array($row) && count($row) > 0) {
            echo "<h1 class='project-title'> " . $row['title'] . "</h1>";
            echo "<p> " . $row['academic_year'] . " - " . $row['type'] . " Project</p>";
            echo "<p><strong>Domain:</strong> " . $row['domain'] . "</p>";
            echo "<p><strong>Developed By:</strong> " . $row['student_names'] . "</p>";

            // Check if files exist before generating download links
            $documentationFile = "../Database/Projects/$projectId/Documentation.pdf";
            $presentationFile = "../Database/Projects/$projectId/Presentation.pptx";
            $codeZipFile = "../Database/Projects/$projectId/Code.zip";
            $videoFile = "../Database/Projects/$projectId/Video.mp4";

            if (file_exists($documentationFile)) {
                echo '<p><a class="download-button" href="' . $documentationFile . '" download>Download Documentation</a></p>';
            } else {
                echo "<p class='error'>Documentation not available for this project.</p>";
            }

            if (file_exists($presentationFile)) {
                echo '<p><a class="download-button" href="' . $presentationFile . '" download>Download Presentation</a></p>';
            } else {
                echo "<p class='error'>Presentation not available for this project.</p>";
            }

            if (file_exists($codeZipFile)) {
                echo '<p><a class="download-button" href="' . $codeZipFile . '" download>Download Code</a></p>';
            } else {
                echo "<p class='error'>Code not available for this project.</p>";
            }

            if (file_exists($videoFile)) {
                echo '<p><a class="download-button play-video">Play Video</a></p>';
                echo '<div class="pop-up">
                        <div class="video-container">
                            <img class="close-icon" src="/AI-Main-Page/assets/Icons/x-solid.svg" width=20 alt="close logo">
                            <video width="600" controls>
                                <source src = ' . $videoFile . ' type="video/mp4">
                                <source src = ' . $videoFile . ' type="video/ogg">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>';
            } else {
                echo "<p class='error'>Demonstration Video not available for this project.</p>";
            }
        } else {
            echo "Project not found.";
        }
        ?>

        <p style="margin-top: 2rem"><a data-back="true" href="./">Back to Search</a></p>
    </div>
</body>
<script>
    const closeIcon = document.querySelector('.close-icon');
    const popUp = document.querySelector('.pop-up');
    const playVideoButton = document.querySelector('.play-video');

    playVideoButton.addEventListener('click', () => {
        popUp.classList.add('reveal');
    })

    closeIcon.addEventListener('click', () => {
        popUp.classList.remove('reveal');
    })
</script>

</html>