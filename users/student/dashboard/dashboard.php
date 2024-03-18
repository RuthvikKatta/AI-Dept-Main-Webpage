<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['studentId']) && $_SESSION['loggedIn'] === true) {
    $studentId = $_SESSION['studentId'];
} else {
    header("Location: ../../login.php");
}

include '../../models/Attendance.php';
include '../../models/Marks.php';
include '../../models/Student.php';
include '../../models/Subject.php';
include '../../models/ClassDetails.php';

$attendance = new Attendance();
$marks = new Marks();
$student = new Student();
$subject = new Subject();
$classDetails = new ClassDetails();

$sd = $student->getStudentDetails($studentId);

$year = $sd['year'];
$section = $sd['section'];

$current_semester = $classDetails->getCurrentSemester($year, $section);
$subjects = $subject->getSubjects($year, $current_semester, $section);
$subjectIds = array_column($subjects, 'subject_id');

function validateMarks($str)
{
    return is_null($str) ? '-' : $str;
}
function bestOfThreeAverage($mid1, $assingment1, $mid2, $assingment2, $mid3, $assingment3)
{
    $values = [
        is_null($mid1) ? null : $mid1 + $assingment1,
        is_null($mid2) ? null : $mid2 + $assingment2,
        is_null($mid3) ? null : $mid3 + $assingment3,
    ];

    $nonNullValues = array_filter($values, function ($value) {
        return $value !== null;
    });


    switch (count($nonNullValues)) {
        case 3:
            return array_sum($nonNullValues) / 3;
        case 2:
            return array_sum($nonNullValues) / 2;
        case 1:
            return current($nonNullValues);
        default:
            return '-';
    }
}
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
    <section class="student-details">
        <div class="student-info">
            <h2 class="student-name">
                <?php echo $sd['salutation'] . ". " . $sd['first_name'] . " " . $sd['middle_name'] . " " . $sd['last_name'] ?>
            </h2>
            <p><strong>Id: </strong>
                <?php echo $studentId ?>
            </p>
            <p><strong>Year: </strong>
                <?php echo $year . " - " . $section ?>
            </p>
            <p><strong>Gender:</strong>
                <?php echo $sd['gender'] ?>
            </p>
            <p><strong>Date of Birth:</strong>
                <?php echo $sd['date_of_birth'] ?>
            </p>
            <p><strong>Email:</strong>
                <?php echo $sd['email'] ?>
            </p>
        </div>
        <div class="right-container">
            <div class="profile-picture">
                <?php
                $imageExtensions = ['jpg', 'jpeg'];
                foreach ($imageExtensions as $extension) {
                    $imagePath = "../../../Database/Student/{$studentId}.{$extension}";

                    if (file_exists($imagePath)) {
                        echo "<img src='$imagePath' alt='Profile Picture'>";
                        break;
                    }
                } ?>
            </div>
            <a href='../../logout.php?logout=true' class='logout'>Logout</a>
        </div>
    </section>
    <section class="student-dashboard">
        <section class="attendance">
            <h2>Attendance</h2>
            <?php
            $rows = $attendance->getAttendanceByStudentId($subjectIds, $studentId);
            $totalClasses = $attendance->getTotalClasses($subjectIds);

            echo "<table><tr><th>Count</th>";
            foreach ($subjects as $subjectDetails) {
                echo "<th>" . $subjectDetails['name'] . "</th>";
            }
            echo "</tr><tr><td>Total Classes</td>";

            $totalClassesCount = [];
            foreach ($totalClasses as $subjectClass) {
                echo "<td>" . $subjectClass['total_classes'] . "</td>";
                $totalClassesCount[$subjectClass['subject_id']] = $subjectClass['total_classes'];
            }

            $totalPresentClasses = array_fill_keys($subjectIds, 0);

            foreach ($rows as $row) {
                echo "<tr><td>Present Classes</td>";
                foreach ($subjectIds as $subjectId) {
                    echo "<td>" . $row[$subjectId] . "</td>";
                    $totalPresentClasses[$subjectId] += $row[$subjectId];
                }
                echo "</tr>";
            }

            echo "<tr><td>Percentage</td>";
            foreach ($subjectIds as $subjectId) {
                if($totalClassesCount[$subjectId] == 0) {
                    echo "<td>0%</td>";
                } else {
                    $percentage = ($totalPresentClasses[$subjectId] / $totalClassesCount[$subjectId]) * 100;
                    echo "<td>" . round($percentage, 2) . "%</td>";
                }
            }
            echo "</tr></table>";
            ?>
        </section>
        <section class="marks">
            <?php
            $rows = $marks->getOverallMarksOfStudents($year, $section, $studentId, $subjectIds);

            echo "<h2>Mid Examination Marks</h2>
              <table>
                <tr>
                  <th>Subject</th>
                  <th>Mid I</th>
                  <th>Assignment I</th>
                  <th>Mid II</th>
                  <th>Assignment II</th>
                  <th>Mid III</th>
                  <th>Assignment III</th>
                  <th>Average</th>
                </tr>";

            if (count($rows) > 0) {
                foreach ($rows as $record) {
                    $subjectName = $subject->getSubjectName($record['subject_id']);
                    echo "<tr><td>" . $subjectName['name'] . "</td>
                      <td>" . validateMarks($record['Mid I']) . "</td>
                      <td>" . validateMarks($record['Assignment I']) . "</td>
                      <td>" . validateMarks($record['Mid II']) . "</td>
                      <td>" . validateMarks($record['Assignment II']) . "</td>
                      <td>" . validateMarks($record['Mid III']) . "</td>
                      <td>" . validateMarks($record['Assignment III']) . "</td>
                      <td>" . bestOfThreeAverage($record['Mid I'], $record['Assignment I'], $record['Mid II'], $record['Assignment II'], $record['Mid III'], $record['Assignment III']) . "</td>
                      </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No data Exists</td></tr>";
            }
            echo "</table>";
            ?>
        </section>
        <section class="backlogs-report">
            <h2>Active Backlogs: </h2>
            <?php
            $rows = $marks->getBacklogsByStudentId($studentId);

            echo "<table>
                    <tr>
                    <th>Sl No.</th>
                    <th>Subject Name</th>
                    <th>Subject Year</th>
                    <th>Subject Semester</th>
                    </tr>";

            if (count($rows) > 0) {
                foreach ($rows as $rec => $record) {
                    echo "<tr><td>" . $rec + 1 . "</td>
                      <td>" . $record['name'] . "</td>
                      <td>" . $record['year'] . "</td>
                      <td>" . $record['semester'] . "</td>
                      </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No Active Backlogs</td></tr>";
            }
            echo "</table>";
            ?>
        </section>
    </section>
</body>

</html>