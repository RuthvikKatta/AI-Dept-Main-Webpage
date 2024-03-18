<?php
include '../users/models/Project.php';

$project = new Project();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Page</title>

    <link rel="stylesheet" href="./project.style.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../assets/Images/favicon-icon.png" type="image/x-icon">

    <script src="../CustomElements/AppHeaderElement.js" defer></script>
    <script src="../CustomElements/VisionMissionElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <div class="container">
        <form id="search-form" method="POST">
            <h1 class="project-title">Project Search</h1>
            <label for="academic-year">Academic Year:</label>
            <select id="academic-year" name="academic_year">
                <option value="">Select Year</option>
                <?php
                $selectedYear = isset($_POST['academic_year']) ? $_POST['academic_year'] : null;
                $selectedDomain = isset($_POST['domain']) ? $_POST['domain'] : null;
                $selectedType = isset($_POST['type']) ? $_POST['type'] : null;

                $distinctOptions = $project->getDistinctOptions();

                $years = $distinctOptions['years'];
                $domains = $distinctOptions['domains'];

                foreach ($years as $year) {
                    echo '<option value="' . $year . '" ' . ($selectedYear === $year ? 'selected' : '') . '>' . $year . '</option>';
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
            <label for="type">Project Type:</label>
            <select id="type" name="type">
                <option value="">Select Project Type</option>
                <option value="Mini" <?php echo ($selectedType === 'Mini') ? 'selected' : ''; ?>>Mini Project</option>
                <option value="Major" <?php echo ($selectedType === 'Major') ? 'selected' : ''; ?>>Major Project</option>
            </select>
            <input type="submit" name="search" value="Search">
        </form>
    </div>

    <div id="result">
        <table id="data-table">
            <tbody>
                <?php
                if (isset($_POST['search'])) {

                    $year = $_POST['academic_year'];
                    $domain = $_POST['domain'];
                    $type = $_POST['type'];

                    $data = $project->getProjects($year, $domain, $type);

                    echo '<thead>
                            <tr>
                                <th>S.No</th>
                                <th>Academic Year</th>
                                <th>Project Title</th>
                                <th>Domain</th>
                                <th>Project Link</th>
                            </tr>
                        </thead>';
                    if (!empty($data)) {
                        $serialNumber = 0;
                        foreach ($data as $row) {
                            $serialNumber++;
                            $pid = $row['project_id'];
                            $AcademicYear = $row['academic_year'];
                            $ProjectTitle = $row['title'];
                            $Domain = $row['domain'];
                            $ProjectType = $row['type'];

                            echo '<tr>';
                            echo '<td>' . $serialNumber . '</td>
                                <td>' . $AcademicYear . '</td>  
                                <td>' . $ProjectTitle . '</td>
                                <td>' . $Domain . '</td>
                                <td><a class="next-page" href="project.php?id=' . $pid . '">View Details</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
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