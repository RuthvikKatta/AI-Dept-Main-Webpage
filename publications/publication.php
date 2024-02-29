<?php
include '../users/models/Publication.php';

$publication = new Publication();

$publicationId = $_GET['id'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publication Page</title>

    <link rel="stylesheet" href="./publication.style.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../assets/favicon-icon.png" type="image/x-icon">

    <script src="../CustomElements/AppHeaderElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <div class="publication-details">
        <?php
        $row = $publication->getPublicationById($publicationId);

        if (is_array($row) && count($row) > 0) {
            echo "<h1 class='publication-title'> " . $row['title'] . "</h1>";
            echo "<p>Domain: " . $row['domain'] . "</p>";
            echo "<p><strong>Journal Name:</strong> " . $row['journal_name'] . "</p>";
            echo "<p><strong>Paper Id:</strong> " . $row['paper_id'] . "</p>";
            echo "<p><strong>Written and Published By:</strong> " . $row['authors'] . "</p>";
            echo "<p style='font-size:x-small'>Abstract: " . $row['abstract'] . "</p>";

            $paperFile = "../Database/Publications/$publicationId/paper.pdf";

            if (file_exists($paperFile)) {
                echo '<p><a class="download-button" href="' . $paperFile . '" download>Download Paper</a></p>';
            } else {
                echo "<p class='error'>Paper not available for this publication.</p>";
            }

        } else {
            echo "Publication not found.";
        }
        ?>

        <p style="margin-top: 2rem"><a data-back="true" href="./">Back to Search</a></p>
    </div>
</body>

</html>