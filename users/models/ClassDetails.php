<?php

class ClassDetails
{
    private $dbh;
    private $classTable = 'class';
    private $classHasSubjectsTable = 'class_has_subjects';
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
    public function getClassDetails(string $classId)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->classTable . " WHERE class_id=:class_id"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":class_id" => $classId
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
    public function getClassId($year, $section){
        $statement = $this->dbh->prepare(
            "SELECT class_id FROM " . $this->classTable ." WHERE year=:year and section=:section" 
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

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    public function updateClassDetails($classId, $currentSemester, $lunchHour)
    {
        $statement = $this->dbh->prepare(
            "UPDATE " . $this->classTable . "
            SET current_semester = :current_semester, 
            lunch_hour = :lunch_hour 
            WHERE class_id = :class_id"
        );

        if (false === $statement) {
            return false;
        }

        try {
            $statement->execute([
                'current_semester' => $currentSemester,
                'lunch_hour' => $lunchHour,
                'class_id' => $classId,
            ]);
            return true;
        } catch (PDOException $e) {
            return "Internal Error";
        }
    }
    public function getSubjectDetailsByClassId($classId)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->classHasSubjectsTable . " WHERE class_id = :class_id"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([":class_id" => $classId]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function addSubjectsToClass($classId, $subjectDetails)
    {

        $message = '';
        foreach ($subjectDetails as $subjectDetail) {
            $addQuery = 'INSERT INTO ' . $this->classHasSubjectsTable . ' (class_id, subject_id, taught_by) VALUES (:class_id, :subject_id, :taught_by)';
            $addStatement = $this->dbh->prepare($addQuery);

            if (false === $addStatement) {
                continue;
            }

            try {
                $addStatement->execute([
                    ":class_id" => $classId,
                    ":subject_id" => $subjectDetail['subject_id'],
                    ":taught_by" => $subjectDetail['taught_by'],
                ]);
            } catch (PDOException $e) {
                if ($e->getCode() == '23000') {
                    $message .= "Subject $subjectDetail[subject_id] already exists for Class $classId";
                }
            }

        }

        if (empty($message)) {
            return 'Added successfully';
        } else {
            return $message;
        }
    }
    public function removeSubjectsOfClass($classId, $subjectId, $taughtBy)
    {
        $deleteStatement = $this->dbh->prepare(
            'DELETE FROM ' . $this->classHasSubjectsTable . ' WHERE class_id = :class_id AND subject_id = :subject_id AND taught_by = :taught_by'
        );

        if (false === $deleteStatement) {
            return false;
        }

        try {
            false === $deleteStatement->execute([
                ':class_id' => $classId,
                ':subject_id' => $subjectId,
                ':taught_by' => $taughtBy,
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getLunchHour($year, $section){
        $statement = $this->dbh->prepare(
            "SELECT lunch_hour FROM " . $this->classTable . " WHERE year = :year AND section=:section"
        );

        if (false === $statement) {
            return "";
        }

        $result = $statement->execute([
            ":year" => $year,
            ":section" => $section
        ]);

        if (false === $result) {
            return "";
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row['lunch_hour'];
    }
}