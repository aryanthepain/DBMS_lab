-- Students table
CREATE TABLE
    Students (
        ID VARCHAR(10) PRIMARY KEY,
        Name TEXT,
        PhoneNo TEXT,
        Email TEXT,
        Username TEXT UNIQUE,
        Password TEXT
    );

-- Instructors table
CREATE TABLE
    Instructors (
        ID VARCHAR(10) PRIMARY KEY,
        Name TEXT,
        PhoneNo TEXT,
        Email TEXT,
        Username TEXT UNIQUE,
        Password TEXT
    );

-- Courses table
CREATE TABLE
    Courses (
        ID VARCHAR(10) PRIMARY KEY,
        Name TEXT,
        Category TEXT,
        Difficulty TEXT,
        Description TEXT,
        Price TEXT,
        PreRequsites TEXT,
        EstimatedTimeOfCompletion TEXT,
        InstructorID VARCHAR(10),
        FOREIGN KEY (InstructorID) REFERENCES Instructors (ID)
    );

-- CourseContents table
CREATE TABLE
    CourseContents (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        Name TEXT,
        Description TEXT,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID)
    );

-- Exams table
CREATE TABLE
    Exams (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        Name TEXT,
        Difficulty TEXT,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID)
    );

-- Questions table
CREATE TABLE
    Questions (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        ExamID INT,
        QuestionName TEXT,
        OptionA TEXT,
        OptionB TEXT,
        OptionC TEXT,
        OptionD TEXT,
        Score TEXT,
        Difficulty TEXT,
        CorrectOption TEXT,
        FOREIGN KEY (ExamID) REFERENCES Exams (ID)
    );

-- Discussions table
CREATE TABLE
    Discussions (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        StudentID VARCHAR(10),
        Discussion TEXT,
        TIMESTAMP TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID),
        FOREIGN KEY (StudentID) REFERENCES Students (ID)
    );

-- Announcements table
CREATE TABLE
    Announcements (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        Announcement TEXT,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID)
    );

-- CourseRegistration table
CREATE TABLE
    CourseRegistration (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        StudentID VARCHAR(10),
        CourseID VARCHAR(10),
        DateOfRegistration TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        StatusOfCompletion TEXT,
        EndDate TIMESTAMP NULL,
        FOREIGN KEY (StudentID) REFERENCES Students (ID),
        FOREIGN KEY (CourseID) REFERENCES Courses (ID)
    );

CREATE TABLE
    Respones (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        StudentID VARCHAR(10),
        QuestionID INT,
        Response TEXT,
        Score INT,
        FOREIGN KEY (StudentID) REFERENCES Students (ID),
        FOREIGN KEY (QuestionID) REFERENCES Questions (ID)
    );