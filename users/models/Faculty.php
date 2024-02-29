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

        if (false === $statement) {
            return null;
        }

        $result = $statement->execute([
            ':designation_id' => $designationId
        ]);

        if (false === $result) {
            return null;
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
    public function addStaff($staffDetails)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO " . $this->staffTableName . " (staff_id, first_name, middle_name, last_name, salutation, qualification, role, designation_id, experience_years, gender, age, mobile_number, alternative_mobile_number, email) 
                VALUES (:staff_id, :first_name, :middle_name, :last_name, :salutation, :qualification, :role, :designation_id, :experience_years, :gender, :age, :mobile_number, :alternative_mobile_number, :email)"
        );

        if (false === $statement) {
            return false;
        }

        $result = $statement->execute([
            ':staff_id' => $staffDetails['staff_id'],
            ':first_name' => $staffDetails['first_name'],
            ':middle_name' => $staffDetails['middle_name'],
            ':last_name' => $staffDetails['last_name'],
            ':salutation' => $staffDetails['salutation'],
            ':qualification' => $staffDetails['qualification'],
            ':role' => $staffDetails['role'],
            ':designation_id' => $staffDetails['designation_id'],
            ':experience_years' => $staffDetails['experience_years'],
            ':gender' => $staffDetails['gender'],
            ':age' => $staffDetails['age'],
            ':mobile_number' => $staffDetails['mobile_number'],
            ':alternative_mobile_number' => $staffDetails['alternative_mobile_number'],
            ':email' => $staffDetails['email'],
        ]);

        if (false === $result) {
            return false;
        }

        return true;
    }
}