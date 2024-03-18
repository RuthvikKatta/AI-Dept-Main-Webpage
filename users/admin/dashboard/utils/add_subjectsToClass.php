<?php

include "../../../models/ClassDetails.php";

$classDetails = new ClassDetails();

$classId = isset($_POST['class_id']) ? $_POST['class_id'] : '';

if (isset($_POST['add-subjects'])) {

    $subjectIds = isset($_POST['newSubjects']) ? array_map('trim', $_POST['newSubjects']) : [];
    $teacherIds = isset($_POST['newTaughtBy']) ? array_map('trim', $_POST['newTaughtBy']) : [];

    $subjectDetails = [];
    foreach ($subjectIds as $subjectId => $s) {
        $subjectDetails[] = [
            'subject_id' => $s, 
            'taught_by' => $teacherIds[$subjectId]
        ];
    }

    print_r($subjectDetails);

    if (empty($subjectIds) || in_array('', $subjectIds) || empty($teacherIds) || in_array('', $teacherIds)) {
        $message = "Error: Subject Details cannot be empty.";
    } else {
        $message = $classDetails->addSubjectsToClass($classId, $subjectDetails);
    }

    echo "
        <script>
            alert('$message');
            window.location.href = '../dashboard.php#view-classes';
        </script>";
}

if ($classId == '') {
    header("Location: ../dashboard.php#view-classes");
    exit();
}
header("Location: ./edit_classdetails.php?id=$classId");
exit();

?>