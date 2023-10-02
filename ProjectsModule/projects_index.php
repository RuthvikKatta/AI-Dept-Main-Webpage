<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="./projects_style.css">
    <title>Project Display Page</title>
</head>

<body>
    <app-header></app-header>
    <form id="search-form" method="POST">
        <h1 class="project-title">Project Search</h1>
        <?php
        // Establish a database connection (you should fill in your database details)
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "college_website_test_db";
        $tablename = "projects_test_table";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $distinct_years = "SELECT distinct academic_year from $tablename";
        $distinctYearResult = $conn->query($distinct_years);

        echo '<label for="academic-year">Academic Year:</label>
            <select id="academic-year" name="academic_year">';
        echo '<option value="">Select Year</option>';

        $years = array();
        while ($row = $distinctYearResult->fetch_assoc()) {
            $years[] = $row['academic_year'];
        }
        foreach ($years as $year) {
            echo '<option value="' . $year . '">' . $year . '</option>';
        }
        echo '</select>
            <label for="domain">Domain:</label>
            <select id="domain" name="domain">';
        echo '<option value="">Select Domain</option>';
        $distinct_domain_names = "SELECT distinct domain from $tablename";
        $distinctDomainResult = $conn->query($distinct_domain_names);
        $domainNames = array();
        while ($row = $distinctDomainResult->fetch_assoc()) {
            $domainNames[] = $row['domain'];
        }
        foreach ($domainNames as $domain) {
            echo '<option value="' . $domain . '">' . $domain . '</option>';
        }
        echo '</select>';
        echo '<label for="type">Project Type:</label>
            <select id="type" name="type">
            <option value="">Select Project Type</option>
                <option value="Mini">Mini Project</option>
                <option value="Major">Major Project</option>
            </select>
            <input type="submit" name="search" value="Search">
            </form>';

        if (isset($_POST['search'])) {
            $academicYear = $_POST["academic_year"];
            $domain = $_POST["domain"];
            $type = $_POST["type"];

            $sql = "SELECT project_id, academic_year, project_title, domain, project_type
                    FROM $tablename
                    WHERE academic_year LIKE CONCAT('%', '$academicYear', '%')
                    AND domain LIKE CONCAT('%', '$domain', '%')
                    AND project_type LIKE CONCAT('%', '$type', '%')";

            $result = $conn->query($sql);
            $data = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row['project_id'];
                }
            }

            echo '<div id="result">';
            echo '        <table id="data-table">';
            echo '           <thead>';
            echo '                <tr>';
            echo '                    <th>S.No</th>';
            echo '                    <th>Academic Year</th>';
            echo '                    <th>Project Title</th>';
            echo '                    <th>Domain</th>';
            echo '                    <th>Project Link</th>';
            echo '                </tr>';
            echo '            </thead>';
            echo '           <tbody>';

            if (count($data) > 0) {
                $c = 0;
                foreach ($data as $d) {
                    $c = $c + 1;
                    $sql1 = "SELECT project_id, academic_year, project_title, domain, project_type
                        FROM $tablename
                        WHERE project_id = '$d'";

                    $result1 = $conn->query($sql1);

                    while ($row = $result1->fetch_assoc()) {
                        $pid = $row['project_id'];
                        $AcademicYear = $row['academic_year'];
                        $ProjectTitle = $row['project_title'];
                        $Domain = $row['domain'];
                        $ProjectType = $row['project_type'];
                    }
                    echo '<tr>';
                    echo '<td>' . $c . '</td>
                        <td>' . $AcademicYear . '</td>  
                        <td>' . $ProjectTitle . '</td>
                        <td>' . $Domain . '</td>
                        <td><a class="next-page" href="project_page.php?id=' . $pid . '">View Details</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<td colspan="5" style="text-align:center;">No Data Found</td>';
            }
            echo '            </tbody>';
            echo '        </table>';
            echo '    </div>';
        }
        mysqli_close($conn);
        ?>
</body>
<script src="../Elements.js"></script>
<script>
    if(performance.navigation.type === 1) {

    var table = document.getElementById('data-table');
    if(table)
        table.remove();
    }
</script>
</html>