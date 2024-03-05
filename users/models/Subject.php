<?php

class Subject
{
    private $dbh;
    private $subjectTable = 'Subject';
    private $staffTeachingStudentTable = 'Staff_teaching_subject';
    private $classTable = 'class';
    private $classHasSubjectsTable = 'class_has_subjects';
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
    public function getSubjectName($subjectId)
    {
        $statement = $this->dbh->prepare(
            "SELECT name FROM " . $this->subjectTable . " WHERE subject_id = :subject_id"
        );

        if (false === $statement) {
            return '';
        }

        $result = $statement->execute([":subject_id" => $subjectId]);

        if (false === $result) {
            return '';
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
    public function getTeachingDetails($facultyId)
    {
        $statement = $this->dbh->prepare(
            "SELECT DISTINCT subject_id, name, year FROM " . $this->staffTeachingStudentTable .
            " LEFT JOIN  " . $this->subjectTable . " USING (subject_id) WHERE staff_id = :staff_id"
        );

        if (false === $statement) {
            return ['subjects' => [], 'years' => []];
        }

        $result = $statement->execute([
            ":staff_id" => $facultyId
        ]);

        if (false === $result) {
            return ['subjects' => [], 'years' => []];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $subjects = [];

        foreach ($rows as $row) {
            $subjects[] = [
                'subject_id' => $row['subject_id'],
                'name' => $row['name']
            ];
        }

        $years = array_column($rows, 'year');

        return [
            'subjects' => $subjects,
            'years' => array_unique($years)
        ];
    }
    public function getSubjects($year, $semester, $section)
    {
        $statement = $this->dbh->prepare(
            "SELECT st.subject_id, st.name FROM " . $this->classHasSubjectsTable . " 
            chs LEFT JOIN " . $this->classTable . " c on chs.class_id = c.class_id 
            LEFT JOIN " . $this->subjectTable . " st ON chs.subject_id = st.subject_id
            WHERE c.year = :year AND c.current_semester = :semester AND c.section = :section"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":year" => $year,
            ":semester" => $semester,
            ":section" => $section,
        ]);

        if (false === $result) {
            return [];
        }

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }
    public function getAllSubjects()
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->subjectTable
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
    public function editSubjectDetails($subjectId, $updatedDetails)
    {
        $statement = $this->dbh->prepare(
            "UPDATE TABLE " . $this->subjectTable . "
            SET name=:name,
            year=:year,
            semester=:semester,
            type=:type,
            credits=:credits,
            WHERE subject_id = :subject_id"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            ":name" => $updatedDetails["name"],
            ":year" => $updatedDetails["year"],
            ":semester" => $updatedDetails["semester"],
            ":type" => $updatedDetails["type"],
            ":credits" => $updatedDetails["credits"],
            ':subject_id' => $subjectId,
        ]);

        if ($result === false) {
            throw new Exception('Error executing the update statement: ');
        }
    }
    public function addSubject($newSubjectDetails)
    {
        try {
            $statement = $this->dbh->prepare(
                "INSERT INTO " . $this->subjectTable . "
            (name, year, semester, type, credits)
            VALUES (:name, :year, :semester, :type, :credits)"
            );

            if (false === $statement) {
                throw new Exception('Invalid prepare statement');
            }

            $result = $statement->execute([
                ":name" => $newSubjectDetails["name"],
                ":year" => $newSubjectDetails["year"],
                ":semester" => $newSubjectDetails["semester"],
                ":type" => $newSubjectDetails["type"],
                ":credits" => $newSubjectDetails["credits"],
            ]);

            if ($result === false) {
                throw new Exception('Error executing the insert statement: ' . $statement->errorInfo()[2]);
            }

            echo "Subject added successfully.";
        } catch (Exception $e) {
            // Handle exceptions appropriately (e.g., log, display an error message)
            echo "Error adding subject: " . $e->getMessage();
        }
    }
}