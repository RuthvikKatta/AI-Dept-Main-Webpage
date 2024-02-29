<?php
class Student
{
    private $dbh;
    private $studentTableName = 'student';

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

    public function getStudentDetails($student_id)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->studentTableName . ' WHERE student_id = :student_id'
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
            'SELECT student_id, name FROM ' . $this->studentTableName . ' WHERE year = :year and section = :section'
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
}