<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
</head>
<body>
    <h1>Project Details</h1>

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
        echo "<p><strong>Project Title:</strong> " . $row['project_title'] . "</p>";
        echo "<p><strong>Academic Year:</strong> " . $row['academic_year'] . "</p>";
        echo "<p><strong>Project Type:</strong> " . $row['project_type'] . "</p>";
        echo "<p><strong>Project Domain:</strong> " . $row['domain'] . "</p>";

        // Retrieve the Google Drive file ID from the database
        $drive_file_id = $row['project_document'];

        if (!empty($drive_file_id)) {
            // Create a download button with a link to the Google Drive file
            echo '<p><a href="https://drive.google.com/uc?export=download&id=' . $drive_file_id . '" target="_blank" download>Download Documentation</a></p>';
        } else {
            echo "Documentation not available for this project.";
        }
    } else {
        echo "Project not found.";
    }

    mysqli_close($conn);
    ?>

    <p><a href="index.html">Back to Search</a></p>
</body>
</html>
