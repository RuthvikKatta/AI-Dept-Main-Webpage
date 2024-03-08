<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['adminId']) && $_SESSION['loggedIn'] === true) {
    $adminId = $_SESSION['adminId'];
} else {
    header("Location: ../../login.php");
}

include '../../models/Attendance.php';
include '../../models/Subject.php';
include '../../models/ClassDetails.php';
include '../../models/TimeTable.php';
include '../../models/Media.php';
include '../../models/Material.php';
include '../../models/Student.php';
include '../../models/Staff.php';
include '../../models/Project.php';
include '../../models/Publication.php';
include '../../models/Mentoring.php';

$subject = new Subject();
$attendance = new Attendance();
$classDetails = new ClassDetails();
$timeTable = new TimeTable();
$media = new Media();
$material = new Material();
$student = new Student();
$staff = new Staff();
$project = new Project();
$publication = new Publication();
$mentoring = new Mentoring();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />

    <link rel="stylesheet" href="./dashboard.style.css" />
    <link rel="shortcut icon" href="../../../assets/images/favicon-icon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="/AI-MAIN-PAGE/Dependencies/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.js" defer></script>
    <title>Dashboard</title>
</head>

<body>
    <section class="vertical-navigation">
        <div class="navigation">
            <ul>
                <li><a href="#view-attendance">Attendance</a></li>
                <li><a href="#view-faculty-leisures">Faculty Leisure</a></li>
                <li><a href="#view-mentoring">View Mentoring</a></li>
                <li><a href="#view-media">View Media</a></li>
                <li><a href="#view-material">View Material</a></li>
                <li><a href="#view-students">View Students</a></li>
                <li><a href="#view-staff">View Staff</a></li>
                <li><a href="#view-projects">View Projects</a></li>
                <li><a href="#view-publications">View Publications</a></li>
                <li><a href="#view-subjects">View Subjects</a></li>
                <li><a href="#view-classes">View Class Details</a></li>
                <li><a href="#logout">Logout</a></li>
            </ul>
        </div>
    </section>
    <main>
        <section id="view-attendance">
            <?php
            $selectedYear = isset($_POST['year']) ? $_POST['year'] : null;
            $selectedSection = isset($_POST['section']) ? $_POST['section'] : null;
            ?>
            <form method="post">
                <label for="year">Select Year:</label>
                <select id="year" name="year" required>
                    <option value="">Select Year</option>
                    <option value="I" <?php echo ($selectedYear == 'I') ? 'selected' : ''; ?>>I</option>
                    <option value="II" <?php echo ($selectedYear == 'II') ? 'selected' : ''; ?>>II</option>
                    <option value="III" <?php echo ($selectedYear == 'III') ? 'selected' : ''; ?>>III</option>
                    <option value="IV" <?php echo ($selectedYear == 'IV') ? 'selected' : ''; ?>>IV</option>
                </select>

                <label for="section">Select Section:</label>
                <select id="section" name="section" required>
                    <option value="">Select Section</option>
                    <option value="A" <?php echo ($selectedSection == 'A') ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?php echo ($selectedSection == 'B') ? 'selected' : ''; ?>>B</option>
                </select>
                <input type="submit" name="getAttendance" value="Get Attendance">
            </form>

            <?php
            if (isset($_POST['getAttendance']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $year = $_POST['year'];
                $section = $_POST['section'];

                $currentSemester = $classDetails->getCurrentSemester($year, $section);
                $subjects = $subject->getSubjects($year, $currentSemester, $section);
                $subjectIds = array_column($subjects, 'subject_id');

                if (count($subjectIds) > 0) {
                    $rows = $attendance->getCummulativeAttendance($subjectIds, $year, $section);
                    $totalClasses = $attendance->getTotalClasses($subjectIds);

                    echo "<table id='cummulative-attendance'><thead><tr><th></th>";
                    foreach ($subjects as $subjectDetails) {
                        echo "<th>" . $subjectDetails['name'] . "</th>";
                    }
                    echo "</tr><tr><td>Total Classes</td>";

                    foreach ($totalClasses as $subjectClass) {
                        echo "<td>" . $subjectClass['total_classes'] . "</td>";
                    }
                    echo "</tr></thead><tbody>";

                    $totalClassesCount = array_sum(array_column($totalClasses, 'total_classes'));
                    $totalPresentClasses = array_fill_keys($subjectIds, 0);

                    foreach ($rows as $row) {
                        echo "<tr><td>$row[name]</td>";
                        foreach ($subjectIds as $subjectId) {
                            echo "<td>" . $row[$subjectId] . "</td>";
                            $totalPresentClasses[$subjectId] += $row[$subjectId];
                        }
                        echo "</tr>";
                    }

                    echo "</tbody></tr></table>";
                }
            }
            ?>
        </section>

        <section id="view-faculty-leisures">
            <?php
            $selectedYear = isset($_POST['year']) ? $_POST['year'] : null;
            $selectedSection = isset($_POST['section']) ? $_POST['section'] : null;
            ?>
            <form method="POST">
                <label for="date">Date: </label>
                <input type="date" name="date" id="date">
                <label for="start_time">From Time: </label>
                <input type="time" name="start_time" id="start_time">
                <label for="end_time">To Time: </label>
                <input type="time" name="end_time" id="end_time">
                <input type="submit" name="viewLeisure" value="View Faculty">
            </form>
            <?php

            if (isset($_POST['viewLeisure']) && $_SERVER['REQUEST_METHOD'] === "POST") {
                $date = $_POST['date'];
                $startTime = $_POST['start_time'];
                $endTime = $_POST['end_time'];
                $day = date('l', strtotime($date));

                $rows = $timeTable->getTodaysLeisures($day, $startTime, $endTime);
            }
            ?>
        </section>

        <section id="view-mentoring">
            <h2>All Mentors</h2>
            <?php
            $mentors = $staff->getAllStaff();
            echo "<table>
                        <thead>
                            <tr>
                            <th>Mentor ID</th>
                            <th>Name</th>
                            <th>View Mentees</th>
                            </tr>
                        </thead><tbody>";
            if (count($mentors) > 0) {
                foreach ($mentors as $m) {
                    $mentorId = $m['staff_id'];
                    $sd = $staff->getStaffDetails($mentorId);
                    $mentorName = $sd['last_name'] . " " . $sd['first_name'] . " " . $sd['middle_name'];
                    echo "<tr>
                                <td>{$mentorId}</td>
                                <td>$mentorName</td>
                                <td><a href='./utils/edit_mentees.php?id=$mentorId'>View Mentees</a></td>
                                </tr>";
                }
                echo "</tbody></table>";
            }
            ?>
        </section>

        <section id="view-media">
            <h2>Uploaded Media</h2>
            <a href='./utils/add_media.php' class='btn btn-add'>Add Images</a>
            <table>
                <thead>
                    <tr>
                        <th>SNo</th>
                        <th>Image</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $carousalImages = $media->getCarousalImages();
                    if (count($carousalImages) > 0) {
                        $index = 1;
                        foreach ($carousalImages as $image) {
                            echo "
                            <tr>
                            <td>" . $index++ . "</td>
                            <td><img src='../../../Database/Carousal Images/{$image["file_name"]}' style='height:80px;' ></td>
                            <td><a href='./utils/delete_media.php?id={$image["id"]}&name={$image["file_name"]}' class='btn btn-danger'>Delete</a></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No Images Found</td></tr>";
                    }
                    echo "</tbody>";
                    ?>
            </table>
        </section>

        <section id="view-material">
            <h2>Uploaded Materials</h2>
            <a href='./utils/add_material.php' class='btn btn-add'>Add Material</a>
            <table>
                <thead>
                    <tr>
                        <th>SNo</th>
                        <th>File Name</th>
                        <th>File Type</th>
                        <th>View File</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $materialTypeDescriptions = [
                        'AC' => 'Academic Calendar',
                        'SM' => 'Study Material',
                        'PQP' => 'Previous Question Paper',
                    ];
                    $materials = $material->getAllMaterials();
                    if (count($materials) > 0) {
                        $index = 1;
                        foreach ($materials as $mat) {
                            echo "
                            <tr>
                                <td>" . $index++ . "</td>
                                <td>$mat[name]</td>
                                <td>" . $materialTypeDescriptions[$mat['material_type']] . "</td>
                                <td><a target='_BLANK' href='/AI-Main-Page/Database/Material/$mat[name]' class='btn btn-view'>View File</a></td>
                                <td><a href='./utils/delete_material.php?id={$mat["material_id"]}&name={$mat["name"]}' class='btn btn-danger'>Delete</a></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No Materials Found</td></tr>";
                    }
                    echo "</tbody>";
                    ?>
            </table>
        </section>

        <section id="view-students">
            <form method="post">
                <h2 class='form-title'>Student Selection form</h2>

                <label for="year">Select Year:</label>
                <select id="year" name="year" required>
                    <option value="">Select Year</option>
                    <option value="I" <?php echo ($selectedYear == 'I') ? 'selected' : ''; ?>>I</option>
                    <option value="II" <?php echo ($selectedYear == 'II') ? 'selected' : ''; ?>>II</option>
                    <option value="III" <?php echo ($selectedYear == 'III') ? 'selected' : ''; ?>>III</option>
                    <option value="IV" <?php echo ($selectedYear == 'IV') ? 'selected' : ''; ?>>IV</option>
                </select>

                <label for="section">Select Section:</label>
                <select id="section" name="section" required>
                    <option value="">Select Section</option>
                    <option value="A" <?php echo ($selectedSection == 'A') ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?php echo ($selectedSection == 'B') ? 'selected' : ''; ?>>B</option>
                </select>
                <input type="submit" name="view-students" value="View Students">
            </form>
            <a href='./utils/add_student.php' class='btn btn-add'>Add Student</a>
            <table>
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Name</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_POST['view-students'])) {
                        $year = $_POST['year'];
                        $section = $_POST['section'];

                        $students = $student->getAllStudentofYearAndSection($year, $section);

                        if (count($students) > 0) {
                            foreach ($students as $s) {
                                echo "<tr>
                            <td>" . $s['student_id'] . "</td>
                            <td>" . $s['last_name'] . " " . $s['first_name'] . " " . $s['middle_name'] . "</td>
                            <td><a href='./utils/edit_student.php?id=$s[student_id]'>Edit</a></td>
                        </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No Data</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section id="view-staff">
            <h2>Staff of AI</h2>
            <a href='./utils/add_staff.php' class='btn btn-add'>Add Staff</a>
            <table>
                <thead>
                    <tr>
                        <th>Staff Id</th>
                        <th>Name</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $staffDetails = $staff->getAllStaff();

                    if (count($staffDetails) > 0) {
                        foreach ($staffDetails as $s) {
                            echo "<tr>
                        <td>" . $s['staff_id'] . "</td>
                        <td>" . $s['last_name'] . " " . $s['first_name'] . " " . $s['middle_name'] . "</td>
                        <td><a href='./utils/edit_staff.php?id=$s[staff_id]'>Edit</a></td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No Data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section id="view-projects">
            <h2>All Projects</h2>
            <a href='./utils/add_project.php' class='btn btn-add'>Add Project</a>
            <table style="width: 60vw">
                <thead>
                    <tr>
                        <th>Project Id</th>
                        <th>Project Name</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $projects = $project->getProjects('', '', '');

                    if (count($projects) > 0) {
                        foreach ($projects as $p) {
                            echo "<tr>
                            <td>" . $p['project_id'] . "</td>
                            <td>" . $p['title'] . "</td>
                            <td><a href='./utils/delete_project.php?id=$p[project_id]'>Delete</a></td>
                        </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No Data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section id="view-publications">
            <h2>All Publications</h2>
            <a href='./utils/add_publication.php' class='btn btn-add'>Add Publication</a>
            <table style="width: 60vw">
                <thead>
                    <tr>
                        <th>Publication Id</th>
                        <th>Title</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $publications = $publication->getPublications('', '', '');

                    if (count($publications) > 0) {
                        foreach ($publications as $p) {
                            echo "<tr>
                            <td>" . $p['publication_id'] . "</td>
                            <td>" . $p['title'] . "</td>
                            <td><a href='./utils/delete_publication.php?id=$p[publication_id]'>Delete</a></td>
                        </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No Data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section id="view-subjects">
            <h2>All Subjects</h2>
            <a href='./utils/add_subject.php' class='btn btn-add'>Add Subject</a>
            <table>
                <thead>
                    <tr>
                        <th>Subject Id</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Type</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subjects = $subject->getAllSubjects();

                    if (count($subjects) > 0) {
                        foreach ($subjects as $s) {
                            echo "<tr>
                            <td>" . $s['subject_id'] . "</td>
                            <td>" . $s['name'] . "</td>
                            <td>" . $s['credits'] . "</td>
                            <td>" . $s['type'] . "</td>
                            <td><a href='./utils/edit_subject.php?id=$s[subject_id]'>Edit</a></td>
                        </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No Data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section id="view-classes">
            <h2>All Classes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Class Id</th>
                        <th>Year</th>
                        <th>Section</th>
                        <th>Semester</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $classes = $classDetails->getAllClasses();

                    if (count($classes) > 0) {
                        foreach ($classes as $c) {
                            echo "<tr>
                            <td>" . $c['class_id'] . "</td>
                            <td>" . $c['year'] . "</td>
                            <td>" . $c['section'] . "</td>
                            <td>" . $c['current_semester'] . "</td>
                            <td><a href='./utils/edit_classdetails.php?id=$c[class_id]'>Edit</a></td>
                        </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No Data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section id="logout">
            <h2>Are you sure want to Logout?</h2>
            <a href='../../logout.php?logout=true' class='logout'>Logout</a>
        </section>

        <!-- TODO: class details should have feature to add subjects just like mentoring -->
    </main>
</body>

<script src="./script.js" type="module"></script>

</html>