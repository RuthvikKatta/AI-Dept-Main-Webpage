<?php

class Staff
{
    private $dbh;
    private $staffTable = 'staff';
    private $designationTable = 'designation';
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
    public function getAllStaff()
    {
        $statement = $this->dbh->prepare(
            "SELECT staff_id, first_name, middle_name, last_name FROM " . $this->staffTable
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute();

        if (false === $result) {
            return [];
        }

        $facultyResult = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $facultyResult;
    }
    public function getStaffByRole($role)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->staffTable ." WHERE role = :role"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":role" => $role
        ]);

        if (false === $result) {
            return [];
        }

        $facultyResult = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $facultyResult;
    }
    public function getStaffDetails($staffId)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->staffTable . " WHERE staff_id = :staff_id"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':staff_id' => $staffId
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
            "SELECT title FROM " . $this->designationTable . " WHERE designation_id = :designation_id"
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
            "INSERT INTO " . $this->staffTable . "
        (staff_id, first_name, middle_name, last_name, salutation, qualification, role, designation_id, experience_years, gender, age, mobile_number, email)
        VALUES
        (:staff_id, :first_name, :middle_name, :last_name, :salutation, :qualification, :role, :designation_id, :experience_years, :gender, :age, :mobile_number, :email)"
        );

        if (false === $statement) {
            return false;
        }

        try {
            $statement->execute([
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
                ':email' => $staffDetails['email'],
            ]);
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return "Staff already exists";
            }
            return "Internal Error Occured";
        }
    }
    public function updateStaffDetails($staffId, $updatedDetails)
    {
        $statement = $this->dbh->prepare(
            "UPDATE " . $this->staffTable . "
            SET first_name=:first_name,
            middle_name=:middle_name,
            last_name=:last_name,
            salutation=:salutation,
            qualification=:qualification,
            role=:role,
            designation_id=:designation_id,
            experience_years=:experience_years,
            gender=:gender,
            age=:age,
            mobile_number=:mobile_number,
            alternative_mobile_number=:alternative_mobile_number,
            email=:email
            WHERE staff_id = :staff_id"
        );

        if (false === $statement) {
            return false;
        }

        $result = $statement->execute([
            ":first_name" => $updatedDetails["first_name"],
            ":middle_name" => $updatedDetails["middle_name"],
            ":last_name" => $updatedDetails["last_name"],
            ":salutation" => $updatedDetails["salutation"],
            ":qualification" => $updatedDetails["qualification"],
            ":role" => $updatedDetails["role"],
            ":designation_id" => $updatedDetails["designation_id"],
            ":experience_years" => $updatedDetails["experience_years"],
            ":gender" => $updatedDetails["gender"],
            ":age" => $updatedDetails["age"],
            ":mobile_number" => $updatedDetails["mobile_number"],
            ":alternative_mobile_number" => $updatedDetails["alternative_mobile_number"],
            ":email" => $updatedDetails["email"],
            ':staff_id' => $staffId,
        ]);

        if ($result === false) {
            return false;
        }
        return true;
    }
    public function getAllDesignations()
    {
        $statement = $this->dbh->prepare(
            "SELECT designation_id, title FROM " . $this->designationTable
        );

        if (false === $statement) {
            return null;
        }

        $result = $statement->execute();

        if (false === $result) {
            return null;
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
}