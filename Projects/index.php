<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "college_website_test_db";
    $tablename = "projects_test_table";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $years = $domainNames = array();

    $selectedYear = $selectedDomain = $selectedType = "";


    if (isset($_POST['search'])) {
        $academicYear = $_POST["academic_year"];
        $domain = $_POST["domain"];
        $type = $_POST["type"];

        $sql = "SELECT project_id, academic_year, project_title, project_domain, project_type
                FROM $tablename
                WHERE academic_year LIKE CONCAT('%', '$academicYear', '%')
                AND project_domain LIKE CONCAT('%', '$domain', '%')
                AND project_type LIKE CONCAT('%', '$type', '%')";

        $result = $conn->query($sql);
        $data = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        // Store selected values in variables to maintain form state
        $selectedYear = $academicYear;
        $selectedDomain = $domain;
        $selectedType = $type;
    }

    $distinct_years = "SELECT DISTINCT academic_year FROM $tablename";
    $distinctYearResult = $conn->query($distinct_years);

    while ($row = $distinctYearResult->fetch_assoc()) {
        $years[] = $row['academic_year'];
    }

    $distinct_domain_names = "SELECT DISTINCT project_domain FROM $tablename";
    $distinctDomainResult = $conn->query($distinct_domain_names);

    while ($row = $distinctDomainResult->fetch_assoc()) {
        $domainNames[] = $row['project_domain'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Page</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="./projects_style.css">
    <link rel="shortcut icon" href="../assets/favicon-icon.png" type="image/x-icon">

    <script src="../Elements.js" defer></script>
</head>
<body>
    <app-header></app-header>
    <form id="search-form" method="POST">
        <h1 class="project-title">Project Search</h1>
        <label for="academic-year">Academic Year:</label>
        <select id="academic-year" name="academic_year">
            <option value="">Select Year</option>
            <?php
            foreach ($years as $year) {
                echo '<option value="' . $year . '" ' . ($selectedYear === $year ? 'selected' : '') . '>' . $year . '</option>';
            }
            ?>
        </select>
        <label for="domain">Domain:</label>
        <select id="domain" name="domain">
            <option value="">Select Domain</option>
            <?php
            foreach ($domainNames as $domain) {
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

    <div id="result">
        <table id="data-table">
            <tbody>
                <?php
                if (isset($_POST['search'])){

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
                        $c = 0;
                        foreach ($data as $row) {
                            $c++;
                            $pid = $row['project_id'];
                            $AcademicYear = $row['academic_year'];
                            $ProjectTitle = $row['project_title'];
                            $Domain = $row['project_domain'];
                            $ProjectType = $row['project_type'];
    
                            echo '<tr>';
                            echo '<td>' . $c . '</td>
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
    
<?php
// Close the database connection when done
mysqli_close($conn);
?>

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