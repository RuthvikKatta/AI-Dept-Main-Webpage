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

    public function create($user_id, $username, $password, $role)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->usersTableName . ' (user_id, username, password, role) VALUES (:user_id, :username, :password, :role)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        if (
            false === $statement->execute([
                ':username' => $username,
                ':password' => $password,
            ])
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
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

        if (!password_verify($password, $row['password'])) {
            $result = $statement->execute([':username' => $username]);
            return 'PASSWORD_NOT_MATCH';
        }

        return 'SUCCESS';
    }

    public function verifyRole($username, $role)
    {
        $statement = $this->dbh->prepare(
            'SELECT * from ' . $this->usersTableName . ' where username = :username'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        $result = $statement->execute([':username' => $username]);

        if (false === $result) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($row)) {
            return false;
        }

        return ucfirst($row['role']) === ucfirst($role);
    }
}