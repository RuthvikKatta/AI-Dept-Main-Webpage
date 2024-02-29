<?php

session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
  $facultyId = $_SESSION['facultyId'];
} else {
  header("Location: ../login/login.php");
}

include '../../models/Leave.php';
include '../../models/Mentoring.php';
include '../../models/Student.php';
include '../../models/Faculty.php';
include '../../models/Subject.php';
include '../../models/Attendance.php';
include '../../models/Marks.php';

$Leave = new Leave();
$Mentoring = new Mentoring();
$Student = new Student();
$faculty = new Faculty();
$subject = new Subject();
$attendance = new Attendance();
$marks = new Marks();

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
      $row = $faculty->getFacultyDetails($facultyId);

      $profile_image = $row['profile_image_link'] == '' ?
        '/AI-MAIN-PAGE/assets/Icons/' . ($row['gender'] == 'Male' ? 'Male.png' : 'Female.png') :
        '/AI-MAIN-PAGE/' . $row['profile_image_link'];

      $designation_id = $row['designation_id'];
      $designation_result = $faculty->getDesignation($designation_id);
      $designation_title = $designation_result['title'];
      ?>
      <div class="profile-container">
        <div class="profile-image">
          <img src="<?php echo $profile_image ?>" alt="Profile Image">
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
                echo '<option value="' . $s['subject_id'] . '" ' . ($selectedSubjectId == $s['subject_id'] ? 'selected' : '') . '>' . $s['subject_name'] . '</option>';
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

          echo "<h2>Marks Record for $year - $section - $subjectName[subject_name]</h2>
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
                      <td>" . $record['Mid I'] . "</td>
                      <td>" . $record['Assignment I'] . "</td>
                      <td>" . $record['Mid II'] . "</td>
                      <td>" . $record['Assignment II'] . "</td>
                      <td>" . $record['Mid III'] . "</td>
                      <td>" . $record['Assignment III'] . "</td>
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
              echo '<td>' . $subjectName['subject_name'] . '</td>';
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
            echo "<h2><strong>" . $studentId . " - " . $studentDetails['name'] . "</strong></h2>";

            // TODO: Add attendance, marks and backlogs report

            echo "<div class='comment-qna-toggle'>
                    <ul>
                      <li class='active'>View Comments</li>
                      <li>View QnA</li>
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
        <h2>Apply for Leave</h2>
        <form action="./apply_leave.php" method="post">

          <label for="fromDate">From Date:</label>
          <input type="date" id="fromDate" name="fromDate" required onchange="updateDate()">

          <label for="toDate">To Date:</label>
          <input type="date" id="toDate" name="toDate" required oninput="calculateTotalDays()">

          <label for="totalDays">Total Days:</label>
          <input type="text" id="totalDays" name="totalDays" readonly>

          <label for="reason">Reason for Leave:</label>
          <textarea id="reason" name="reason" rows="4" required></textarea>

          <label for="adjustedWith">Adjusted With: </label>
          <textarea id="adjustedWith" name="adjustedWith" rows="2" required></textarea>
          <p class="note"><strong>Note: </strong>Follow this format while entering 'Adjusted with' column.<br>
            (Adjusted Faculty Name - Subject - Class hour).<br>Seperate with comma for Multiple Adjustments</p>

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
            <th>Substitute</th>
            <th>Status</th>
          </tr>
          <?php
          $prevRecords = $Leave->getPreviousRecords($facultyId);
          if (!empty($prevRecords)) {
            foreach ($prevRecords as $record) {
              echo '<tr> <td>' . $record['applied_on'] . '</td>';
              echo '<td>' . $record['applied_from'] . '</td>';
              echo '<td>' . $record['applied_to'] . '</td>';
              echo '<td>' . $record['total_days'] . '</td>';
              echo '<td>' . $record['reason'] . '</td>';
              echo '<td style="width:20rem">' . $record['adjusted_with'] . '</td>';
              echo '<td>' . $record['status'] . '</td> </tr>';
            }
          } else {
            echo '<tr><td colspan="7" style="text-align:center;">No Records Found</td></tr>';
          }
          ?>
        </table>
      </div>
    </section>

    <section id="logout">
      <h2>Are you sure want to Logout?</h2>
      <a href='../login/logout.php?logout=true' class='logout'>Logout</a>
    </section>
  </main>
</body>

<script src="./script.js"></script>

</html>