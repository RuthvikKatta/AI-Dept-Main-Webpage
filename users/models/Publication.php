<?php
class Publication
{
    private $dbh;
    private $publicationTable = 'publication';

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
            "SELECT journal_name, domain FROM " . $this->publicationTable
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
            'journal_names' => array_unique(array_column($rows, 'journal_name')),
            'domains' => array_unique(array_column($rows, 'domain'))
        ];
    }
    public function getPublications($domain, $journalName, $roleType)
    {
        $statement = $this->dbh->prepare(
            "SELECT publication_id, title, paper_id, journal_name, domain
                FROM " . $this->publicationTable . "
            WHERE domain LIKE CONCAT('%', :domain, '%')
                AND journal_name LIKE CONCAT('%', :journal_name, '%')
                AND role_type LIKE CONCAT('%', :role_type, '%')"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":domain" => $domain,
            ":journal_name" => $journalName,
            ":role_type" => $roleType,
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getPublicationById($publicationId)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->publicationTable . " WHERE publication_id = :publication_id"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            'publication_id' => $publicationId
        ]);

        if (false === $result) {
            return [];
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
    public function addPublication($publicationData)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO " . $this->publicationTable . " (title, paper_id, journal_name, domain, abstract, authors, roleType) VALUES 
                (:title, :paper_id, :journal_name, :domain, :abstract, :authors, :roleType)"
        );

        if (false === $statement) {
            return false;
        }

        if (
            false === $statement->execute([
                ":title" => $publicationData['title'],
                ":paper_id" => $publicationData['paper_id'],
                ":journal_name" => $publicationData['journal_name'],
                ":domain" => $publicationData['domain'],
                ":abstract" => $publicationData['abstract'],
                ":authors" => $publicationData['authors'],
                ":roleType" => $publicationData['roleType']
            ])
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }
    }
    public function editPoublicationDetails($publicationId, $updatedDetails)
    {
        $statement = $this->dbh->prepare(
            "UPDATE TABLE " . $this->publicationTable . "
            SET title=:title,
            paper_id=:paper_id,
            journal_name=:journal_name,
            domain=:domain,
            abstract=:abstract,
            authors=:authors,
            role_type=:role_type
            WHERE publication_id = :publication_id"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([
            ":title" => $updatedDetails["title"],
            ":paper_id" => $updatedDetails["paper_id"],
            ":journal_name" => $updatedDetails["journal_name"],
            ":domain" => $updatedDetails["domain"],
            ":abstract" => $updatedDetails["abstract"],
            ":authors" => $updatedDetails["authors"],
            ":role_type" => $updatedDetails["role_type"],
            ':publication_id' => $publicationId
        ]);

        if ($result === false) {
            throw new Exception('Error executing the update statement: ');
        }
    }
}