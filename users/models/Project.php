<?php
class Project
{
    private $dbh;
    private $projectTable = 'project';

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
    public function getDistinctOptions()
    {
        $statement = $this->dbh->prepare(
            "SELECT academic_year, domain FROM " . $this->projectTable
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute();

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return [
            'years' => array_unique(array_column($rows, 'academic_year')),
            'domains' => array_unique(array_column($rows, 'domain'))
        ];
    }
    public function getProjects($academicYear, $domain, $type)
    {
        $statement = $this->dbh->prepare(
            "SELECT project_id, academic_year, title, domain, type
            FROM " . $this->projectTable . "
            WHERE academic_year LIKE CONCAT('%', :academic_year, '%')
            AND domain LIKE CONCAT('%', :domain, '%')
            AND type LIKE CONCAT('%', :type, '%')"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":academic_year" => $academicYear,
            ":domain" => $domain,
            ":type" => $type,
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getProjectById($projectId)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->projectTable . " WHERE project_id = :project_id"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            'project_id' => $projectId
        ]);

        if (false === $result) {
            return [];
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
    public function addProject($projectData)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO " . $this->projectTable . " (academic_year, title, domain, type, student_names, team_number, mentor_name) VALUES 
                (:academic_year, :title, :domain, :type, :student_names, :team_number, :mentor_name)"
        );

        if (false === $statement) {
            return false;
        }

        if (
            false === $statement->execute([
                ":academic_year" => $projectData['academic_year'],
                ":title" => $projectData['title'],
                ":domain" => $projectData['domain'],
                ":type" => $projectData['type'],
                ":student_names" => $projectData['student_names'],
                ":team_number" => $projectData['team_number'],
                ":mentor_name" => $projectData['mentor_name'],
            ])
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }

        return $this->dbh->lastInsertId();
    }
    public function editProjectDetails($projectId, $updatedDetails)
    {
        $statement = $this->dbh->prepare(
            "UPDATE TABLE " . $this->projectTable . "
            SET academic_year=:academic_year,
            title=:title,
            domain=:domain,
            type=:type,
            student_names=:student_names,
            mentor_name=:mentor_name
            WHERE project_id = :project_id"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            ":academic_year" => $updatedDetails['academic_year'],
            ":title" => $updatedDetails['title'],
            ":domain" => $updatedDetails['domain'],
            ":type" => $updatedDetails['type'],
            ":student_names" => $updatedDetails['student_names'],
            ":mentor_nam" => $updatedDetails['mentor_name'],
            ':project_id' => $projectId,
        ]);

        if ($result === false) {
            throw new Exception('Error executing the update statement: ');
        }
    }
    public function deleteProjectByProjectId($projectId)
    {
        $statement = $this->dbh->prepare(
            "DELETE FROM " . $this->projectTable . " WHERE project_id = :project_id"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([':project_id' => $projectId]);

        if (false === $result) {
            throw new Exception('Project deletion failed');
        }

        return true;
    }
}