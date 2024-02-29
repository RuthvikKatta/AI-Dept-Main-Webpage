<?php
session_start();

include '../../models/Attendance.php';
include '../../models/Student.php';

$attendance = new Attendance();
$student = new Student();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['takeAttendance'])) {

        $subjectId = $_POST['subjectId'];
        $year = $_POST['year'];
        $section = $_POST['section'];
        $facultyId = $_SESSION['facultyId'];

        $studentIds = array();
        $rows = $student->getAllStudentofYearAndSection($year, $section);
        foreach ($rows as $row) {
            $studentIds[] = $row['student_id'];
        }

        $presentStudentIds = is_array($_POST['students']) ? $_POST['students'] : [];
        $absentStudentIds = array_diff($studentIds, $presentStudentIds);

        $insertedLogNo = $attendance->addAttendanceLog($facultyId, $year, $section, $subjectId);
        $attendance->addAbsentLogs($absentStudentIds, $insertedLogNo, $subjectId, $facultyId);

    } else if (isset($_POST['editAttendance'])) {

        $facultyId = $_SESSION['facultyId'];
        $subjectId = $_POST['subjectId'];
        $year = $_POST['year'];
        $section = $_POST['section'];
        $attendance_log_no = $_POST['logNo'];

        $studentIds = array();
        $rows = $student->getAllStudentofYearAndSection($year, $section);
        foreach ($rows as $row) {
            $studentIds[] = $row['student_id'];
        }

        $presentStudentIds = is_array($_POST['students']) ? $_POST['students'] : [];
        $absentStudentIds = array_diff($studentIds, $presentStudentIds);

        // removing student absent logs if edited as present
        $prevAbsentStudentIds = $attendance->getAbsentLogs($attendance_log_no);
        $prevAbsentStudentIds = array_column($prevAbsentStudentIds, 'student_id');

        $editAbsentStudentIds = array_diff($prevAbsentStudentIds, $absentStudentIds);

        if (count($editAbsentStudentIds) > 0) {
            $attendance->removeAbsentLogs($editAbsentStudentIds, $attendance_log_no);
        }

        // adding student absent logs if edited as absent
        $editPresentStudentIds = array_diff($absentStudentIds, $prevAbsentStudentIds);
        if (count($editPresentStudentIds) > 0) {
            $attendance->addAbsentLogs($editPresentStudentIds, $attendance_log_no, $subjectId, $facultyId);
        }
    }

}

header("Location: ./dashboard.php#attendance");
exit();