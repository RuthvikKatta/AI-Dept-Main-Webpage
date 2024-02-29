<?php
session_start();

include '../../models/Student.php';
include '../../models/Marks.php';

$student = new Student();
$marks = new Marks();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['addMarks'])) {

        $subjectId = $_POST['subjectId'];
        $year = $_POST['year'];
        $section = $_POST['section'];
        $marksType = $_POST['marks_type'];
        $examSession = $_POST['exam_session'];
        $totalMarks = $_POST['total_marks'];

        $rows = $student->getAllStudentofYearAndSection($year, $section);
        $studentIds = array_column($rows, 'student_id');

        $marksObtained = is_array($_POST['students']) ? $_POST['students'] : [];

        $marks->addMarkRecords($studentIds, $section, $year, $subjectId, $marksType, $examSession, $totalMarks, $marksObtained);

    } else if (isset($_POST['editMarks'])) {

        $recordIdsAndMarks = $_POST['marks'];

        $marks->updateMarks($recordIdsAndMarks);
    }
}

header("Location: ./dashboard.php#marks");
exit();