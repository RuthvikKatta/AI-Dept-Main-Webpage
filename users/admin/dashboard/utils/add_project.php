<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Project.php';

$project = new Project();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Project Add Form</title>
</head>

<body>
    <h2>New Project Form</h2>
    <a href="../dashboard.php#view-projects" class='btn-back'>Back to dashboard</a>
    <form method="POST" enctype='multipart/form-data'>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="domain">Domain:</label>
        <input type="text" id="domain" name="domain" required>

        <label for="academic_year">Academic Year:</label>
        <input type="text" id="academic_year" name="academic_year" required>
        
        <label for="type">Project Type:</label>
        <select name="type" id="type">
            <option value="Mini">Mini</option>
            <option value="Major">Major</option>
        </select>

        <label for="student_names">Student Names(Comma Seperated):</label>
        <input type="text" id="student_names" name="student_names" required>

        <label for="mentor_name">Mentor Name:</label>
        <input type="text" id="mentor_name" name="mentor_name" required>

        <label for="presentation_file">Presentation File (PPT, PPTX, PDF):</label>
        <input type="file" id="presentation_file" name="presentation_file" accept=".ppt, .pptx, .pdf">

        <label for="documentation_file">Documentation File (PDF, DOC, DOCX):</label>
        <input type="file" id="documentation_file" name="documentation_file" accept=".pdf, .doc, .docx">

        <label for="code_file">Code File:</label>
        <input type="file" id="code_file" name="code_file">

        <label for="execution_video">Execution Video (MP4):</label>
        <input type="file" id="execution_video" name="execution_video" accept=".mp4">

        <input type="submit" name="add-project" value="Add Project">
    </form>
    <?php
    if (isset($_POST['add-project'])) {
        $projectData = array(
            'academic_year' => $_POST['academic_year'],
            'title' => $_POST['title'],
            'domain' => $_POST['domain'],
            'type' => $_POST['type'],
            'student_names' => $_POST['student_names'],
            'mentor_name' => $_POST['mentor_name'],
        );

        try {
            $projectId = $project->addProject($projectData);
            $message = $projectId ? "Project added successfully" : "Failed to add project";
            if ($projectId) {

                $projectDirectory = "../../../../Database/Projects/{$projectId}";
        
                if (!is_dir($projectDirectory)) {
                    mkdir($projectDirectory, 0777, true);
                }
        
                $presentationFile = uploadFile('presentation_file', $projectDirectory, 'Presentation');
                $documentationFile = uploadFile('documentation_file', $projectDirectory, 'Documentation');
                $codeZipFile = uploadFile('code_file', $projectDirectory, 'Code');
                $executionVideo = uploadFile('execution_video', $projectDirectory, 'Video');
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }

        echo "<script>
                alert($message);
                window.location.href = '../dashboard.php#view-projects';
            </script>";
    }
    function uploadFile($inputName, $directory, $fileType)
    {
        $allowedTypes = [];
        $message = "";

        switch ($fileType) {
            case 'Presentation':
                $allowedTypes = ["ppt", "pptx", "pdf"];
                break;
            case 'Documentation':
                $allowedTypes = ["pdf", "doc", "docx"];
                break;
            case 'Code':
                // No specific restriction on code file types
                break;
            case 'Video':
                $allowedTypes = ["mp4"];
                break;
        }

        $fileName = $fileType;
        $fileType = strtolower(pathinfo($_FILES[$inputName]["name"], PATHINFO_EXTENSION));

        if (!empty($allowedTypes) && !in_array($fileType, $allowedTypes)) {
            $message = "{$fileType} file upload failed. Invalid file type.";
        } elseif ($_FILES[$inputName]["size"] > 50 * 1024 * 1024) {
            $message = "{$fileType} file upload failed. File size exceeds 50MB.";
        } else {
            $fileName = "{$fileName}.{$fileType}";
            $filePath = "{$directory}/{$fileName}";

            if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $filePath)) {
                return $filePath;
            } else {
                $message = "{$fileType} file upload failed.";
            }
        }
    }
    ?>

</body>

</html>