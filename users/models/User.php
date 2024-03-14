<?php
class User
{
    private $dbh;
    private $usersTableName = 'user_authentication';
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

    public function createUser($username, $password, $role)
    {
        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->usersTableName . ' (username, password, role) VALUES (:username, :password, :role)'
        );

        if (false === $statement) {
            return false;
        }

        if (
            false === $statement->execute([
                ':username' => $username,
                ':password' => $password,
                ':role' => $role,
            ])
        ) {
            return false;
        }
    }

    public function exists($username, $password, $role)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->usersTableName . ' WHERE username = :username and role = :role'
        );

        if (false === $statement) {
            return 'SERVER_ERROR';
        }

        $result = $statement->execute([':username' => $username, ':role' => $role]);

        if (false === $result) {
            return 'SERVER_ERROR';
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($row)) {
            return 'USERNAME_NOT_EXIST';
        }

        $statement = $this->dbh->prepare(
            'UPDATE ' . $this->usersTableName . ' SET failed_login_attempts = failed_login_attempts + 1 WHERE username = :username'
        );

        if ($password != $row['password']) {
            $result = $statement->execute([':username' => $username]);
            return 'PASSWORD_NOT_MATCH';
        }

        return 'SUCCESS';
    }
}