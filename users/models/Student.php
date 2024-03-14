<?php
class Student
{
    private $dbh;
    private $studentTable = 'student';
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
    public function getStudentDetails($student_id)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->studentTable . ' WHERE student_id = :student_id'
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([':student_id' => $student_id]);

        if (false === $result) {
            return [];
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
    public function getAllStudentofYearAndSection($year, $section = 'A')
    {
        $statement = $this->dbh->prepare(
            'SELECT student_id, first_name, middle_name, last_name FROM ' . $this->studentTable . ' WHERE year = :year and section = :section'
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ':year' => $year,
            ':section' => $section
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function updateStudentDetails($studentId, $updatedDetails)
    {
        $statement = $this->dbh->prepare(
            "UPDATE " . $this->studentTable . "
            SET first_name=:first_name,
            middle_name=:middle_name,
            last_name=:last_name,
            salutation=:salutation,
            gender=:gender,
            date_of_birth=:date_of_birth,
            email=:email,
            year=:year,
            section=:section
            WHERE student_id = :student_id"
        );

        if (false === $statement) {
            return false;
        }

        $result = $statement->execute([
            ":first_name" => $updatedDetails['first_name'],
            ":middle_name" => $updatedDetails['middle_name'],
            ":last_name" => $updatedDetails['last_name'],
            ":salutation" => $updatedDetails['salutation'],
            ":gender" => $updatedDetails['gender'],
            ":date_of_birth" => $updatedDetails['date_of_birth'],
            ":email" => $updatedDetails['email'],
            ":year" => $updatedDetails['year'],
            ":section" => $updatedDetails['section'],
            ':student_id' => $studentId,
        ]);

        if ($result === false) {
            return false;
        }
        return true;
    }
    public function deleteStudent(string $studentId)
    {
        $statement = $this->dbh->prepare(
            "DELETE FROM " . $this->studentTable . " WHERE student_id = :student_id"
        );

        if (false === $statement) {
            return [];
        }

        if (
            false === $statement->execute([
                ':student_id' => $studentId,
            ])
        ) {
            return false;
        }
    }
    public function addStudent($newStudentDetails)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO " . $this->studentTable . "
        (student_id, first_name, middle_name, last_name, salutation, gender, date_of_birth, email, year, section)
        VALUES (:student_id, :first_name, :middle_name, :last_name, :salutation, :gender, :date_of_birth, :email, :year, :section)"
        );

        if (false === $statement) {
            return false;
        }

        try {
            $statement->execute([
                ':student_id' => $newStudentDetails['student_id'],
                ':first_name' => $newStudentDetails['first_name'],
                ':middle_name' => $newStudentDetails['middle_name'],
                ':last_name' => $newStudentDetails['last_name'],
                ':salutation' => $newStudentDetails['salutation'],
                ':gender' => $newStudentDetails['gender'],
                ':date_of_birth' => $newStudentDetails['date_of_birth'],
                ':email' => $newStudentDetails['email'],
                ':year' => $newStudentDetails['year'],
                ':section' => $newStudentDetails['section'],
            ]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return "Student already exists";
            }
            return "Internal Error Occured";
        }
    }
}