CREATE TABLE Subject (
    subject_id INT PRIMARY KEY,
    subject_name VARCHAR(255) NOT NULL,
    semester INT NOT NULL,
    subject_type ENUM('Theoretical', 'Lab') NOT NULL,
    credits INT NOT NULL
);

CREATE TABLE Staff (
    staff_id INT PRIMARY KEY,
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
    staff_id INT,
    subject_id INT,
    FOREIGN KEY (staff_id) REFERENCES Staff(staff_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES Subject(subject_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Student (
    student_id INT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    salutation ENUM('Mr', 'Ms', 'Mrs') NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    section_id ENUM('A', 'B') NOT NULL,
    joining_date DATE NOT NULL,
    date_of_birth DATE NOT NULL,
    mobile_number VARCHAR(20),
    alternative_mobile_number VARCHAR(20),
    email VARCHAR(255),
    profile_image_link VARCHAR(255)
);

CREATE TABLE Designation (
    designation_id INT PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

INSERT INTO Designation (designation_id, title) VALUES
(15, 'HOD'),
(14, 'Professor'),
(13, 'Associate Professor'),
(12, 'Assistant Professor');

CREATE TABLE Mentoring (
    mentee_id INT,
    mentor_id INT,
    PRIMARY KEY(mentee_id, mentor_id),
    mentee_id FOREIGN KEY REFERENCES Student(student_id),
    mentor_id FOREIGN KEY REFERENCES Staff(staff_id)
);

CREATE TABLE Mentoring_Comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    mentee_id INT,
    mentor_id INT,
    comment TEXT,
    comment_datetime DATETIME,
    FOREIGN KEY(mentee_id) REFERENCES Student(student_id),
    FOREIGN KEY(mentor_id) REFERENCES Staff(staff_id)
);

CREATE TABLE Publications (
    publication_id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT,  -- staff_id or student_id
    pub_title VARCHAR(255),
    pub_date DATE,
    abstract_text TEXT,
    journal_name VARCHAR(255),
    is_faculty BOOLEAN,
    FOREIGN KEY(author_id) REFERENCES Staff(staff_id)
);

CREATE TABLE Authorization (
    user_id INT,   -- staff_id or student_id
    role ENUM('admin', 'user') DEFAULT 'user',
);

CREATE TABLE Account (
    username VARCHAR(60) NOT NULL UNIQUE,
    account_pass VARCHAR(60) NOT NULL,
    pass_changed_date DATE
);

-- Vertabelo
-- https://www.w3docs.com/snippets/php/secure-hash-and-salt-for-php-passwords.html#:~:text=To%20securely%20hash%20and%20salt,secure%20and%20resistant%20to%20attacks.&text=The%20password_hash%20function%20generates%20a,and%20storing%20a%20salt%20yourself. Authentication example link