<?php

class Material
{
    private $dbh;
    private $materialTable = 'material';
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
    public function addMaterial(string $name, string $materialType, string $subjectId = null)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO " . $this->materialTable . "(name, material_type, subject_id) VALUES (:name, :material_type, :subject_id)"
        );

        if ($statement === false) {
            return false;
        }

        // $subjectId = ($subjectId === '') ? null : $subjectId;

        if (
            false === $statement->execute([
                ':name' => $name,
                ':material_type' => $materialType,
                ':subject_id' => $subjectId,
            ])
        ) {
            return false;
        } else {
            return true;
        }
    }
    public function getMaterialByMaterialType(string $materialType)
    {
        $statement = $this->dbh->prepare(
            "SELECT name FROM " . $this->materialTable . " WHERE material_type = :material_type"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":material_type" => $materialType
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getMaterials(string $materialType, ?string $subjectId = null)
    {
        $query = "SELECT * FROM " . $this->materialTable . " WHERE material_type = :material_type";

        if ($subjectId !== null) {
            $query .= " AND subject_id = :subject_id";
        }

        $statement = $this->dbh->prepare($query);

        if (false === $statement) {
            return [];
        }

        $params = [":material_type" => $materialType];

        if ($subjectId !== null) {
            $params[":subject_id"] = $subjectId;
        }

        $result = $statement->execute($params);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
    public function getAllMaterials()
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->materialTable
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
    public function deleteMaterial(string $fileId)
    {
        $statement = $this->dbh->prepare(
            "DELETE FROM " . $this->materialTable . " WHERE material_id = :material_id"
        );

        if (false === $statement) {
            return [];
        }

        if (
            false === $statement->execute([
                ':material_id' => $fileId,
            ])
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }
    }
}