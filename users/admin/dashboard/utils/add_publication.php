<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Publication.php';

$publication = new Publication();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Publication Add Form</title>
</head>

<body>
    <h2>New Publication Form</h2>
    <a href="../dashboard.php#view-publications" class='btn-back'>Back to dashboard</a>
    <form method="POST" enctype='multipart/form-data'>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="paper_id">Paper Id:</label>
        <input type="text" id="paper_id" name="paper_id">

        <label for="journal_name">Journal Name:</label>
        <input type="text" id="journal_name" name="journal_name" required>

        <label for="domain">Domain:</label>
        <input type="text" id="domain" name="domain" required>

        <label for="authors">Author Names(Comma Seperated):</label>
        <input type="text" id="authors" name="authors" required>

        <label for="abstract">Abstract:</label>
        <textarea id="abstract" name="abstract" rows="10" required></textarea>

        <label for="role_type">Role Type:</label>
        <select name="role_type" id="role_type">
            <option value="Faculty">Faculty</option>
            <option value="Student">Student</option>
        </select>

        <label for="paper_file">Research Paper (PDF, DOC, DOCX):</label>
        <input type="file" id="paper_file" name="paper_file" accept=".pdf, .doc, .docx">

        <input type="submit" name="add-publication" value="Add Publication">
    </form>
    <?php
    if (isset($_POST['add-publication'])) {
        $publicationData = [
            'title' => $_POST['title'],
            'paper_id' => $_POST['paper_id'],
            'journal_name' => $_POST['journal_name'],
            'domain' => $_POST['domain'],
            'authors' => $_POST['authors'],
            'abstract' => $_POST['abstract'],
            'role_type' => $_POST['role_type'],
        ];

        try {
            $publicationId = $publication->addPublication($publicationData);
            $message = $publicationId ? "Publication added successfully" : "Failed to add publication";

            if ($publicationId) {
                $publicationDirectory = "../../../../Database/Publications/{$publicationId}";
                if (!is_dir($publicationDirectory))
                    mkdir($publicationDirectory, 0777, true);

                $documentationFile = uploadFile('paper_file', $publicationDirectory, 'Paper');
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }

        echo "<script>
            alert('$message');
            window.location.href = '../dashboard.php#view-publications';
        </script>";
    }

    function uploadFile($inputName, $directory, $fileType)
    {
        $allowedTypes = [
            'Paper' => ["pdf", "doc", "docx"],
        ];

        $fileName = $fileType;
        $fileType = strtolower(pathinfo($_FILES[$inputName]["name"], PATHINFO_EXTENSION));

        if (!empty($allowedTypes[$fileType]) && !in_array($fileType, $allowedTypes[$fileType])) {
            return "{$fileType} file upload failed. Invalid file type.";
        }

        if ($_FILES[$inputName]["size"] > 10 * 1024 * 1024) {
            return "{$fileType} file upload failed. File size exceeds 10MB.";
        }

        $fileName = "{$fileName}.{$fileType}";
        $filePath = "{$directory}/{$fileName}";

        return move_uploaded_file($_FILES[$inputName]["tmp_name"], $filePath) ? $filePath : "{$fileType} file upload failed.";
    }
    ?>


</body>

</html>