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
        $config = include 'Config.php';

        $host = $config['database']['host'];
        $database = $config['database']['database_name'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];

        try {
            $this->dbh = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public function addMarkRecords(array $studentIds, string $section, string $year, string $subject_id, string $marks_type, string $exam_session, string $total_marks, array $marks_obtained)
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
    public function getMarks(string $section, string $year, string $subject_id, string $marks_type, string $exam_session)
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
    public function getOverallMarks(string $year, string $section, string $subjectId)
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
    public function getOverallMarksOfStudents(string $year, string $section, string $studentId, array $subjectIds)
    {
        $subjectIdPlaceholders = implode(',', array_fill(0, count($subjectIds), '?'));

        $statement = $this->dbh->prepare(
            "SELECT 
                subq.subject_id,
                MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'I' THEN marks_obtained END) AS 'Mid I',
                MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'I' THEN marks_obtained END) AS 'Assignment I',
                MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'II' THEN marks_obtained END) AS 'Mid II',
                MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'II' THEN marks_obtained END) AS 'Assignment II',
                MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'III' THEN marks_obtained END) AS 'Mid III',
                MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'III' THEN marks_obtained END) AS 'Assignment III'
            FROM
                (SELECT subject_id, name FROM subject WHERE subject_id IN ($subjectIdPlaceholders)) subq
            JOIN
                (SELECT * FROM student WHERE student_id = ?) sq ON 1 = 1 
            LEFT JOIN
                marks
            ON
                marks.student_id = sq.student_id AND marks.subject_id = subq.subject_id AND marks.year = ? AND marks.section = ?
            GROUP BY
                subq.subject_id"
        );

        if (false === $statement) {
            return [];
        }

        foreach ($subjectIds as $key => $subjectId) {
            $statement->bindValue($key + 1, $subjectId, PDO::PARAM_INT);
        }

        $statement->bindValue($key + 2, $studentId, PDO::PARAM_STR);
        $statement->bindValue($key + 3, $year, PDO::PARAM_STR);
        $statement->bindValue($key + 4, $section, PDO::PARAM_STR);

        $result = $statement->execute();

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getBacklogsByStudentId(string $studentId, string $status)
    {
        $statement = $this->dbh->prepare(
            "SELECT sb.subject_id, sb.name, bt.year, bt.semester FROM " . $this->backlogsTable . " bt
            LEFT JOIN " . $this->subjectTable . " sb ON bt.subject_id = sb.subject_id WHERE bt.student_id = :student_id AND bt.status = :status"
        );

        if (false === $statement) {
            return [];
        }

        $results = $statement->execute([
            ':student_id' => $studentId,
            ':status' => $status,
        ]);

        if (false === $results) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
}