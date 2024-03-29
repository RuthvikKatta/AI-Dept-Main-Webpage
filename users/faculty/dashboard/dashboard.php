<?php

session_start();

if (isset($_SESSION['loggedIn']) && isset($_SESSION['facultyId']) && $_SESSION['loggedIn'] === true) {
  $facultyId = $_SESSION['facultyId'];
} else {
  header("Location: ../../login.php");
}

include '../../models/Leave.php';
include '../../models/Mentoring.php';
include '../../models/Student.php';
include '../../models/Staff.php';
include '../../models/Subject.php';
include '../../models/Attendance.php';
include '../../models/Marks.php';
include '../../models/ClassDetails.php';
include '../../models/TimeTable.php';

$leave = new Leave();
$Mentoring = new Mentoring();
$Student = new Student();
$staff = new Staff();
$subject = new Subject();
$attendance = new Attendance();
$marks = new Marks();
$classDetails = new ClassDetails();
$timeTable = new TimeTable();

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
  <section class="vertical-navigation">
    <div class="navigation">
      <ul>
        <li><a href="#profile">Profile</a></li>
        <li><a href="#timetable">Time Table</a></li>
        <li><a href="#marks">Marks</a></li>
        <li><a href="#attendance">Attendance</a></li>
        <li><a href="#mentoring">Mentoring</a></li>
        <li><a href="#leave">Apply Leave</a></li>
        <li><a href="#logout">Logout</a></li>
      </ul>
    </div>
  </section>

  <main>
    <section id="profile">
      <?php
      $row = $staff->getStaffDetails($facultyId);

      $profileImagePath = '../../../Database/Staff/' . $row['staff_id'] . '.jpeg';

      if (!file_exists($profileImagePath)) {
        $profileImagePath = '../../../Database/Staff/' . $row['staff_id'] . '.jpg';

        if (!file_exists($profileImagePath)) {
          $profileImagePath = '../../../assets/Icons/' . ($row['gender'] == 'Male' ? 'Male.png' : 'Female.png');
        }
      }

      $designation_id = $row['designation_id'];
      $designation_result = $staff->getDesignation($designation_id);
      $designation_title = $designation_result['title'];
      ?>
      <div class="profile-container">
        <div class="profile-image">
          <img src="<?php echo $profileImagePath ?>" alt="Profile Image">
        </div>
        <div class="profile-info">
          <h2 class="profile-name">
            <?php echo $row['salutation'] . ' ' . $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?>
          </h2>
          <p>
            <?php echo 'Qualification: ' . $row['qualification']; ?>
          </p>
          <p>
            <?php echo 'Role: ' . $designation_title; ?>
          </p>
          <p>
            <?php echo 'Experience: ' . $row['experience_years'] . ' years'; ?>
          </p>
        </div>
        <div class="contact-details">
          <p>
            <?php echo 'Mobile Number: ' . $row['mobile_number']; ?>
          </p>
          <p>
            <?php echo 'Email: ' . $row['email']; ?>
          </p>
        </div>
      </div>
    </section>

    <section id="timetable">
      <h2>Your TimeTable</h2>
      <table>
        <thead>
          <tr height="45px">
            <th>DAY\TIME</th>
            <th>9:00-10:00</th>
            <th>10:00-11:00</th>
            <th>11:00-12:00</th>
            <th>12:00-12:45</th>
            <th>12:45-1:45</th>
            <th>1:45-2:45</th>
            <th>2:45-3:45</th>
          </tr>
        </thead>
        <tbody>
        <tbody>
          <?php
          $tt = $timeTable->getTimetableForFaculty($facultyId);
          $adjustments = $leave->getAdjustmentsByFaculty($facultyId);

          $weekdayMap = array(
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
          );

          foreach ($tt as $day => $schedule) {
            echo "<tr><th>$day</th>";

            for ($hour = 1; $hour <= 6; $hour++) {
              if ($hour == 4) {
                echo "<th>Lunch</th>";
              }

              $adjustmentFound = false;
              foreach ($adjustments as $adjustment) {
                $adjustmentWeekday = date('l', strtotime($adjustment['date']));

                if ($adjustmentWeekday == $day && $adjustment['hour'] == $hour) {
                  echo "<td class='adjusted'>{$adjustment['name']}<br><span>(adjusted)</span></td>";
                  $adjustmentFound = true;
                  break; 
                }
              }

              if (!$adjustmentFound) {
                echo "<td>" . (count($schedule[$hour]) > 0 ? ($schedule[$hour]['subjectName']) : ' ') . "</td>";
              }
            }
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
      
    </section>

    <section id="marks">
      <a href='./marks_page.php'>Upload Marks</a>
      <a href='./marks_page.php?edit=true'>Edit Marks</a>
      <section class="view-marks">
        <?php
        $teachingDetails = $subject->getTeachingDetails($facultyId);
        $subjects = $teachingDetails['subjects'];
        $years = $teachingDetails['years'];

        $selectedSection = isset($_POST['section']) ? $_POST['section'] : null;
        $selectedSubjectId = isset($_POST['subject']) ? $_POST['subject'] : null;
        $selectedYear = isset($_POST['year']) ? $_POST['year'] : null;
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
          <input type="submit" name="viewMarks" value="View Marks">
        </form>
        <?php
        if (isset($_POST['viewMarks']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
          $year = $_POST['year'];
          $section = $_POST['section'];
          $subjectId = $_POST['subject'];
          $subjectName = $subject->getSubjectName($subjectId);
          $result = $marks->getOverallMarks($year, $section, $subjectId);

          echo "<h2>Marks Record for $year - $section - $subjectName[name]</h2>
              <table>
                <tr>
                  <th>Student Id</th>
                  <th>Mid I</th>
                  <th>Assignment I</th>
                  <th>Mid II</th>
                  <th>Assignment II</th>
                  <th>Mid III</th>
                  <th>Assignment III</th>
                </tr>";

          if (count($result) > 0) {
            foreach ($result as $record) {
              echo "<tr><td>" . $record['student_id'] . "</td>
                      <td>" . validateMarks($record['Mid I']) . "</td>
                      <td>" . validateMarks($record['Assignment I']) . "</td>
                      <td>" . validateMarks($record['Mid II']) . "</td>
                      <td>" . validateMarks($record['Assignment II']) . "</td>
                      <td>" . validateMarks($record['Mid III']) . "</td>
                      <td>" . validateMarks($record['Assignment III']) . "</td>
                      </tr>";
            }
          } else {
            echo "<tr><td colspan='7'>No data Exists</td></tr>";
          }
          echo "</table>";
        }
        ?>
      </section>
    </section>

    <section id="attendance">
      <a href='./attendance_page.php'>Take Attendance</a>
      <section class="prev-attendance">
        <h2>Attendance Record</h2>
        <table>
          <tr>
            <th>S.No</th>
            <th>Class Year</th>
            <th>Class Section</th>
            <th>Subject</th>
            <th>Taken On</th>
            <th>Edit Attendance</th>
          </tr>
          <?php
          $prevRecords = $attendance->getAttendanceLogsByFacultyId($facultyId);
          if (count($prevRecords) > 0) {
            $sno = 1;
            foreach ($prevRecords as $record) {
              $subjectName = $subject->getSubjectName($record['subject_id']);
              echo '<tr> <td>' . $sno++ . '</td>';
              echo '<td>' . $record['class_year'] . '</td>';
              echo '<td>' . $record['class_section'] . '</td>';
              echo '<td>' . $subjectName['name'] . '</td>';
              echo '<td>' . $record['taken_on'] . '</td>';
              echo '<td><a href="./attendance_page.php?edit=true&attendanceId=' . $record['log_no'] . '">View Attendance</a></td> </tr>';
            }
          } else {
            echo '<tr><td colspan="6" style="text-align:center;">No Records Found</td></tr>';
          }
          ?>
        </table>
      </section>
    </section>

    <section id="mentoring">
      <div class="mentee-details">
        <?php
        $mentees = $Mentoring->getMentees($facultyId);
        if (count($mentees) > 0) {
          foreach ($mentees as $key => $mentee) {
            $studentId = $mentee['mentee_id'];
            $studentDetails = $Student->getStudentDetails($studentId);

            $activeClass = ($key === 0) ? 'active' : '';

            echo "<div class='mentee $activeClass'>";
            echo "<h2><strong>" . $studentId . " - " . $studentDetails['last_name'] . " " . $studentDetails['first_name'] . " " . $studentDetails['middle_name'] . "</strong></h2>";

            echo "<div class='toggle-sections'>
                    <ul>
                      <li class='active'>Comments</li>
                      <li>QnA</li>
                      <li>Attendance</li>
                      <li>Mid Marks</li>
                      <li>Backlog Record</li>
                    </ul>
                  </div>";

            $prevComments = $Mentoring->getMenteePrevComments($studentId);
            echo "<div class='comments-section active'>";
            if (count($prevComments) > 0) {
              foreach ($prevComments as $comment) {
                echo "<div class='comment'>
                        <h5>Comment on $comment[posted_date]: " . $comment['comment'] . "</h5> 
                      </div>";
              }
            } else {
              echo "<h5>No previous comments found</h5>";
            }
            echo "</div>";

            $prevQnA = $Mentoring->getQnA($facultyId, $studentId);
            echo "<div class='QnA-section'>";
            if (count($prevQnA['question']) > 0) {
              foreach ($prevQnA['question'] as $index => $question) {
                echo "<div class='QnA'>
                        <h4>Question: " . $question . "?</h4> 
                        <h5>Answer: " . $prevQnA['answer'][$index] . "</h5> 
                      </div>";
              }
            } else {
              echo "<h5>No previous QnA found</h5>";
            }
            echo "</div>";

            echo "<div class='attendance'>";

            $year = $studentDetails['year'];
            $section = $studentDetails['section'];

            $current_semester = $classDetails->getCurrentSemester($year, $section);
            $subjects = $subject->getSubjects($year, $current_semester, $section);
            $subjectIds = array_column($subjects, 'subject_id');

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
              if ($totalClassesCount[$subjectId] == 0) {
                echo "<td>0%</td>";
              } else {
                $percentage = ($totalPresentClasses[$subjectId] / $totalClassesCount[$subjectId]) * 100;
                echo "<td>" . round($percentage, 2) . "%</td>";
              }
            }
            echo "</tr></table>";

            echo "</div>
              <div class='marks'>";
            $rows = $marks->getOverallMarksOfStudents($year, $section, $studentId, $subjectIds);

            echo "<table>
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
            echo "</table>
                    </div>
                    <div class='backlogs-report'>";
            $rows = $marks->getBacklogsByStudentId($studentId, 'Active');

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
            echo "</table></div>";

            echo "<div class='add-question'>
              <form method='post' action='process_qa.php?mentor_id=$facultyId$&mentee_id=$studentId'>
                  <input type='text' name='question' id='question_$studentId' placeholder='Type your Question'>
                  <input type='text' name='answer' id='answer_$studentId' placeholder='Enter Answer'>
                  <button type='submit'>Add</button>
              </form>
            </div>";
            echo "<div class='add-comment'>
              <form method='post' action='process_comment.php?mentor_id=$facultyId$&mentee_id=$studentId'>
                  <label for='newComment_$studentId'>Add new comment:</label>
                  <input type='text' name='newComment' id='newComment_$studentId' placeholder='Type your comment'>
                  <button type='submit'>Add</button>
              </form>
            </div></div>";
          }
        }
        ?>
      </div>

      <div class="mentees">
        <?php
        if (count($mentees) > 0) {
          echo "<ul><h2>Select Mentee</h2>";

          foreach ($mentees as $key => $mentee) {
            $studentId = $mentee['mentee_id'];
            $activeClass = ($key === 0) ? 'active' : '';

            echo "<li class='mentee-item $activeClass' data-student-id='$studentId'>$studentId</li>";
          }

          echo "</ul>";
        } else {
          echo "<p>Mentees don't exist</p>";
        }
        ?>
      </div>
    </section>

    <section id="leave">
      <div class="leave-apply">
        <h2>Apply for a Leave</h2>
        <form action="./apply_leave.php" method="post">
          <?php
          $records = $leave->getAppliedLeavesOfFaculty($facultyId, 'Approved');
          $leavesLeft = 15;
          if (count($records) > 0) {
            foreach ($records as $rec) {
              $leavesLeft -= intval($rec['total_days']);
            }
          }
          ?>

          <label for="leaves_left">Available Leaves:</label>
          <input type="text" id="leaves_left" name="leaves_left" value="<?php echo $leavesLeft ?>" disabled required>

          <label for="fromDate">From Date:</label>
          <input type="date" id="fromDate" name="fromDate" required onchange="updateDate()">

          <label for="toDate">To Date:</label>
          <input type="date" id="toDate" name="toDate" required oninput="calculateTotalDays()">

          <label for="totalDays">Total Days:</label>
          <input type="text" id="totalDays" name="totalDays" readonly>

          <label for="reason">Reason for Leave:</label>
          <textarea id="reason" name="reason" rows="4" required></textarea>

          <div>
            <h2>Add Adjustments</h2>
          </div>
          <div id="adjustments">
            <div class="adjustment">
              <input type="date" name="adjustment_date[]" onchange="updateDate()" required>

              <input type="number" name="adjustment_hour[]" min=1 max=6 placeholder="choose Hour" required>

              <select name="adjustment_year[]" required>
                <option value="">Select Year</option>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
              </select>

              <select name="adjustment_section[]" required>
                <option value="">Select Section</option>
                <option value="A">A</option>
                <option value="B">B</option>
              </select>

              <select name="adjustment_subject[]" required>
                <option value="">Select Subject</option>
                <?php
                $subjects = $subject->getAllSubjects();
                if (count($subjects) > 0) {
                  foreach ($subjects as $s) {
                    echo "<option value='" . $s['subject_id'] . "'>" . $s['name'] . "</option>";
                  }
                }
                ?>
              </select>

              <select name="adjustment_faculty[]" required>
                <option value="">Choose Faculty</option>
                <?php
                $staff = $staff->getAllStaff();
                if (count($staff) > 0) {
                  foreach ($staff as $s) {
                    if($s['staff_id'] == $facultyId){
                      continue;
                    }
                    echo "<option value='" . $s['staff_id'] . "'>" . $s['last_name'] . " " . $s['first_name'] . " " . $s['middle_name'] . "</option>";
                  }
                }
                ?>
              </select>

              <button type="button" class="btn-remove" onclick="removeAdjustment(this)">Remove</button>
            </div>
          </div>
          <button type="button" class="btn-add" onclick="addAdjustment()">Add Adjustment</button>

          <button type="submit" class="button">Submit Leave Application</button>
        </form>
      </div>

      <div class="leave-record">
        <h2>Previous Leave Records</h2>
        <table>
          <tr>
            <th>Applied On</th>
            <th>Applied From</th>
            <th>Applied To</th>
            <th>Total Days</th>
            <th>Reason</th>
            <th>Adjustments</th>
            <th>Status</th>
          </tr>
          <?php
          $prevRecords = $leave->getPreviousRecords($facultyId);
          if (!empty($prevRecords)) {
            foreach ($prevRecords as $record) {

              $adjustmentRecords = $leave->getPreviousAdjustments($record['leave_id']);

              $adjustments = '';
              if (count($adjustmentRecords) > 0) {
                foreach ($adjustmentRecords as $rec) {
                  $adjustments .= $rec['date'] . ", " . $rec['year'] . "-" . $rec['section'] . ", " . $rec['hour'] . " Hr" . ", " . $rec['name'] . ", " . $rec['adjusted_with'] . "<br>";
                }
              }

              echo '<tr> <td>' . $record['applied_on'] . '</td>';
              echo '<td>' . $record['applied_from'] . '</td>';
              echo '<td>' . $record['applied_to'] . '</td>';
              echo '<td>' . $record['total_days'] . '</td>';
              echo '<td>' . $record['reason'] . '</td>';
              echo '<td>' . $adjustments . '</td>';
              echo '<td>' . $record['status'] . '</td> </tr>';
            }
          } else {
            echo '<tr><td colspan="7" style="text, align:center;">No Records Found</td></tr>';
          }
          ?>
        </table>
      </div>
    </section>

    <section id="logout">
      <h2>Are you sure want to Logout?</h2>
      <a href='../../logout.php?logout=true' class='logout'>Logout</a>
    </section>
  </main>
</body>
<script src="./script.js"></script>

</html>