<?php

class Faculty
{
    private $dbh;
    private $staffTableName = 'staff';
    private $designationTableName = 'designation';
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

    public function getFacultyIds()
    {
        $statement = $this->dbh->prepare(
            "SELECT staff_id FROM " . $this->staffTableName . " WHERE role = 'Teaching'"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute();

        if (false === $result) {
            return [];
        }

        $facultyResult = $statement->fetchAll(PDO::FETCH_ASSOC);

        $faculty = array();

        foreach ($facultyResult as $record) {
            $faculty[] = $record['staff_id'];
        }

        return $faculty;
    }

    public function getFacultyDetails($facultyId)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->staffTableName . " WHERE staff_id = :staff_id"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':staff_id' => $facultyId
        ]);

        if (false === $result) {
            return [];
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function getDesignation($designationId)
    {
        $statement = $this->dbh->prepare(
            "SELECT title FROM " . $this->designationTableName . " WHERE designation_id = :designation_id"
        );

        if(false === $statement){
            return null;
        }

        $result = $statement->execute([
            ':designation_id' => $designationId
        ]);

        if(false === $result){
            return null;
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
}