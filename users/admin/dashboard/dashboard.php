<?php

session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../login/login.php");
}

include '../../models/Attendance.php';
include '../../models/Subject.php';

$subject = new Subject();
$attendance = new Attendance();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />

    <link rel="stylesheet" href="./dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
</head>

<body>
    <section class="container">
        <div class="navigation">
            <ul>
                <li><a href="#profile">Profile</a></li>
                <li><a href="#ViewAttendance">View Attendance</a></li>
                <li><a href="#logout">Logout</a></li>
            </ul>
        </div>
    </section>

    <main>

        <section id="profile"></section>

        <section id="ViewAttendance">
            <?php
            $result = $subject->getSubjects('IV', 1, 'A');
            $subjectIds = array_column($result, 'subject_id');
            $studentId = '20911A3595';

            $rows = $attendance->getAttendanceByStudentId($subjectIds, '20911A3594');

            echo "<table><tr><th>Student Id</th><th>Student Name</th>";
            foreach ($result as $subject) {
                echo "<th>" . $subject['subject_name'] . "</th>";
            }
            echo "</tr>";
            foreach ($rows as $row) {
                echo "<tr>
                <td>" . $row['student_id'] . "</td>
                <td>" . $row['name'] . "</td>
                <td>" . $row['1'] . "</td>
                <td>" . $row['2'] . "</td>
                <td>" . $row['3'] . "</td>
                </tr>";
            }
            echo "</table>"
                ?>
        </section>

        <section id="logout">
            <h2>Are you sure want to Logout?</h2>
            <a href='../login/logout.php?logout=true' class='logout'>Logout</a>
        </section>

    </main>
</body>

<script src="./script.js"></script>

</html>