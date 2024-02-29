<?php

class Leave
{
    private $dbh;
    private $usersTableName = 'leave_record';
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

    public function addRecord($appliedby, $appliedfrom, $appliedto, $totaldays, $reason, $adjustedWith)
    {

        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->usersTableName . ' (applied_by, applied_from, applied_to, total_days, reason, adjusted_withd_by) 
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
            'SELECT * FROM ' . $this->usersTableName . ' WHERE applied_by = :faculty_id'
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
}