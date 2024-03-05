<?php

class TimeTable
{
    private $dbh;
    private $timeTable = 'timetable';
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
    public function getTodaysLeisures($day, $startTime, $endTime)
    {
        $statement = $this->dbh->prepare(
            "SELECT instructor_id FROM " . $this->timeTable . " WHERE day = :day AND start_time >= :start_time AND end_time <= :end_time"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":day" => $day,
            ":start_time" => $startTime,
            ":end_time" => $endTime,
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $teaching = array_unique($rows);

        $allFacultyStatement = $this->dbh->prepare(
            "SELECT staff_id FROM Faculty WHERE role = 'Teaching'"
        );

        if (false === $allFacultyStatement) {
            return [];
        }

        $result = $allFacultyStatement->execute();

        if (false === $result) {
            return [];
        }

        $facultyResult = $allFacultyStatement->fetchAll(PDO::FETCH_ASSOC);

        $allTeachingFaculty  = array_column($facultyResult, 'staff_id');

        return array_unique(array_diff($allTeachingFaculty, $teaching));
    }
    public function editTimetableDetails($timetableId, $updatedDetails)
    {
        try {
            $statement = $this->dbh->prepare(
                "UPDATE " . $this->timeTable . "
                SET class_id=:class_id,
                    day=:day,
                    start_time=:start_time,
                    end_time=:end_time,
                    subject_id=:subject_id,
                    instructor_id=:instructor_id
                WHERE timetable_id = :timetable_id"
            );

            if (false === $statement) {
                throw new Exception('Invalid prepare statement');
            }

            $result = $statement->execute([
                ":class_id" => $updatedDetails["class_id"],
                ":day" => $updatedDetails["day"],
                ":start_time" => $updatedDetails["start_time"],
                ":end_time" => $updatedDetails["end_time"],
                ":subject_id" => $updatedDetails["subject_id"],
                ":instructor_id" => $updatedDetails["instructor_id"],
                ':timetable_id' => $timetableId,
            ]);

            if ($result === false) {
                throw new Exception('Error executing the update statement: ' . $statement->errorInfo()[2]);
            }
        } catch (Exception $e) {
        }
    }
    public function addNewTimetableRecord($newTimetableDetails)
    {
        try {
            $statement = $this->dbh->prepare(
                "INSERT INTO " . $this->timeTable . "
                (class_id, day, start_time, end_time, subject_id, instructor_id)
                VALUES (:class_id, :day, :start_time, :end_time, :subject_id, :instructor_id)"
            );

            if (false === $statement) {
                throw new Exception('Invalid prepare statement');
            }

            $result = $statement->execute([
                ":class_id" => $newTimetableDetails["class_id"],
                ":day" => $newTimetableDetails["day"],
                ":start_time" => $newTimetableDetails["start_time"],
                ":end_time" => $newTimetableDetails["end_time"],
                ":subject_id" => $newTimetableDetails["subject_id"],
                ":instructor_id" => $newTimetableDetails["instructor_id"],
            ]);

            if ($result === false) {
                throw new Exception('Error executing the insert statement: ' . $statement->errorInfo()[2]);
            }
        } catch (Exception $e) {
        }
    }
}