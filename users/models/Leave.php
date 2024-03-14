<?php

class Leave
{
    private $dbh;
    private $leaveRecordTable = 'leave_record';
    private $leaveAdjustmentTable = 'leave_adjustment';
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
    public function addRecord($appliedby, $appliedfrom, $appliedto, $totaldays, $reason)
    {
        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->leaveRecordTable . ' (applied_by, applied_from, applied_to, total_days, reason) 
                    VALUES (:applied_by, :applied_from, :applied_to, :total_days, :reason)'
        );

        if (false === $statement) {
            return false;
        }

        try {
            $statement->execute([
                ':applied_by' => $appliedby,
                ':applied_from' => $appliedfrom,
                ':applied_to' => $appliedto,
                ':total_days' => $totaldays,
                ':reason' => $reason,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->dbh->lastInsertId();
    }
    public function getPreviousRecords($facultyId)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->leaveRecordTable . ' WHERE applied_by = :faculty_id'
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([':faculty_id' => $facultyId]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function updateLeaveStatus($leaveId, $status)
    {
        $statement = $this->dbh->prepare(
            "UPDATE " . $this->leaveRecordTable . "
            SET status = :status
            WHERE leave_id = :leave_id"
        );

        if (false === $statement) {
            return "Internal Error";
        }

        $result = $statement->execute([
            ":status" => $status,
            ":leave_id" => $leaveId,
        ]);

        if ($result === false) {
            return "Leave Rejected";
        }
        return "Leave Accepted";
    }
    public function getAppliedLeaves($status)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->leaveRecordTable . " WHERE status = :status"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':status' => $status
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getAppliedLeavesOfFaculty($appliedBy, $status)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->leaveRecordTable . " WHERE applied_by = :applied_by AND status = :status"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':applied_by' => $appliedBy,
            ':status' => $status
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function addLeaveAdjustment($leaveId, $adjustmentData)
    {
        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->leaveAdjustmentTable . ' (leave_id, date, year, section, subject_id, hour, adjusted_with) 
                    VALUES (:leave_id, :date, :year, :section, :subject_id, :hour, :adjusted_with)'
        );

        if (false === $statement) {
            return false;
        }

        try {
            $statement->execute([
                ':leave_id' => $leaveId,
                ':date' => $adjustmentData['date'],
                ':year' => $adjustmentData['year'],
                ':section' => $adjustmentData['section'],
                ':subject_id' => $adjustmentData['subject'],
                ':hour' => $adjustmentData['hour'],
                ':adjusted_with' => $adjustmentData['faculty'],
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function getPreviousAdjustments($leaveId)
    {
        $statement = $this->dbh->prepare(
            'SELECT adjusted_with, date, name, year, section, hour  FROM ' . $this->leaveAdjustmentTable . ' 
            LEFT JOIN subject using (subject_id) 
            WHERE leave_id = :leave_id'
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([':leave_id' => $leaveId]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getAdjustmentsByFaculty($facultyId)
    {
        $today = date('Y-m-d');
        $weekFromToday = date('Y-m-d', strtotime('+1 week'));

        $statement = $this->dbh->prepare(
            "SELECT *  FROM " . $this->leaveAdjustmentTable . " 
        LEFT JOIN subject using (subject_id) 
        LEFT JOIN " . $this->leaveRecordTable . " using (leave_id)
        WHERE adjusted_with = :adjusted_with 
        AND status = 'Approved'
        AND date >= :today 
        AND date < :week_from_today"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':adjusted_with' => $facultyId,
            ':today' => $today,
            ':week_from_today' => $weekFromToday
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

}