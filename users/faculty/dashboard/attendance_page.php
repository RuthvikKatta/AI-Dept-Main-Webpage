<?php

session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    $facultyId = $_SESSION['facultyId'];
} else {
    header("Location: ../../login.php");
}

include '../../models/Student.php';
include '../../models/Attendance.php';
include '../../models/Subject.php';

$student = new Student();
$attendance = new Attendance();
$subject = new Subject();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />

    <link rel="stylesheet" href="./dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendance</title>
</head>

<body>
    <section id="attendance">
        <a href="./dashboard.php#attendance" class='btn-back'>Back to dashboard</a>
        <section class="attendance-details">
            <?php
            $teachingDetails = $subject->getTeachingDetails($facultyId);
            $subjects = $teachingDetails['subjects'];
            $years = $teachingDetails['years'];

            $selectedSection = isset($_POST['section']) ? $_POST['section'] : null;
            $selectedSubjectId = isset($_POST['subject']) ? $_POST['subject'] : null;
            $selectedYear = isset($_POST['year']) ? $_POST['year'] : null;

            $logNo = null;

            if (isset($_GET['edit'])) {
                $logNo = $_GET['attendanceId'];
                $prevAttendanceDetails = $attendance->getAttendanceLogsByLogNo($logNo);

                $selectedSubjectId = $prevAttendanceDetails['subject_id'];
                $selectedYear = $prevAttendanceDetails['class_year'];
                $selectedSection = $prevAttendanceDetails['class_section'];
            }

            ?>
            <form method="post">
                <label for="year">Select Year:</label>
                <select id="year" name="year" required>
                    <option value="">Select Year</option>
                    <?php
                    if (is_array($years)) {
                        foreach ($years as $year) {
                            echo '<option value="' . $year . '" ' . ($selectedYear == $year ? 'selected' : '') . '>' . $year . '</option>';
                        }
                    }
                    ?>
                </select>

                <label for="section">Select Section:</label>
                <select id="section" name="section" required>
                    <option value="">Select Section</option>
                    <option value="A" <?php echo ($selectedSection == 'A') ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?php echo ($selectedSection == 'B') ? 'selected' : ''; ?>>B</option>
                </select>

                <label for="subject">Select Subject:</label>
                <select id="subject" name="subject" required>
                    <option value="">Select Subject</option>
                    <?php
                    if (is_array($subjects)) {
                        foreach ($subjects as $s) {
                            echo '<option value="' . $s['subject_id'] . '" ' . ($selectedSubjectId == $s['subject_id'] ? 'selected' : '') . '>' . $s['name'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <input type="submit" name="getStudents" value="Get Students">
            </form>
        </section>
        <section class='students-form'>
            <?php
            if (isset($_POST['getStudents']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $subjectId = $_POST['subject'];
                $year = $_POST['year'];
                $section = $_POST['section'];

                $students = $student->getAllStudentofYearAndSection($year, $section);

                echo "<h2>Take Attendance of " . $year . " - " . $section . "</h2>";
                if (count($students) > 0) {
                    echo '<form id="take-attendance" method="post" action="handle_attendance.php">';

                    echo '<div class="radio-buttons">
                           <label><input type="radio" name="attendanceOption" value="markPresentees" checked> Mark Presentees</label>
                           <label><input type="radio" name="attendanceOption" value="markAbsentees"> Mark Absentees</label>
                         </div>';

                    foreach ($students as $student) {
                        echo '<label for=' . $student['student_id'] . '>'. $student['student_id'] . ' - ' . $student['last_name'] . " " . $student['first_name'] . " " . $student['middle_name'] . '</label>';
                        echo '<input type="checkbox" id="' . $student['student_id'] . '" name="students[]" value="' . $student['student_id'] . '" />';
                    }
                    echo '<input type="hidden" name="subjectId" value="' . $subjectId . '">';
                    echo '<input type="hidden" name="year" value="' . $year . '">';
                    echo '<input type="hidden" name="section" value="' . $section . '">';
                    echo '<input type="Submit" name="takeAttendance" value="Post Attendance"></form>';
                } else {
                    echo 'No Students Found';
                }
            } else if (isset($_GET['edit'])) {
                $subjectId = $selectedSubjectId;
                $year = $selectedYear;
                $section = $selectedSection;

                $students = $student->getAllStudentofYearAndSection($year, $section);
                $absentStudents = $attendance->getAbsentLogs($logNo);
                $absentStudents = array_column($absentStudents, 'student_id');

                echo "<h2>Edit Attendance Year " . $year . " Section" . $section . "</h2>";
                if (count($students) > 0) {
                    echo '<form method="post" action="handle_attendance.php">';
                    foreach ($students as $student) {
                        $isChecked = in_array($student['student_id'], $absentStudents) ? '' : 'checked';
                        echo '<label for=' . $student['student_id'] . '>'. $student['student_id'] . ' - ' . $student['last_name'] . " " . $student['first_name'] . " " . $student['middle_name'] . '</label>';
                        echo '<input type="checkbox" id="' . $student['student_id'] . '" name="students[]" value="' . $student['student_id'] . '" ' . $isChecked . ' />';
                    }

                    echo '<input type="hidden" name="subjectId" value="' . $subjectId . '">';
                    echo '<input type="hidden" name="year" value="' . $year . '">';
                    echo '<input type="hidden" name="section" value="' . $section . '">';
                    echo '<input type="hidden" name="logNo" value="' . $logNo . '">';
                    echo '<input type="Submit" name="editAttendance" value="Edit Attendance"></form>';
                } else {
                    echo 'No Students Found';
                }
            }
            ?>
        </section>
    </section>
</body>

<script>
    const studentsForm = document.getElementById('take-attendance');

    if (studentsForm) {
        const editButtons = studentsForm.querySelectorAll('input[type="radio"]');
        editButtons.forEach((button) => {
            button.addEventListener("change", function (event) {
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                var selectedOption = document.querySelector('input[name="attendanceOption"]:checked');

                if (selectedOption && checkboxes) {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = (selectedOption.value === "markAbsentees");
                    });
                }
            });
        })
    }
</script>

</html>