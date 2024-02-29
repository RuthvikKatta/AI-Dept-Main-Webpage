<?php

class Marks
{
    private $dbh;
    private $marksTable = 'marks';
    private $studentTable = 'student';
    private $subjectTable = 'subject';
    private $backlogsTable = 'backlog_record';
    public function __construct()
    {
        $database = 'college_website_test_db';
        $host = 'localhost';
        $databaseUsername = 'root';
        $databaseUserPassword = '';
        try {

            $this->dbh =
                new PDO(
                    sprintf('mysql:host=%s;dbname=%s', $host, $database),
                    $databaseUsername,
                    $databaseUserPassword
                );

        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function addMarkRecords(array $studentIds, $section, $year, $subject_id, $marks_type, $exam_session, $total_marks, array $marks_obtained)
    {
        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->marksTable . ' (student_id, section, year, subject_id, marks_type, exam_session, total_marks, marks_obtained) 
        VALUES (:student_id, :section, :year, :subject_id, :marks_type, :exam_session, :total_marks, :marks_obtained)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        foreach ($studentIds as $index => $student_id) {
            if (
                false === $statement->execute([
                    ':student_id' => $student_id,
                    ':section' => $section,
                    ':year' => $year,
                    ':subject_id' => $subject_id,
                    ':marks_type' => $marks_type,
                    ':exam_session' => $exam_session,
                    ':total_marks' => $total_marks,
                    ':marks_obtained' => $marks_obtained[$index],
                ])
            ) {
                throw new Exception(implode(' ', $statement->errorInfo()));
            }
        }
    }
    public function getMarks($section, $year, $subject_id, $marks_type, $exam_session)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->marksTable . '
            LEFT JOIN ' . $this->studentTable . ' ON ' . $this->marksTable . '.student_id = ' . $this->studentTable . '.student_id
            WHERE ' . $this->marksTable . '.section = :section
            AND ' . $this->marksTable . '.year = :year
            AND ' . $this->marksTable . '.subject_id = :subject_id
            AND ' . $this->marksTable . '.marks_type = :marks_type
            AND ' . $this->marksTable . '.exam_session = :exam_session'
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':section' => $section,
            ':year' => $year,
            ':subject_id' => $subject_id,
            ':marks_type' => $marks_type,
            ':exam_session' => $exam_session,
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function updateMarks(array $recordIdsAndMarks)
    {
        $statement = $this->dbh->prepare(
            'UPDATE ' . $this->marksTable . ' 
        SET marks_obtained = :updatedMarks 
        WHERE record_id = :record_id'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        foreach ($recordIdsAndMarks as $recordId => $updatedMarks) {
            $statement->bindParam(':updatedMarks', $updatedMarks, PDO::PARAM_INT);
            $statement->bindParam(':record_id', $recordId, PDO::PARAM_INT);

            if (!$statement->execute()) {
                throw new Exception('Failed to execute update statement');
            }
        }
    }
    public function getOverallMarks($year, $section, $subjectId)
    {
        $statement = $this->dbh->prepare(
            "SELECT 
            student_id, 
            MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'I' THEN marks_obtained END) AS `Mid I`,
            MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'I' THEN marks_obtained END) AS `Assignment I`,
            MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'II' THEN marks_obtained END) AS `Mid II`,
            MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'II' THEN marks_obtained END) AS `Assignment II`,
            MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'III' THEN marks_obtained END) AS `Mid III`,
            MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'III' THEN marks_obtained END) AS `Assignment III`
            FROM (
                SELECT * FROM `marks` WHERE year = :year AND section = :section AND subject_id = :subject_id
            ) sub_query GROUP BY student_id"
        );


        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':year' => $year,
            ':section' => $section,
            ':subject_id' => $subjectId,
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getOverallMarksOfStudents($year, $section, $studentId, $subjectIds)
    {
        $subjectIdPlaceholders = implode(',', array_fill(0, count($subjectIds), '?'));

        $query = "SELECT 
                    subject_id, 
                    MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'I' THEN marks_obtained END) AS `Mid I`,
                    MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'I' THEN marks_obtained END) AS `Assignment I`,
                    MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'II' THEN marks_obtained END) AS `Mid II`,
                    MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'II' THEN marks_obtained END) AS `Assignment II`,
                    MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'III' THEN marks_obtained END) AS `Mid III`,
                    MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'III' THEN marks_obtained END) AS `Assignment III`
                FROM marks
                WHERE year = ? AND section = ? AND student_id = ? AND subject_id IN ($subjectIdPlaceholders)
                GROUP BY subject_id";

        $statement = $this->dbh->prepare($query);

        if (false === $statement) {
            return [];
        }

        $bindValues = array_merge([$year, $section, $studentId], $subjectIds);

        $result = $statement->execute($bindValues);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function getBacklogsByStudentId($student_id)
    {
        $statement = $this->dbh->prepare(
            "SELECT sb.subject_id, sb.subject_name FROM " . $this->backlogsTable . " bt
            LEFT JOIN " . $this->subjectTable . " sb ON bt.subject_id = sb.subject_id WHERE bt.student_id = :student_id"
        );

        if (false === $statement) {
            return [];
        }

        $results = $statement->execute([
            ':student_id' => $student_id
        ]);

        if (false === $results) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
}