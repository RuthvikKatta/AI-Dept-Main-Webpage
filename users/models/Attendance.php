<?php

class Attendance
{

    private $dbh;
    private $attendanceLogTable = 'attendance_log';
    private $absenteesLogTable = 'absentees_log';
    private $subjectTable = 'subject';
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
    public function lastAbsentDate($student_id)
    {
        $statement = $this->dbh->prepare(
            "SELECT absent_on FROM " . $this->absenteesLogTable . "order by absent_on desc limit 1"
        );

        if (false === $statement) {
            return null;
        }

        $result = $statement->execute();

        if (false === $result) {
            return null;
        }

        $absentRecord = $statement->fetch(PDO::FETCH_ASSOC);

        return $absentRecord;
    }
    public function addAttendanceLog($attendance_taken_by, $class_year, $class_section, $subject_id)
    {
        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->attendanceLogTable . ' (attendance_taken_by, class_year, class_section, subject_id) 
                    VALUES (:attendance_taken_by, :class_year, :class_section, :subject_id)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        if (
            false === $statement->execute([
                ':attendance_taken_by' => $attendance_taken_by,
                ':class_year' => $class_year,
                ':class_section' => $class_section,
                ':subject_id' => $subject_id,
            ])
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }

        return $this->dbh->lastInsertId();
    }
    public function getAttendanceLogsByFacultyId($attendance_taken_by)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->attendanceLogTable . ' WHERE (attendance_taken_by) = (:attendance_taken_by)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            ':attendance_taken_by' => $attendance_taken_by
        ]);

        if (false === $result) {
            return null;
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getAttendanceLogsByLogNo($log_no)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->attendanceLogTable . ' WHERE (log_no) = (:log_no)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            ':log_no' => $log_no
        ]);

        if (false === $result) {
            return null;
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
    public function getAbsentLogs($attendance_log_no)
    {
        $statement = $this->dbh->prepare(
            'SELECT student_id FROM ' . $this->absenteesLogTable . ' WHERE (attendance_log_no) = (:attendance_log_no)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            ':attendance_log_no' => $attendance_log_no
        ]);

        if (false === $result) {
            return null;
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function addAbsentLogs(array $studentIds, $attendance_log_no, $subject_id, $attendance_taken_by)
    {
        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->absenteesLogTable . ' (student_id, attendance_log_no, subject_id, attendance_taken_by) 
        VALUES (:student_id, :attendance_log_no, :subject_id, :attendance_taken_by)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        foreach ($studentIds as $student_id) {
            if (
                false === $statement->execute([
                    ':student_id' => $student_id,
                    ':attendance_log_no' => $attendance_log_no,
                    ':subject_id' => $subject_id,
                    ':attendance_taken_by' => $attendance_taken_by,
                ])
            ) {
                throw new Exception(implode(' ', $statement->errorInfo()));
            }
        }
    }
    public function removeAbsentLogs(array $studentIds, $attendance_log_no)
    {
        $statement = $this->dbh->prepare(
            'DELETE FROM ' . $this->absenteesLogTable . ' WHERE student_id = :student_id AND attendance_log_no = :attendance_log_no'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        foreach ($studentIds as $student_id) {
            if (
                false === $statement->execute([
                    ':student_id' => $student_id,
                    ':attendance_log_no' => $attendance_log_no,
                ])
            ) {
                throw new Exception(implode(' ', $statement->errorInfo()));
            }
        }
    }
    public function getTotalClasses(array $subjectIds): array
    {
        $placeholders = implode(', ', array_fill(0, count($subjectIds), '?'));
        $statement = $this->dbh->prepare(
            "Select subq.subject_id, subq.name, COALESCE(atq.total_classes, 0) as total_classes from
            (select subject_id, name from " . $this->subjectTable . " Where subject_id in ($placeholders)) subq
            LEFT JOIN (SELECT subject_id, COUNT(*) AS total_classes FROM " . $this->attendanceLogTable . " GROUP BY subject_id) atq ON subq.subject_id = atq.subject_id;"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute($subjectIds);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getCummulativeAttendance(array $subjectIds, $year, $section)
    {
        $subjectIdPlaceholders = implode(',', array_fill(0, count($subjectIds), '?'));

        $statement = $this->dbh->prepare("
        SELECT 
            student_id,
            CONCAT(last_name, ' ', first_name, ' ', middle_name) as name,
            " . implode(',', array_map(function ($subjectId) {
            return "SUM(CASE WHEN subject_id = $subjectId THEN present ELSE 0 END) AS '$subjectId'";
        }, $subjectIds)) . "
        FROM
        (SELECT 
            stu.student_id,
            stu.first_name,
            stu.middle_name,
            stu.last_name,
            subject.subject_id,
            COALESCE(ab.absent, 0) AS absent,
            (total.total_classes - COALESCE(ab.absent, 0)) AS present
        FROM
            Student stu 
        CROSS JOIN (SELECT 
            subject_id 
        FROM 
            subject WHERE subject_id IN ($subjectIdPlaceholders)) subject
        LEFT JOIN (SELECT 
            subject_id, COUNT(*) AS total_classes
        FROM
            attendance_log
        GROUP BY subject_id) total ON total.subject_id = subject.subject_id
        LEFT JOIN (SELECT 
            student_id, subject_id, COUNT(*) AS absent
        FROM
            absentees_log
        GROUP BY student_id, subject_id) ab ON stu.student_id = ab.student_id
            AND subject.subject_id = ab.subject_id
        WHERE stu.year = ? AND stu.section = ?) AS main
        GROUP BY student_id
    ");

        if (!$statement) {
            throw new Exception('Invalid query statement');
        }

        foreach ($subjectIds as $key => $subjectId) {
            $statement->bindValue($key + 1, $subjectId, PDO::PARAM_INT);
        }

        if (count($subjectIds) > 0) {
            $statement->bindValue($key + 2, $year, PDO::PARAM_STR);
            $statement->bindValue($key + 3, $section, PDO::PARAM_STR);
        }

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAttendanceByStudentId(array $subjectIds, string $studentId)
    {
        $subjectIdPlaceholders = implode(',', array_fill(0, count($subjectIds), '?'));

        $statement = $this->dbh->prepare("
        SELECT 
            student_id,
            " . implode(',', array_map(function ($subjectId) {
            return "SUM(CASE WHEN subject_id = $subjectId THEN present ELSE 0 END) AS '$subjectId'";
        }, $subjectIds)) . "
        FROM
        (SELECT 
            stu.student_id,
            subject.subject_id,
            COALESCE(ab.absent, 0) AS absent,
            (total.total_classes - COALESCE(ab.absent, 0)) AS present
        FROM
            Student stu 
        CROSS JOIN (SELECT 
            subject_id 
        FROM 
            subject WHERE subject_id IN ($subjectIdPlaceholders)) subject
        LEFT JOIN (SELECT 
            subject_id, COUNT(*) AS total_classes
        FROM
            attendance_log
        GROUP BY subject_id) total ON total.subject_id = subject.subject_id
        LEFT JOIN (SELECT 
            student_id, subject_id, COUNT(*) AS absent
        FROM
            absentees_log
        GROUP BY student_id, subject_id) ab ON stu.student_id = ab.student_id
            AND subject.subject_id = ab.subject_id
        WHERE stu.student_id LIKE ?) AS main
        GROUP BY student_id
    ");

        if (!$statement) {
            throw new Exception('Invalid query statement');
        }

        foreach ($subjectIds as $key => $subjectId) {
            $statement->bindValue($key + 1, $subjectId, PDO::PARAM_INT);
        }

        if (count($subjectIds) > 0) {
            $statement->bindValue($key + 2, $studentId, PDO::PARAM_STR);
        }

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}