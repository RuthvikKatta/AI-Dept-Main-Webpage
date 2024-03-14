<?php

class TimeTable
{
    private $dbh;
    private $timeTable = 'timetable';
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
    public function getLeisuresByHour($day, $hour)
    {
        $statement = $this->dbh->prepare(
            "SELECT * FROM " . $this->timeTable . " WHERE day = :day AND hour = :hour"
        );

        if (false === $statement) {
            return [];
        }

        $result = $statement->execute([
            ":day" => $day,
            ":hour" => $hour,
        ]);

        if (false === $result) {
            return [];
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $teaching = array_unique($rows);

        $allFacultyStatement = $this->dbh->prepare(
            "SELECT staff_id FROM Staff WHERE role = 'Teaching'"
        );

        if (false === $allFacultyStatement) {
            return [];
        }

        $result = $allFacultyStatement->execute();

        if (false === $result) {
            return [];
        }

        $facultyResult = $allFacultyStatement->fetchAll(PDO::FETCH_ASSOC);

        $allTeachingFaculty = array_column($facultyResult, 'staff_id');

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
    function getTimetableForFaculty($facultyId)
    {
        $statement = $this->dbh->prepare(
            "SELECT tt.day, tt.hour, sub.name, cls.year, cls.section FROM ". $this->timeTable ." tt
             LEFT JOIN subject sub on sub.subject_id = tt.subject_id
             LEFT JOIN class cls on cls.class_id = tt.class_id
             WHERE instructor_id = :instructor_id"
        );

        if (false === $statement) {
            return false;
        }

        $timetable = array(
            'Monday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Tuesday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Wednesday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Thursday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Friday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Saturday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            )
        );

        $result = $statement->execute([
            ':instructor_id' => $facultyId
        ]);

        if (false === $result) {
            return false;
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $day = $row['day'];
            $hour = $row['hour'];
            $subjectName = $row['name'];
            $classYear = $row['year'];
            $classSection = $row['section'];

            $timetable[$day][$hour] = array(
                'subjectName' => $subjectName,
                'year' => $classYear,
                'section' => $classSection
            );
        }

        return $timetable;
    }
    function getTimetableForClass($year, $section)
    {
        $statement = $this->dbh->prepare(
            "SELECT tt.day, tt.hour, sub.name, cls.year, cls.section FROM ". $this->timeTable ." tt
             LEFT JOIN subject sub on sub.subject_id = tt.subject_id
             LEFT JOIN class cls on cls.class_id = tt.class_id
             WHERE cls.year = :year and cls.section = :section"
        );

        if (false === $statement) {
            return false;
        }

        $timetable = array(
            'Monday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Tuesday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Wednesday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Thursday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Friday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            ),
            'Saturday' => array(
                '1' => array(),
                '2' => array(),
                '3' => array(),
                '4' => array(),
                '5' => array(),
                '6' => array()
            )
        );

        $result = $statement->execute([
            ':year' => $year,
            ':section' => $section,
        ]);

        if (false === $result) {
            return false;
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $day = $row['day'];
            $hour = $row['hour'];
            $subjectName = $row['name'];
            $classYear = $row['year'];
            $classSection = $row['section'];

            $timetable[$day][$hour] = array(
                'subjectName' => $subjectName,
                'year' => $classYear,
                'section' => $classSection
            );
        }

        return $timetable;
    }
}