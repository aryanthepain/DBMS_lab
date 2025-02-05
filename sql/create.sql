-- student table
CREATE TABLE
    students (
        Roll_number BIGINT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL,
        photo BLOB NOT NULL,
        Phone_no INT (11) NOT NULL
    );

CREATE TABLE
    student_password (
        Roll_number BIGINT PRIMARY key NOT NULL,
        password VARCHAR(20) NOT NULL,
        FOREIGN key (Roll_number) REFERENCES students (Roll_number) ON DELETE cascade
    );

-- examiner table
CREATE TABLE
    examiners (
        EID BIGINT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL,
        Phone_no VARCHAR(10) NOT NULL
    );

CREATE TABLE
    examiner_password (
        EID BIGINT PRIMARY key NOT NULL,
        password VARCHAR(20) NOT NULL,
        FOREIGN key (EID) REFERENCES examiners (EID) ON DELETE cascade
    );

-- exam table
CREATE TABLE
    exam (
        Exam_ID BIGINT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL,
        fees INT (10) NOT NULL DEFAULT 0
    );

CREATE TABLE
    administered_by (
        EID BIGINT NOT NULL,
        Exam_ID BIGINT NOT NULL,
        FOREIGN KEY (EID) REFERENCES examiners (EID) ON DELETE cascade,
        FOREIGN KEY (Exam_ID) REFERENCES exam (Exam_ID) ON DELETE cascade
    );

-- defining slots
CREATE TABLE
    slot (
        slot_ID BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        start_time DATETIME NOT NULL,
        duration INT NOT NULL
    );

CREATE TABLE
    available_on_slot (
        slot_ID BIGINT NOT NULL,
        Exam_ID BIGINT NOT NULL,
        FOREIGN KEY (slot_ID) REFERENCES slot (slot_ID) ON DELETE cascade,
        FOREIGN KEY (Exam_ID) REFERENCES exam (Exam_ID) ON DELETE cascade
    );

-- booking exam
CREATE TABLE
    takes_exam (
        booking_ID INT NOT NULL PRIMARY key AUTO_INCREMENT,
        Roll_number BIGINT NOT NULL,
        Exam_ID BIGINT NOT NULL,
        transaction_ID text DEFAULT NULL,
        end_time DATETIME NULL DEFAULT NULL,
        slot_ID BIGINT DEFAULT NULL,
        difficulty_counter INT NOT NULL DEFAULT 0,
        FOREIGN key (Roll_number) REFERENCES students (Roll_number) ON DELETE cascade,
        FOREIGN KEY (slot_ID) REFERENCES slot (slot_ID) ON DELETE SET NULL,
        FOREIGN KEY (Exam_ID) REFERENCES exam (Exam_ID) ON DELETE cascade
    );

-- adding questions
CREATE TABLE
    questions (
        QID INT NOT NULL PRIMARY key AUTO_INCREMENT,
        question text NOT NULL,
        difficulty INT NOT NULL,
        option1 text NOT NULL,
        option2 text NOT NULL,
        option3 text NOT NULL,
        option4 text NOT NULL,
        correct_option INT NOT NULL
    );

CREATE TABLE
    uploaded_by (
        QID INT NOT NULL PRIMARY key,
        EID BIGINT NOT NULL,
        FOREIGN KEY (QID) REFERENCES questions (QID) ON DELETE cascade,
        FOREIGN KEY (EID) REFERENCES examiners (EID) ON DELETE cascade
    );

-- in exam
CREATE TABLE
    in_exam (
        QID INT NOT NULL PRIMARY key,
        Exam_ID BIGINT NOT NULL,
        FOREIGN KEY (QID) REFERENCES questions (QID) ON DELETE cascade,
        FOREIGN KEY (Exam_ID) REFERENCES exam (Exam_ID) ON DELETE cascade
    );

-- results
CREATE TABLE
    exam_results (
        result_ID BIGINT PRIMARY KEY AUTO_INCREMENT,
        booking_ID INT NOT NULL,
        QID INT NOT NULL,
        selected_option INT DEFAULT NULL,
        is_correct BOOLEAN DEFAULT NULL,
        start_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        end_time DATETIME DEFAULT NULL,
        FOREIGN KEY (booking_ID) REFERENCES takes_exam (booking_ID) ON DELETE CASCADE,
        FOREIGN KEY (QID) REFERENCES questions (QID) ON DELETE CASCADE
    );

-- feedback
CREATE TABLE
    feedback (
        feedback_ID INT PRIMARY KEY AUTO_INCREMENT,
        booking_ID INT NOT NULL,
        QID INT NOT NULL,
        EID BIGINT NOT NULL,
        feedback_text TEXT NOT NULL,
        FOREIGN KEY (booking_ID) REFERENCES takes_exam (booking_ID) ON DELETE CASCADE,
        FOREIGN KEY (QID) REFERENCES questions (QID) ON DELETE CASCADE,
        FOREIGN KEY (EID) REFERENCES examiners (EID) ON DELETE CASCADE
    );

-- creating procedure for registering students
DELIMITER /
-- Procedure for registering students
CREATE PROCEDURE RegisterStudent (
    IN Roll_number BIGINT,
    IN StudentName VARCHAR(25),
    IN Phone_no INT,
    IN Password VARCHAR(20),
    IN Photo BLOB
) BEGIN
-- Declare a variable to hold the default photo
DECLARE default_photo BLOB;

-- Assign the default photo value
SET
    default_photo = FROM_BASE64 (
        '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'
    );

-- Check if the Roll_number already exists
IF EXISTS (
    SELECT
        1
    FROM
        students
    WHERE
        Roll_number = Roll_number
) THEN SIGNAL SQLSTATE '45000'
SET
    MESSAGE_TEXT = 'Roll_number already exists';

ELSE
-- Insert into students table with photo or default_photo
INSERT INTO
    students (Roll_number, name, Phone_no, photo)
VALUES
    (
        Roll_number,
        StudentName,
        Phone_no,
        COALESCE(Photo, default_photo)
    );

-- Insert into student_password table
INSERT INTO
    student_password (Roll_number, password)
VALUES
    (Roll_number, Password);

END IF;

END / DELIMITER;

-- creating procedure for registering admin
DELIMITER / CREATE PROCEDURE RegisterExaminer (
    IN EID BIGINT,
    IN ExaminerName VARCHAR(25),
    IN Phone_no INT,
    IN Password VARCHAR(20)
) BEGIN
-- Check if the EID already exists
IF EXISTS (
    SELECT
        1
    FROM
        examiners
    WHERE
        EID = EID
) THEN SIGNAL SQLSTATE '45000'
SET
    MESSAGE_TEXT = 'EID already exists';

ELSE
-- Insert into examiners table
INSERT INTO
    examiners (EID, name, Phone_no)
VALUES
    (EID, ExaminerName, Phone_no);

-- Insert into examiner_password table
INSERT INTO
    examiner_password (EID, password)
VALUES
    (EID, Password);

END IF;

END / DELIMITER;