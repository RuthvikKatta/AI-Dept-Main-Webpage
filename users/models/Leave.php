<?php

class Leave
{
    private $dbh;
    private $LeaveRecordTable = 'leave_record';
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
    public function addRecord($appliedby, $appliedfrom, $appliedto, $totaldays, $reason, $adjustedWith)
    {

        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->LeaveRecordTable . ' (applied_by, applied_from, applied_to, total_days, reason, adjusted_with) 
                    VALUES (:applied_by, :applied_from, :applied_to, :total_days, :reason, :adjusted_with)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        if (
            false === $statement->execute([
                ':applied_by' => $appliedby,
                ':applied_from' => $appliedfrom,
                ':applied_to' => $appliedto,
                ':total_days' => $totaldays,
                ':reason' => $reason,
                ':adjusted_with' => $adjustedWith,
            ])
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }
    }
    public function getPreviousRecords($facultyId)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->LeaveRecordTable . ' WHERE applied_by = :faculty_id'
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
            "UPDATE " . $this->LeaveRecordTable . "
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
    public function getAppliedLeaves()
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->LeaveRecordTable . " WHERE status = 'Pending'"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute();

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
}