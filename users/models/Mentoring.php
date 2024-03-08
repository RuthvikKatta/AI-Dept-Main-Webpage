<?php
class Mentoring
{
    private $dbh;
    private $mentoringTable = 'mentoring';
    private $mentoringLogTable = 'mentoring_log';
    private $mentoringQnA = 'mentoring_qna';
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
    public function removeMentees($mentor_id, $mentee_id)
    {
        $deleteStatement = $this->dbh->prepare(
            'DELETE FROM ' . $this->mentoringTable . ' WHERE mentor_id = :mentor_id AND mentee_id = :mentee_id'
        );

        if (false === $deleteStatement) {
            return false;
        }
        
        try {
            false === $deleteStatement->execute([
                ':mentor_id' => $mentor_id,
                ':mentee_id' => $mentee_id,
            ]);
        } catch (Exception $e) {
            return false;
        }
    }
    public function assignMentees($mentorId, $menteeIds)
    {
        $message = '';

        foreach ($menteeIds as $menteeId) {
            $checkQuery = 'SELECT mentee_id, mentor_id FROM ' . $this->mentoringTable . ' WHERE mentee_id = :mentee_id';
            $checkStatement = $this->dbh->prepare($checkQuery);

            if (false === $checkStatement) {
                continue;
            }

            $checkResult = $checkStatement->execute([
                ":mentee_id" => $menteeId
            ]);

            if (false === $checkResult) {
                continue;
            }

            $checkStatementResult = $checkStatement->fetch(PDO::FETCH_ASSOC);
            
            if (!empty($checkStatementResult)) {
                $existingMentorId = $checkStatementResult['mentor_id'];
                $message .= "$menteeId is already assigned to $existingMentorId. \n";
                continue; 
            }

            $addQuery = 'INSERT INTO ' . $this->mentoringTable . ' (mentor_id, mentee_id) VALUES (:mentor_id, :mentee_id)';
            $addStatement = $this->dbh->prepare($addQuery);

            if (false === $addStatement) {
                continue;
            }

            $addResult = $addStatement->execute([
                ":mentor_id" => $mentorId,
                ":mentee_id" => $menteeId
            ]);

            if (false === $addResult) {
                $message .= "$menteeId Doesn't Exist. \n";
            }
        }

        if (empty($message)) {
            return 'Added successfully';
        } else {
            return $message;
        }
    }
}