<?php
class Mentoring
{
    private $dbh;
    private $mentoringTable = 'mentoring';
    private $mentoringLogTable = 'mentoring_log';
    private $mentoringQnA = 'mentoring_qna';

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
    public function getMentees($mentor_id)
    {
        $statement = $this->dbh->prepare(
            'SELECT mentee_id FROM ' . $this->mentoringTable . ' WHERE mentor_id = :mentor_id'
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([':mentor_id' => $mentor_id]);

        if (false === $result) {
            return [];
        }

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (is_array($row)) {
            return $row;
        }
        return [];
    }
    public function addMentorComment($mentor_id, $mentee_id, $comment)
    {
        $statement = $this->dbh->prepare(
            'INSERT INTO ' . $this->mentoringLogTable . ' (mentor_id, mentee_id, comment) VALUES (:mentor_id, :mentee_id, :comment)'
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        if (
            false === $statement->execute(
                [
                    ':mentor_id' => $mentor_id,
                    ':mentee_id' => $mentee_id,
                    ':comment' => $comment,
                ]
            )
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }
    }
    public function getMenteePrevComments($mentee_id)
    {
        $statement = $this->dbh->prepare(
            'SELECT * FROM ' . $this->mentoringLogTable . ' WHERE mentee_id = :mentee_id'
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute(
            [
                ':mentee_id' => $mentee_id
            ]
        );

        if (false === $result) {
            return [];
        }

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (is_array($row)) {
            return $row;
        }
        return [];
    }
    public function addQuestion($mentor_id, $mentee_id, $content)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO  " . $this->mentoringQnA . " (content, content_type, mentor_id, mentee_id) VALUES 
            (:content, 'Q', :mentor_id, :mentee_id)"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        if (
            false === $statement->execute(
                [
                    ':mentor_id' => $mentor_id,
                    ':mentee_id' => $mentee_id,
                    ':content' => $content,
                ]
            )
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }

        return $this->dbh->lastInsertId();
    }
    public function addAnswer($mentor_id, $mentee_id, $content, $related_q_id)
    {
        $statement = $this->dbh->prepare(
            "INSERT INTO  " . $this->mentoringQnA . " (content, content_type, mentor_id, mentee_id, related_q_id) VALUES 
            (:content, 'A', :mentor_id, :mentee_id, :related_q_id)"
        );

        if (false === $statement) {
            throw new Exception('Invalid prepare statement');
        }

        if (
            false === $statement->execute(
                [
                    ':mentor_id' => $mentor_id,
                    ':mentee_id' => $mentee_id,
                    ':content' => $content,
                    ':related_q_id' => $related_q_id,
                ]
            )
        ) {
            throw new Exception(implode(' ', $statement->errorInfo()));
        }
    }
    public function getQnA($mentor_id, $mentee_id)
    {
        $questionStatement = $this->dbh->prepare(
            "SELECT q_id, content FROM " . $this->mentoringQnA . " WHERE content_type = 'Q' AND mentor_id = :mentor_id AND mentee_id = :mentee_id"
        );

        if (false === $questionStatement) {
            return ['question' => [], 'answer' => []];
        }

        $questionResult = $questionStatement->execute([
            ":mentor_id" => $mentor_id,
            ":mentee_id" => $mentee_id
        ]);

        if (false === $questionResult) {
            return ['question' => [], 'answer' => []];
        }

        $questionRows = $questionStatement->fetchAll(PDO::FETCH_ASSOC);
        $questions = array_column($questionRows, 'content');

        $qIds = array_column($questionRows, 'q_id');

        $answerStatement = $this->dbh->prepare(
            "SELECT * FROM " . $this->mentoringQnA . " WHERE content_type = 'A' AND mentor_id = :mentor_id AND mentee_id = :mentee_id AND related_q_id = :related_q_id"
        );

        if (false === $answerStatement) {
            return ['question' => [], 'answer' => []];
        }

        $answers = [];

        foreach ($qIds as $qId) {
            $answerResult = $answerStatement->execute([
                ":mentor_id" => $mentor_id,
                ":mentee_id" => $mentee_id,
                ":related_q_id" => $qId
            ]);

            if (false === $answerResult) {
                return ['question' => [], 'answer' => []];
            }

            $answerRow = $answerStatement->fetch(PDO::FETCH_ASSOC);
            $answers[] = $answerRow['content'];
        }

        return ['question' => $questions, 'answer' => $answers];
    }
    public function assignMentees($mentor_id, array $mentee_ids)
    {
        try {
            $insertStatement = $this->dbh->prepare(
                'INSERT INTO ' . $this->mentoringTable . ' (mentor_id, mentee_id) VALUES (:mentor_id, :mentee_id)'
            );

            if (false === $insertStatement) {
                throw new Exception('Invalid prepare statement for insert');
            }

            foreach ($mentee_ids as $mentee_id) {
                $insertResult = $insertStatement->execute([
                    ':mentor_id' => $mentor_id,
                    ':mentee_id' => $mentee_id,
                ]);

                if (false === $insertResult) {
                    throw new Exception('Error inserting relationship: ' . $insertStatement->errorInfo()[2]);
                }
            }
        } catch (Exception $e) {

        }
    }
    public function removeMentees($mentor_id, $mentee_ids)
    {
        try {
            $deleteStatement = $this->dbh->prepare(
                'DELETE FROM ' . $this->mentoringTable . ' WHERE mentor_id = :mentor_id AND mentee_id = :mentee_id'
            );

            if (false === $deleteStatement) {
                throw new Exception('Invalid prepare statement for delete');
            }

            foreach ($mentee_ids as $mentee_id) {
                $deleteResult = $deleteStatement->execute([
                    ':mentor_id' => $mentor_id,
                    ':mentee_id' => $mentee_id,
                ]);

                if (false === $deleteResult) {
                    throw new Exception('Error deleting relationship: ' . $deleteStatement->errorInfo()[2]);
                }
            }
        } catch (Exception $e) {
        }
    }
}