<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="./projects_style.css">
</head>
<body>
    <app-header></app-header>
    <div class="project-details">
        <h1 class="project-title">Project Details</h1>

        <?php
        // Read the project ID from the URL
        $project_id = $_GET['id'];

        // Replace with your database connection code
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "college_website_test_db";
        $tablename = "projects_test_table";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT * FROM $tablename WHERE project_id = '$project_id'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            echo "<h1> " . $row['project_title'] . "</h1>";
            echo "<p> " . $row['academic_year'] . " - " . $row['project_type'] . " Project</p>";
            echo "<p> " . $row['domain'] . "</p>";
            echo "<p><strong>Developed By:</strong> " . $row['student_names'] . "</p>";

            // Retrieve the Google Drive file ID from the database
            $drive_documentation_file_id = $row['documentation_link'];

            if (!empty($drive_documentation_file_id)) {
                // Create a download button with a link to the Google Drive file
                echo '<p><a class= "download-button" href="https://drive.google.com/uc?export=download&id=' . $drive_documentation_file_id . '" target="_blank" download>Download Documentation</a></p>';
            } else {
                echo "<p>Presentation not available for this project.</p>";
            }

            // Retrieve the Google Drive file ID from the database
            $drive_presentation_file_id = $row['presentation_link'];

            if (!empty($drive_presentation_file_id)) {
                // Create a download button with a link to the Google Drive file
                echo '<p><a class= "download-button" href="https://drive.google.com/uc?export=download&id=' . $drive_presentation_file_id . '" target="_blank" download>Download Presentation</a></p>';
            } else {
                echo "<p>Documentation not available for this project.</p>";
            }

            $drive_video_file_id = $row['project_demonstration_link'];

            if (!empty($drive_video_file_id)) {
                // Create a download button with a link to the Google Drive file
                echo '<p><a class= "download-button" href="https://drive.google.com/uc?export=download&id=' . $drive_video_file_id . '" target="_blank" download>Download Demonstration Video</a></p>';
            } else {
                echo "<p>Demonstration Video not available for this project.</p>";
            }
        } else {
            echo "Project not found.";
        }

        mysqli_close($conn);
        ?>

        <p><a data-back="true" href="./projects_index.php">Back to Search</a></p>
    </div>
</body>
<script src="../Elements.js"></script>
</html>
