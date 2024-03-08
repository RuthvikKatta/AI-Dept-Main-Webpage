<?php
include '../users/models/Publication.php';

$publication = new Publication();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publications Page</title>

    <link rel="stylesheet" href="./publication.style.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../assets/Images/favicon-icon.png" type="image/x-icon">

    <script src="../CustomElements/AppHeaderElement.js" defer></script>
    <script src="../CustomElements/VisionMissionElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <div class="container">
        <form id="search-form" method="POST">
            <h1 class="publication-title">Publication Search</h1>
            <label for="journal-name">Journal Name:</label>
            <select id="journal-name" name="journal-name">
                <option value="">Select Journal</option>
                <?php
                $selectedYear = isset($_POST['journal-name']) ? $_POST['journal-name'] : null;
                $selectedDomain = isset($_POST['domain']) ? $_POST['domain'] : null;
                $selectedType = isset($_POST['type']) ? $_POST['type'] : null;

                $distinctOptions = $publication->getDistinctOptions();

                $journalNames = $distinctOptions['journal_names'];
                $domains = $distinctOptions['domains'];

                foreach ($journalNames as $journalName) {
                    echo '<option value="' . $journalName . '" ' . ($selectedYear === $journalName ? 'selected' : '') . '>' . $journalName . '</option>';
                }
                ?>
            </select>
            <label for="domain">Domain:</label>
            <select id="domain" name="domain">
                <option value="">Select Domain</option>
                <?php
                foreach ($domains as $domain) {
                    echo '<option value="' . $domain . '" ' . ($selectedDomain === $domain ? 'selected' : '') . '>' . $domain . '</option>';
                }
                ?>
            </select>
            <label for="type">Role Type:</label>
            <select id="type" name="type">
                <option value="">Select Role Type</option>
                <option value="Faculty" <?php echo ($selectedType === 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                <option value="Student" <?php echo ($selectedType === 'Student') ? 'selected' : ''; ?>>Student</option>
            </select>
            <input type="submit" name="search" value="Search">
        </form>
    </div>

    <div id="result">
        <table id="data-table">
            <tbody>
                <?php
                if (isset($_POST['search'])) {

                    $journalName = $_POST['journal-name'];
                    $domain = $_POST['domain'];
                    $type = $_POST['type'];

                    $data = $publication->getPublications($domain, $journalName, $type);

                    echo '<thead>
                            <tr>
                                <th>S.No</th>
                                <th>Title</th>
                                <th>Journal Name</th>
                                <th>Paper Id</th>
                                <th>Domain</th>
                                <th>Project Link</th>
                            </tr>
                        </thead>';
                    if (!empty($data)) {
                        $serialNumber = 0;
                        foreach ($data as $row) {
                            $serialNumber++;
                            $pid = $row['publication_id'];

                            echo '<tr>';
                            echo '<td>' . $serialNumber . '</td>
                            <td>' . $row['title'] . '</td>
                                <td>' . $row['journal_name'] . '</td>  
                                <td>' . $row['paper_id'] . '</td>
                                <td>' . $row['domain'] . '</td>
                                <td><a class="next-page" href="publication.php?id=' . $pid . '">View Details</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
<script>
    if (performance.navigation.type === 1) {
        var table = document.getElementById('data-table');
        if (table) {
            table.remove();
        }
    }
</script>

</html>