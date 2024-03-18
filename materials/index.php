<?php

$materialType = $_GET['type'];

if ($materialType != 'AC' && $materialType != 'PQP' && $materialType != 'SM') {
    header("Location: ../ai-department/index.php");
}
include '../users/models/Material.php';
include '../users/models/Subject.php';

$material = new Material();
$subject = new Subject();

function getClassName($mimeType)
{
    switch ($mimeType) {
        case 'application/pdf':
            return 'pdf';
        case 'application/msword':
        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            return 'doc';
        case 'application/vnd.ms-powerpoint':
        case 'application/octet-stream':
        case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            return 'ppt';
        default:
            return 'unknown';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materials Page</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="./material.style.css">
    <link rel="shortcut icon" href="../assets/Images/favicon-icon.png" type="image/x-icon">

    <script src="../CustomElements/AppHeaderElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <section class="main">
        <?php
        if ($materialType === 'AC') {
            echo "<div class='file-container'><h2>Academic Calenders</h2>";
            $materials = $material->getMaterials($materialType);

            if (count($materials) > 0) {
                echo "<div class='files'>";
                foreach ($materials as $mat) {
                    $filePath = "../Database/Material/" . $mat['name'];
                    if (file_exists($filePath)) {
                        $className = getClassName(mime_content_type($filePath));
                        echo "<a target='_blank' href='$filePath' class='$className tooltip' title='" . htmlspecialchars($mat['name']) . "'>" . substr($mat['name'], 0, 15) . "...</a>";
                    }
                }
                echo "</div>";
            } else {
                echo "<p>Academic Calendars Not Available.</p>";
            }
            echo "</div>";
        } elseif ($materialType === 'PQP') {
            echo "<div class='file-container'><h2>Previous Question Papers</h2>";

            echo "<form method='post'>";
            echo "<label for='subject-id'>Choose Subject: </label>";
            echo "<select name='subject-id' id='subject-id'>";

            $subjects = $subject->getAllSubjects();
            foreach ($subjects as $subject) {
                echo "<option value='" . $subject['subject_id'] . "'>" . $subject['name'] . "</option>";
            }

            echo "</select>";
            echo "<input type='submit' name='submit-subject' value='Submit'>";
            echo "</form>";

            if (isset($_POST['submit-subject'])) {
                $selectedSubjectId = $_POST['subject-id'];

                $materials = $material->getMaterials($materialType, $selectedSubjectId);

                if (count($materials) > 0) {
                    echo "<div class='files'>";
                    foreach ($materials as $mat) {
                        $filePath = "../Database/Material/" . $mat['name'];
                        if (file_exists($filePath)) {
                            $className = getClassName(mime_content_type($filePath));
                            echo "<a target='_blank' href='$filePath' class='$className tooltip' title='" . htmlspecialchars($mat['name']) . "'>" . substr($mat['name'], 0, 15) . "...</a>";
                        }
                    }
                    echo "</div>";
                } else {
                    echo "<p>Previous Question Papers Not Available.</p>";
                }
            }
            echo "</div>";
        } elseif ($materialType === 'SM') {
            echo "<div class='file-container'><h2>Subject Material</h2>";

            echo "<form method='post'>";
            echo "<label for='subject-id'>Choose Subject: </label>";
            echo "<select name='subject-id' id='subject-id'>";

            $subjects = $subject->getAllSubjects();
            foreach ($subjects as $subject) {
                echo "<option value='" . $subject['subject_id'] . "'>" . $subject['name'] . "</option>";
            }

            echo "</select>";
            echo "<input type='submit' name='submit-subject' value='Submit'>";
            echo "</form>";

            if (isset($_POST['submit-subject'])) {
                $selectedSubjectId = $_POST['subject-id'];

                $materials = $material->getMaterials($materialType, $selectedSubjectId);

                if (count($materials) > 0) {
                    echo "<div class='files'>";
                    foreach ($materials as $mat) {
                        $filePath = "../Database/Material/" . $mat['name'];
                        if (file_exists($filePath)) {
                            $className = getClassName(mime_content_type($filePath));
                            echo "<a target='_blank' href='$filePath' class='$className tooltip' title='" . htmlspecialchars($mat['name']) . "'>" . substr($mat['name'], 0, 15) . "...</a>";
                        }
                    }
                    echo "</div>";
                } else {
                    echo "<p>Subject Material Not Available.</p>";
                }
            }
            echo "</div>";
        }
        ?>
    </section>
</body>

</html>