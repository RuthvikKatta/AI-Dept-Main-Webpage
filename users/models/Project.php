<?php
class Project
{
    private $dbh;
    private $projectTableName = 'project';

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
            "SELECT academic_year, project_domain FROM " . $this->projectTableName
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
            'domains' => array_unique(array_column($rows, 'project_domain'))
        ];
    }
    public function getProjects($academicYear, $domain, $type)
    {
        $statement = $this->dbh->prepare(
            "SELECT project_id, academic_year, project_title, project_domain, project_type
            FROM " . $this->projectTableName . "
            WHERE academic_year LIKE CONCAT('%', :academic_year, '%')
            AND project_domain LIKE CONCAT('%', :project_domain, '%')
            AND project_type LIKE CONCAT('%', :project_type, '%')"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":academic_year" => $academicYear,
            ":project_domain" => $domain,
            ":project_type" => $type,
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
            "SELECT * FROM " . $this->projectTableName . " WHERE project_id = :project_id"
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
}