<?php

class ClassDetails
{
    private $dbh;
    private $classTable = 'class';
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
    public function getCurrentSemester(string $year, string $section)
    {
        $statement = $this->dbh->prepare(
            "SELECT current_semester FROM " . $this->classTable . " WHERE year = :year AND section = :section"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":year" => $year,
            ":section" => $section
        ]);

        if (false === $result) {
            return [];
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row['current_semester'];
    }
    public function getClassDetails(string $year, string $section)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->classTable . " WHERE year=:year AND section=:section"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":year" => $year,
            ":section" => $section
        ]);

        if (false === $result) {
            return [];
        }

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllClasses()
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->classTable
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute();

        if (false === $result) {
            return [];
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateClassDetails($classId, $updatedDetails)
    {
        $statement = $this->dbh->prepare(
            "UPDATE TABLE " . $this->classTable . "
            SET year=:year,
            section=:section,
            class_incharge_id=:class_incharge_id,
            current_semster=:current_semester 
            WHERE class_id = :class_id"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            'year' => $updatedDetails["year"],
            'section' => $updatedDetails["section"],
            'class_incharge_id' => $updatedDetails["class_incharge_id"],
            'current_semester' => $updatedDetails["current_semester"],
            'class_id' => $classId,
        ]);

        if ($result === false) {
            throw new Exception('Error executing the update statement: ');
        }
    }
}