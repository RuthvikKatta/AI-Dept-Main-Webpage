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
}