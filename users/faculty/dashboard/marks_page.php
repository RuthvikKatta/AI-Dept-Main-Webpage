<?php

session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    $facultyId = $_SESSION['facultyId'];
} else {
    header("Location: ../../login.php");
}

include '../../models/Student.php';
include '../../models/Subject.php';
include '../../models/Marks.php';

$student = new Student();
$subject = new Subject();
$marks = new Marks();
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
    <section id="marks">
    <a href="./dashboard.php#marks" class='btn-back'>Back to dashboard</a>
        <section class="upload-marks-form">
            <?php
            $teachingDetails = $subject->getTeachingDetails($facultyId);
            $subjects = $teachingDetails['subjects'];
            $years = $teachingDetails['years'];

            $selectedSection = isset($_POST['section']) ? $_POST['section'] : null;
            $selectedSubjectId = isset($_POST['subject']) ? $_POST['subject'] : null;
            $selectedYear = isset($_POST['year']) ? $_POST['year'] : null;
            $selectedMarksType = isset($_POST['marks_type']) ? $_POST['marks_type'] : null;
            $selectedExamSession = isset($_POST['exam_session']) ? $_POST['exam_session'] : null;
            $selectedTotalMarks = isset($_POST['total_marks']) ? $_POST['total_marks'] : null;

            $isEdit = false;
            if (isset($_GET['edit'])) {
                $isEdit = true;
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
                <label for="marks_type">Select Marks Type:</label>
                <select id="marks_type" name="marks_type" required>
                    <option value="">Select Marks Type</option>
                    <option value="Assignment" <?php echo ($selectedMarksType == 'Assignment') ? 'selected' : ''; ?>>
                        Assignment Marks</option>
                    <option value="Mid" <?php echo ($selectedMarksType == 'Mid') ? 'selected' : ''; ?>>Mid Marks</option>
                    ?>
                </select>

                <label for="exam_session">Select Exam Session:</label>
                <select id="exam_session" name="exam_session" required>
                    <option value="">Select Exam Session</option>
                    <option value="I" <?php echo ($selectedExamSession == 'I') ? 'selected' : ''; ?>>I</option>
                    <option value="II" <?php echo ($selectedExamSession == 'II') ? 'selected' : ''; ?>>II</option>
                    <option value="III" <?php echo ($selectedExamSession == 'III') ? 'selected' : ''; ?>>III</option>
                    ?>
                </select>

                <label for="total_marks">Enter Total Marks:</label>
                <input type="number" name="total_marks" id="total_marks" value="<?php echo $selectedTotalMarks ?>"
                    placeholder="Enter Total Marks" required>

                <input type="submit" name="<?php echo $isEdit ? "editMarks" : "getStudents" ?>"
                    value="<?php echo $isEdit ? "Edit Marks" : "Get Students" ?>">
            </form>
            <section class='students-form'>
                <?php
                if (isset($_POST['getStudents']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $subjectId = $_POST['subject'];
                    $year = $_POST['year'];
                    $section = $_POST['section'];
                    $marksType = $_POST['marks_type'];
                    $examSession = $_POST['exam_session'];
                    $totalMarks = $_POST['total_marks'];

                    $students = $student->getAllStudentofYearAndSection($year, $section);

                    echo "<h2>Upload Marks of " . $year . " - " . $section . "</h2>";
                    if (count($students) > 0) {
                        echo '<form method="post" action="handle_marks.php">';
                        foreach ($students as $student) {
                            echo '<label for=' . $student['student_id'] . '>' . $student['last_name'] . " " . $student['first_name'] . " " . $student['middle_name'] . '</label>';
                            echo '<input type="number" id="' . $student['student_id'] . '" name="students[]"/>';
                        }

                        echo '<div><h2> ---OR--- </h2></div><label for="marks_file">Upload Marks File: </label>';
                        echo '<input type="file" name="marks_file" id="marks_file" accept=".xls, .xlsx">';

                        echo '<input type="hidden" name="subjectId" value="' . $subjectId . '">';
                        echo '<input type="hidden" name="year" value="' . $year . '">';
                        echo '<input type="hidden" name="section" value="' . $section . '">';
                        echo '<input type="hidden" name="marks_type" value="' . $marksType . '">';
                        echo '<input type="hidden" name="exam_session" value="' . $examSession . '">';
                        echo '<input type="hidden" name="total_marks" value="' . $totalMarks . '">';

                        echo '<input type="Submit" name="addMarks" value="Upload Marks"></form>';
                    } else {
                        echo 'No Students Found';
                    }
                } else if (isset($_POST['editMarks']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

                    $subjectId = $_POST['subject'];
                    $year = $_POST['year'];
                    $section = $_POST['section'];
                    $marksType = $_POST['marks_type'];
                    $examSession = $_POST['exam_session'];
                    $totalMarks = $_POST['total_marks'];

                    $marksDetails = $marks->getMarks($section, $year, $subjectId, $marksType, $examSession);

                    echo "<h2>Edit Marks of " . $year . " Section " . $section . "</h2>";
                    if (count($marksDetails) > 0) {
                        echo '<form method="post" action="handle_marks.php">';
                        foreach ($marksDetails as $marks) {
                            echo '<label for="' . $marks['student_id'] . '">' . $marks['student_id'] . ' • ' . $marks['name'] . '</label>';
                            echo '<input type="number" id="' . $marks['student_id'] . '" name="marks[' . $marks['record_id'] . ']" value=' . $marks['marks_obtained'] . ' />';
                        }
                        echo '<input type="Submit" name="editMarks" value="Edit Marks"></form>';
                    } else {
                        echo 'No Students Found';
                    }
                }
                ?>
            </section>
        </section>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
<script>

    const studentsForm = document.querySelector('.students-form');
    const inputFields = studentsForm.querySelectorAll("input[type='number']");
    const totalMarksInput = document.getElementById('total_marks');

    function validateInputMarks() {
        totalMarks = parseInt(totalMarksInput.value);
        inputMarks = parseInt(this.value);
        if (inputMarks > totalMarks) {
            this.style.border = '2px solid red';
        } else {
            this.style.border = '2px solid green';
        }
    }

    inputFields.forEach((inputField) => {
        inputField.addEventListener('input', validateInputMarks);
    })
    const excelInputField = document.getElementById("marks_file");

    if (excelInputField) {

        excelInputField.addEventListener("change", (event) => {
            const selectedFile = event.target.files[0];

            if (selectedFile) {
                const filereader = new FileReader();

                filereader.onload = (e) => {
                    const data = e.target.result;
                    const workbook = XLSX.read(data, { type: 'binary' });

                    const sheetName = workbook.SheetNames[0];
                    const sheet = workbook.Sheets[sheetName];

                    XLSX.utils.sheet_to_json(sheet, { header: 1 }).forEach((row, rowIndex) => {
                        const studentId = row[0];
                        const marks = row[1];

                        let studentInput = document.getElementById(studentId);
                        studentInput.value = marks === null ? 0 : parseInt(marks);

                        totalMarks = parseInt(totalMarksInput.value);
                        inputMarks = parseInt(studentInput.value);
                        if (inputMarks > totalMarks) {
                            studentInput.style.border = '2px solid red';
                        } else {
                            studentInput.style.border = '2px solid green';
                        }
                    });
                };

                filereader.readAsBinaryString(selectedFile);
            }
        });
    }
</script>

</html>