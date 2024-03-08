<?php

class Subject
{
    private $dbh;
    private $subjectTable = 'Subject';
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
            "SELECT * FROM " . $this->classTable .
            " LEFT JOIN " . $this->classHasSubjectsTable . " USING (class_id)" .
            " LEFT JOIN " . $this->subjectTable . " USING (subject_id)" .
            " WHERE taught_by = :taught_by"
        );

        if (false === $statement) {
            return ['subjects' => [], 'years' => []];
        }

        $result = $statement->execute([
            ":taught_by" => $facultyId
        ]);

        if (false === $result) {
            return ['subjects' => [], 'years' => []];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $subjects = [];

        foreach ($rows as $row) {
            $subjectId = $row['subject_id'];

            if (!isset($subjects[$subjectId])) {
                $subjects[$subjectId] = [
                    'subject_id' => $subjectId,
                    'name' => $row['name']
                ];
            }
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
    public function getSubjectDetails($subjectId)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->subjectTable . " WHERE subject_id = :subject_id"
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
    public function updateSubjectDetails($subjectId, $updatedDetails)
    {
        $statement = $this->dbh->prepare(
            "UPDATE " . $this->subjectTable . "
            SET name=:name,
            type=:type,
            credits=:credits
            WHERE subject_id = :subject_id"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            ":name" => $updatedDetails["name"],
            ":type" => $updatedDetails["type"],
            ":credits" => $updatedDetails["credits"],
            ':subject_id' => $subjectId,
        ]);

        if ($result === false) {
            return false;
        }
        return true;
    }
    public function addSubject($newSubjectDetails)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO " . $this->subjectTable . "
            (subject_id, name, type, credits)
            VALUES (:subject_id, :name, :type, :credits)"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        try {
            $statement->execute([
                ":subject_id" => $newSubjectDetails["subject_id"],
                ":name" => $newSubjectDetails["name"],
                ":type" => $newSubjectDetails["type"],
                ":credits" => $newSubjectDetails["credits"],
            ]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return "Subject already exists";
            }
            return "Internal Error";
        }
    }
}