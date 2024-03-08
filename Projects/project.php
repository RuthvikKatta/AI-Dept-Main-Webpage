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
    <link rel="shortcut icon" href="../assets/Images/favicon-icon.png" type="image/x-icon">

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

            $fileExtensions = [
                'Documentation' => ['pdf', 'doc', 'docx'],
                'Presentation' => ['ppt', 'pptx'],
                'Code' => 'zip',
            ];

            foreach ($fileExtensions as $fileType => $extensions) {
                $fileExists = false;
                $fileLink = "#";

                if (is_array($extensions)) {
                    foreach ($extensions as $extension) {
                        $filePath = "../Database/Projects/{$projectId}/{$fileType}.{$extension}";

                        if (file_exists($filePath)) {
                            $fileExists = true;
                            $fileLink = $filePath;
                            break;
                        }
                    }
                } else {
                    $extension = $extensions;
                    $filePath = "../Database/Projects/{$projectId}/{$fileType}.{$extension}";

                    if (file_exists($filePath)) {
                        $fileExists = true;
                        $fileLink = $filePath;
                    }
                }

                echo $fileExists
                    ? "<p><a target='_BLANK' class='download-button' href='{$fileLink}' download>Download {$fileType}</a></p>"
                    : "<p class='error'>{$fileType} not available for this project.</p>";
            }

            $videoFile = "../Database/Projects/{$projectId}/Video.mp4";

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
    const video = document.querySelector('.video-container video');

    playVideoButton.addEventListener('click', () => {
        popUp.classList.add('reveal');
        adjustVideoWidth();
    });

    closeIcon.addEventListener('click', () => {
        popUp.classList.remove('reveal');
        stopVideo();
    });

    function adjustVideoWidth() {
        const aspectRatio = video.videoWidth / video.videoHeight;

        if(aspectRatio < 1){
            video.width = 250;
        }
    }

    function stopVideo() {
        video.pause();
        video.currentTime = 0;
    }
</script>

</html>