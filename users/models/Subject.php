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
            "SELECT  subject_name FROM " . $this->subjectTable . " WHERE subject_id = :subject_id"
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
            "SELECT DISTINCT subject_id, subject_name, subject_year FROM " . $this->staffTeachingStudentTable .
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
                'subject_name' => $row['subject_name']
            ];
        }

        $years = array_column($rows, 'subject_year');

        return [
            'subjects' => $subjects,
            'years' => array_unique($years)
        ];
    }
    public function getSubjects($year, $semester, $section)
    {
        $statement = $this->dbh->prepare(
            "SELECT st.subject_id, st.subject_name FROM " . $this->classHasSubjectsTable . " 
            chs LEFT JOIN " . $this->classTable . " c on chs.class_id = c.class_id 
            LEFT JOIN ". $this->subjectTable ." st ON chs.subject_id = st.subject_id
            WHERE c.year = :year AND c.semester = :semester AND c.section = :section"
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
}