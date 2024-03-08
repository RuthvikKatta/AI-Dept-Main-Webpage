<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../../login.php");
}

include '../../../models/Student.php';
include '../../../models/Mentoring.php';
include '../../../models/Staff.php';

$student = new Student();
$mentoring = new Mentoring();
$staff = new Staff();

$mentorId = $_GET['id'];
if(empty($mentorId)) {
    header("Location: ../dashboard.php#view-mentoring");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dashboard.style.css" />
    <link rel="shortcut icon" href="../../../../assets/images/favicon-icon.png" type="image/x-icon">

    <title>Mentee Details</title>

    <style>
        form {
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
    <h2>Mentees</h2>
    <a href="../dashboard.php#view-mentoring" class='btn-back'>Back to dashboard</a>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Year</th>
                <th>Section</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $mentees = $mentoring->getMentees($mentorId);

            if (count($mentees) > 0) {


                foreach ($mentees as $mentee) {
                    $menteeId = $mentee['mentee_id'];
                    $sd = $student->getStudentDetails($menteeId);
                    $name = $sd['last_name'] . ' ' . $sd['first_name'] . ' ' . $sd['middle_name'];
                    $year = $sd['year'];
                    $section = $sd['section'];
                    echo "<tr>
                    <td>{$menteeId}</td>
                    <td>{$name}</td>
                    <td>{$year}</td>
                    <td>{$section}</td>
                    <td><a href='./remove_mentee.php?mentor_id={$mentorId}&mentee_id={$menteeId}'>Remove</a></td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No Mentees available for Mentor</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <form method="POST" action="./add_mentees.php">
        <div id="addMenteeInput"></div>
        <button type="button" id="addMenteeButton" class="btn btn-add">Add New Mentee</button>
        <input type="hidden" name="mentor_id" value="<?php echo $mentorId ?>">
        <input type="submit" id="submitButton" name="add-mentees" value="Add Mentees" disabled>
    </form>
</body>
<script>
    let menteeCount = 0;

    function removeMenteeInput(inputId) {
        const inputToRemove = document.getElementById(inputId);
        const removeButtonId = `removeButton${inputId}`;
        const removeButtonToRemove = document.getElementById(removeButtonId);
        if (inputToRemove) {
            inputToRemove.remove();
            removeButtonToRemove.remove();
            menteeCount--;

            document.getElementById('submitButton').disabled = menteeCount > 0 ? false : true;
        }
    }

    function addMenteeInput() {
        const inputContainer = document.getElementById('addMenteeInput');
        const inputId = `menteeInput${menteeCount}`;
        const input = document.createElement('div');
        input.innerHTML = `
        <input type="text" id="${inputId}" name="newMenteeIds[]" placeholder="Enter Student ID">
        <button type="button" class="remove-button" id="removeButton${inputId}" onclick="removeMenteeInput('${inputId}')"> - </button>`;
        inputContainer.appendChild(input);
        menteeCount++;

        document.getElementById('submitButton').disabled = false;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('addMenteeButton').addEventListener('click', addMenteeInput);
    });
</script>

</html>