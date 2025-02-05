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
    IN Phone_no VARCHAR(10),
    IN Password VARCHAR(20),
    IN Photo BLOB
) BEGIN
-- Declare a variable to hold the default photo
DECLARE default_photo BLOB;

-- Assign the default photo value
SET
    default_photo = 'iVBORw0KGgoAAAANSUhEUgAAAMwAAADACAMAAAB/Pny7AAAAMFBMVEXk5ueutLeor7La3d/n6erh4+SrsbXT1ti7wMPq7O2zubzKztDHy83Bxsi4vcDd4OHJo5BXAAAFEElEQVR4nO2cXZucIAxGUQIoCv7/f1t02t35XoHXSdxyrtq9mvMAgUSIUo1Go9FoNBqNRqPRaDQajUaj0Wg0Go2GbIj7ByCghFk2jFn/w/2Dikke2k1zGDZCmCenzTl9yLh5iN5e4+MQ3Ol8aNQh+s7a7o5NyJ1Jh0a3mtyL/PPpungenVH7Fx7fRD2eQYeW4dWYXNMPywls3Mv5dTfb+on7p/4ALaHfpXKCwSHt97skm07LtaG9U+xrqnWO+ze/ZMoyuehIXTgFLgmZNmUuIm2o1EWgTVr75ThZMS3F5Lw4doMXtt9k7JWP9AP3z7/B1bgkG0nLRleprDZyJhrFigWzYaMUGaqcZNvQSIloVK2S8NwWF2iqnWQbk4yhQaikoTHcHgo2MDKGhn6uXuzCRm6TykPZLfwBjQaUiw3sMiaiZLqomV2As4x/nlEAxbKEnXldlNlVvtwpMyysLrSjrLwfz1tGq8j8n8G7aGDb/wZzEc0A1/+607Cez/Z9vtgtwxoBSFfnmDdEzgiADWYJVhmHlbGc4Qx6mOmYKwHkoEuGWwbqkmT4XA4YmV8l09YMSuZXhebftGlWfWN6hDehwR40u8iaaoJTgIG3RIuVmVll0Jkmb9qMDWfMhTONK2gy52ZqLTUD62a8JYAkM8NcuJcMNnH23LVmpXCfNJh3GQWdZzP7wNCCcmGuNF9sUPOM/8PZGgJAwZl/+av15gzExUtwSXlA/dWZFMsErJiVEXEKiCO3xj8At5q4Fb6gqdbGTmIGRqnKUoAVsvov1J7QuK8z3FJXdJZ033SlohhgA/ePv6e86MR9l+EZS+HnTRvlXAP+ZikKAlba3fkLZEpc5L7UypfxRqqLotzNM0p+SUsh4wmdFZGPvYHc7uzGRhHp2DtomXcNThoWkWHsHjc8Pp2/V7GD3BeaN5BxQ/9Ox/aDkxvF7kk6oX/hY3sbNHu5L5PFxUcf2/dxkncU2wEpF5JQ/7eBRvpXF040vR6gcVTaTStOm/EU3RneQpSUxnP3nFHKrCxXbH8w6jxe6y9NCtpd2udE/9V5xvu4ttKZnNN6ka+09TJKFmGI23p/1namX//uY1gX0SLViLZmRnOI3j6ReCbV+SEN06LE+RAlkWGbUj953Ch16xhpST40LlMSyeyfcSPklIiKJo1m8s+6MuUIpYwzOPZNiEYN+0DrZ8PpQ2buEN9m/pIO03zVDZNOxjiVi07kSXP0+5yllN67D+cHpHQ4RGXTie6jSYLel+eXknLqT9UHSM/Ya6ZPbLrPlAhITdgHQK90wvGRbdTx0Bl2peOnY085RIfPsCub7tCPHbQgLzHu0TnuOl12LzaATjimbS2pGbzf76E/5hJqTutCIEd02iMdWVy6Axo5kftcFHu0maELh2Hp39ggwwCzCzSowd8vMdoIcIHZoJ9iFQLpFkKl10jQIF4KmIFrf7kH8O6R4wzzHFt9JR38RLaKyg6VVN2GEUpf9fQB+W4JQV8x0eqv+KKpuWUra1y69QRdagO5Fo+msD4Ie0uCxA5lH3LGz1Yv9lK0daKbfYAo61Jb3x33GEoeQIo4+D+j6H469HE8koIjGvQFNpbsvQbcHw9J/ktbbD8JMJkhgATPsuyqILrVD5TsvgHizsvXZMYzZONSPHnVAHivPyx5i0bkgfmbzEUD6PR/IJk7DbQ3Dp6YIyN6y1zJyTfN0Msm68WtFs7ZHkY0Go1Go9Fo/H/8AVoJTYWJQwYkAAAAAElFTkSuQmCC';

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

END / DELIMITER;

-- creating procedure for registering admin
DELIMITER / CREATE PROCEDURE RegisterExaminer (
    IN EID BIGINT,
    IN ExaminerName VARCHAR(25),
    IN Phone_no VARCHAR(10),
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