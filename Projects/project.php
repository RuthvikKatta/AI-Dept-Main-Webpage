<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Page</title>

    <link rel="stylesheet" href="./project.style.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../assets/favicon-icon.png" type="image/x-icon">

    <script src="../Elements.js" defer></script>
</head>
<body>
    <app-header></app-header>
    <div class="project-details">
        <?php
        // Read the project ID from the URL
        $project_id = $_GET['id'];

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "college_website_test_db";
        $tablename = "projects_test_table";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT * FROM $tablename WHERE project_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<h1> " . $row['project_title'] . "</h1>";
            echo "<p> " . $row['academic_year'] . " - " . $row['project_type'] . " Project</p>";
            echo "<p> " . $row['project_domain'] . "</p>";
            echo "<p><strong>Developed By:</strong> " . $row['student_names'] . "</p>";

            // Check if files exist before generating download links
            $documentationFile = "../MasterData/$project_id/Documentation.pdf";
            $presentationFile = "../MasterData/$project_id/Presentation.pptx";
            $codeZipFile = "../MasterData/$project_id/code.zip";
            $videoFile = "../MasterData/$project_id/Video.mp4";

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
                                <source src = '. $videoFile .' type="video/mp4">
                                <source src = '. $videoFile .' type="video/ogg">
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

        mysqli_close($conn);
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
