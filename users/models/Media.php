<?php


class Media
{
    private $dbh;
    private $mediaTable = 'media';
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
    public function getCarousalImages()
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->mediaTable
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
    public function addCarousalImage($imageName)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO " . $this->mediaTable . "(file_name) VALUES (:file_name)"
        );

        if (false === $statement) {
            return [];
        }

        if (
            false === $statement->execute([
                ':file_name' => $imageName,
            ])
        ) {
            return false;
        } else {
            return true;
        }
    }
    public function deleteCarousalImage(string $imageId)
    {
        $statement = $this->dbh->prepare(
            "DELETE FROM " . $this->mediaTable . " WHERE id = :id"
        );

        if (false === $statement) {
            return [];
        }

        if (
            false === $statement->execute([
                ':id' => $imageId,
            ])
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }
    }
}