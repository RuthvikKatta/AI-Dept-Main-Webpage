<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/ClassDetails.php';
include '../../../models/Subject.php';
include '../../../models/Staff.php';

$classDetails = new ClassDetails();
$subject = new Subject();
$staff = new Staff();

$classId = $_GET['id'];
if(empty($classId)) {
    header("Location: ../dashboard.php#view-classes");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Class Details</title>

    <style>
        form#newSubjectForm {
            grid-template-columns: 1fr;
        }

        #addMenteeButton {
            margin: 0 auto;
        }

        input[type="text"] {
            margin: 0.25rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 8px;
        }

        input[type="text"]:focus {
            border-color: black;
            outline: none;
        }

        .remove-button {
            padding: 0px 10px;
            border-radius: 50%;
            border: none;
            outline: none;
            color: white;
            font-size: larger;
            background-color: red;
            cursor: pointer;
        }

        .remove-button:focus {
            border: 1px solid black; 
        }
    </style>
</head>

<body>
    <h2>Class Details Edit Form</h2>
    <a href="../dashboard.php#view-classes" class='btn-back'>Back to dashboard</a>
    <form method="POST">
        <?php
        $classDetailsData = $classDetails->getClassDetails($classId);
        if (count($classDetailsData) > 0) {
            ?>
            <label for='subject_id'>Class ID: </label>
            <input type='text' id='subject_id' value='<?php echo $classDetailsData['class_id']; ?>' readonly>

            <label for='year'>Year: </label>
            <input type='text' id='year' value='<?php echo $classDetailsData['year']; ?>' readonly>

            <label for='section'>Section: </label>
            <input type='text' id='section' value='<?php echo $classDetailsData['section']; ?>' readonly>

            <label for='updated_semester'>Semester: </label>
            <select id='updated_semester' name='updated_semester'>
                <option value='1' <?php echo ($classDetailsData['current_semester'] == '1') ? 'selected' : ''; ?>>1</option>
                <option value='2' <?php echo ($classDetailsData['current_semester'] == '2') ? 'selected' : ''; ?>>2</option>
            </select>

            <label for='lunch_hour'>Lunch Hour: </label>
            <select id='lunch_hour' name='lunch_hour'>
                <option value='4' <?php echo ($classDetailsData['lunch_hour'] == '4') ? 'selected' : ''; ?>>12:00 - 12:45</option>
                <option value='5' <?php echo ($classDetailsData['lunch_hour'] == '5') ? 'selected' : ''; ?>>1:00 - 1:45</option>
            </select>

            <input type='submit' name='update-details' value='Update Details'>
            <?php
        }
        ?>
    </form>
    <?php
    if (isset($_POST['update-details'])) {
        $updatedSemester = $_POST['updated_semester'];
        $lunchHour = $_POST['lunch_hour'];

        $success = $classDetails->updateClassDetails($classId, $updatedSemester, $lunchHour);

        $message = $success ? "Class details updated successfully" : "Failed to update Class details";

        echo "
        <script>
            alert('$message');
            window.location.href = '../dashboard.php#view-classes';
        </script>";
    }
    ?>
    <h2>Subjects of this Class</h2>
    <table>
        <thead>
            <tr>
                <th>Subject ID</th>
                <th>Name</th>
                <th>Taught by</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $class = $classDetails->getSubjectDetailsByClassId($classId);
            if (count($class) > 0) {
                foreach ($class as $c) {
                    $subjectId = $c['subject_id'];
                    $sd = $subject->getSubjectName($subjectId);
                    $taughtBy = $c['taught_by'];
                    $staffName = $staff->getStaffDetails($taughtBy);
                    echo "<tr>
                    <td>{$subjectId}</td>
                    <td>{$sd['name']}</td>
                    <td>{$staffName['last_name']} {$staffName['first_name']} {$staffName['middle_name']}</td>
                    <td><a href='./remove_subjectForClass.php?class_id={$classId}&subject_id={$subjectId}&taught_by={$taughtBy}'>Remove</a></td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No Subjects available for this Class.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <form id='newSubjectForm' method="POST" action="./add_subjectsToClass.php">
        <div id="addSubjectInput"></div>
        <button type="button" id="addSubjectButton" class="btn btn-add">Add New Subject</button>
        <input type="hidden" name="class_id" value="<?php echo $classId ?>">
        <input type="submit" id="submitButton" name="add-subjects" value="Add Subjects" disabled>
    </form>
</body>
<script>
    let subjectCount = 1;

    function removeSubjectInput(inputId) {
        const removeButtonToRemove = document.getElementById(`removeButton${inputId}`);
        const subjectInputToRemove = document.getElementById(`subject${inputId}`);
        const taughtInputToRemove = document.getElementById(`taught_by${inputId}`);
        if (removeButtonToRemove) {
            subjectInputToRemove.remove();
            taughtInputToRemove.remove();
            removeButtonToRemove.remove();
            subjectCount--;

            document.getElementById('submitButton').disabled = subjectCount > 0 ? false : true;
        }
    }

    function addSubjectInput() {
        const inputContainer = document.getElementById('addSubjectInput');
        const inputId = `${subjectCount}`;
        const input = document.createElement('div');

        input.innerHTML = `
        <select name="newSubjects[]" id="subject${subjectCount}" required>
            <option value="">Choose Subject</option>
            <?php
            $subjects = $subject->getAllSubjects();
            if (count($subjects) > 0) {
                foreach ($subjects as $s) {
                    echo "<option value='" . $s['subject_id'] . "'>" . $s['name'] . "</option>";
                }
            }
            ?>
        </select>
        <select name="newTaughtBy[]" id="taught_by${subjectCount}" required>
            <option value="">Choose Faculty</option>
            <?php
            $staff = $staff->getAllStaff();
            if (count($staff) > 0) {
                foreach ($staff as $s) {
                    echo "<option value='" . $s['staff_id'] . "'>" . $s['last_name'] . " " . $s['first_name'] . " " . $s['middle_name'] . "</option>";
                }
            }
            ?>
        </select>
        <button type="button" class="remove-button" id="removeButton${inputId}" onclick="removeSubjectInput('${inputId}')"> - </button>
    `;

        inputContainer.appendChild(input);
        subjectCount++;

        document.getElementById('submitButton').disabled = false;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('addSubjectButton').addEventListener('click', addSubjectInput);
    });
</script>

</html>