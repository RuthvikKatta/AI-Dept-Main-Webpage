
CREATE TABLE Class_Details (
    class_year INT NOT NULL,
    semester ENUM(1, 2) NOT NULL,
    section CHAR(1),
    class_incharge_id VARCHAR(10) NOT NULL,
    class_room_number INT,
    no_of_students INT NOT NULL,
    PRIMARY KEY(class_year, section),
    FOREIGN KEY(class_incharge_id) REFERENCES Staff(staff_id)
);

CREATE TABLE Student_Class (
    student_id VARCHAR(10) NOT NULL,
    class_year INT NOT NULL,
    semester ENUM(1, 2) NOT NULL,
    section CHAR(1),
    PRIMARY KEY(student_id, class_year),
    FOREIGN KEY(student_id) REFERENCES Student(student_id)
)

CREATE TABLE Student (
    student_id VARCHAR(10) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    salutation ENUM('Mr', 'Ms', 'Mrs') NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    section_id ENUM('A', 'B') NOT NULL,
    joining_date DATE NOT NULL,
    admission_type VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    mobile_number VARCHAR(20),
    alternative_mobile_number VARCHAR(20),
    email VARCHAR(255),
    profile_image_link VARCHAR(255)
);

CREATE TABLE Subject (
    subject_id INT PRIMARY KEY,
    subject_name VARCHAR(255) NOT NULL,
    subject_year INT NOT NULL,
    subject_semester INT NOT NULL,
    subject_type ENUM('Theoretical', 'Lab') NOT NULL,
    credits INT NOT NULL
);

CREATE TABLE Staff (
    staff_id VARCHAR(10) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    salutation ENUM('Mr', 'Ms', 'Mrs', 'Dr', 'Prof') NOT NULL,
    qualification VARCHAR(255),
    role ENUM('Teaching', 'Non Teaching') NOT NULL,
    designation_id INT,
    experience_years INT,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    age INT,
    mobile_number VARCHAR(20),
    alternative_mobile_number VARCHAR(20),
    email VARCHAR(255),
    profile_image_link VARCHAR(255)
);

CREATE TABLE Staff_Teaching_Subject (
    staff_id VARCHAR(10) NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (staff_id) REFERENCES Staff(staff_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES Subject(subject_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Designation (
    designation_id INT PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE Mentoring (
    mentee_id VARCHAR(10),
    mentor_id VARCHAR(10),
    PRIMARY KEY(mentee_id, mentor_id),
    mentee_id FOREIGN KEY REFERENCES Student(student_id),
    mentor_id FOREIGN KEY REFERENCES Staff(staff_id)
);

CREATE TABLE Mentoring_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    mentee_id VARCHAR(10),
    mentor_id VARCHAR(10),
    comment TEXT,
    comment_datetime DATETIME,
    FOREIGN KEY(mentee_id) REFERENCES Student(student_id),
    FOREIGN KEY(mentor_id) REFERENCES Staff(staff_id)
);

CREATE TABLE Publications (
    publication_id INT AUTO_INCREMENT PRIMARY KEY,
    author_id VARCHAR(10) NOT NULL,  -- staff_id or student_id
    pub_title VARCHAR(255),
    pub_date DATE,
    abstract_text TEXT,
    journal_name VARCHAR(255),
    roleType ENUM('Faculty', 'Student'),
    FOREIGN KEY(author_id) REFERENCES Staff(staff_id)
);

CREATE TABLE UserAuthentication (
    user_id VARCHAR(10) PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Faculty', 'Admin') NOT NULL,
    last_login_timestamp TIMESTAMP,
    failed_login_attempts INT DEFAULT 0
);

CREATE TABLE Admin (
    user_id VARCHAR(10) PRIMARY KEY,
    admin_level VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES UserAuthentication_test(user_id)
);


CREATE TABLE LeaveRecord (
    leave_id INT PRIMARY KEY AUTO_INCREMENT,
    applied_by VARCHAR(10),
    applied_on DATE,
    applied_from DATE,
    applied_to DATE,
    total_days INT,
    reason TEXT,
    status BOOLEAN,
    FOREIGN KEY (applied_by) REFERENCES Staff(staff_id)
);

CREATE TABLE marks_record (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(10),
    subject_id INT,
    marks_type ENUM('MID', 'ASSIGNMENT', 'SEMESTER'),
    marks_obtained INT,
    total_marks INT,
    FOREIGN KEY (student_id) REFERENCES Student(student_id),
    FOREIGN KEY (subject_id) REFERENCES Subject(subject_id)
);


-- Vertabelo
-- https://www.w3docs.com/snippets/php/secure-hash-and-salt-for-php-passwords.html#:~:text=To%20securely%20hash%20and%20salt,secure%20and%20resistant%20to%20attacks.&text=The%20password_hash%20function%20generates%20a,and%20storing%20a%20salt%20yourself. Authentication example link


-- function debug_to_console($data)
-- {
--     $output = $data;
--     if (is_array($output))
--         $output = implode(',', $output);

--     echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
-- }



SELECT 
    student_id,
    SUM(CASE
        WHEN subject_id = 102 THEN present
        ELSE 0
    END) AS '102',
    SUM(CASE
        WHEN subject_id = 105 THEN present
        ELSE 0
    END) AS '105',
    SUM(CASE
        WHEN subject_id = 106 THEN present
        ELSE 0
    END) AS '106',
    SUM(CASE
        WHEN subject_id = 107 THEN present
        ELSE 0
    END) AS '107'
FROM
    (SELECT 
        stu.student_id,
            t.subject_id,
            total.total_classes,
            COALESCE(ab.absent, 0) AS absent,
            (total.total_classes - COALESCE(ab.absent, 0)) AS present
    FROM
        Student stu -- Corrected table name 
    CROSS JOIN (SELECT DISTINCT
        subject_id
    FROM
        attendance_log) t
    LEFT JOIN (SELECT 
        subject_id, COUNT(*) AS total_classes
    FROM
        attendance_log
    GROUP BY subject_id) total ON total.subject_id = t.subject_id
    LEFT JOIN (SELECT 
        student_id, subject_id, COUNT(*) AS absent
    FROM
        absentees_log
    GROUP BY student_id , subject_id) ab ON stu.student_id = ab.student_id
        AND t.subject_id = ab.subject_id) AS main
GROUP BY student_id ;


SELECT 
    subq.subject_id,
    MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'I' THEN marks_obtained END) AS `Mid I`,
    MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'I' THEN marks_obtained END) AS `Assignment I`,
    MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'II' THEN marks_obtained END) AS `Mid II`,
    MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'II' THEN marks_obtained END) AS `Assignment II`,
    MAX(CASE WHEN marks_type = 'MID' AND exam_session = 'III' THEN marks_obtained END) AS `Mid III`,
    MAX(CASE WHEN marks_type = 'Assignment' AND exam_session = 'III' THEN marks_obtained END) AS `Assignment III`
FROM
    (SELECT subject_id, subject_name FROM subject WHERE subject_id IN (1, 2, 3)) subq
JOIN
    (SELECT * FROM student WHERE student_id = '20911A3594') sq
ON
    1 = 1  -- A constant condition for a cross join
LEFT JOIN
    marks
ON
    marks.student_id = sq.student_id AND marks.subject_id = subq.subject_id
GROUP BY
    subq.subject_id;