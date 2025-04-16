-- ATP - create.sql
-- This file creates the necessary tables for the lab8_elearn database.
CREATE DATABASE IF NOT EXISTS lab8_elearn;

USE lab8_elearn;

-- Students table
CREATE TABLE
    IF NOT EXISTS Students (
        ID VARCHAR(10) PRIMARY KEY,
        Name TEXT,
        PhoneNo TEXT,
        Email TEXT,
        Username TEXT UNIQUE,
        Password TEXT
    ) ENGINE = InnoDB;

-- Instructors table
CREATE TABLE
    IF NOT EXISTS Instructors (
        ID VARCHAR(10) PRIMARY KEY,
        Name TEXT,
        PhoneNo TEXT,
        Email TEXT,
        Username TEXT UNIQUE,
        Password TEXT
    ) ENGINE = InnoDB;

-- Courses table
CREATE TABLE
    IF NOT EXISTS Courses (
        ID VARCHAR(10) PRIMARY KEY,
        Name TEXT,
        Category TEXT,
        Difficulty TEXT,
        Description TEXT,
        Price TEXT,
        PreRequsites TEXT,
        EstimatedTimeOfCompletion TEXT,
        InstructorID VARCHAR(10),
        FOREIGN KEY (InstructorID) REFERENCES Instructors (ID) ON DELETE SET NULL
    ) ENGINE = InnoDB;

-- CourseContents table
CREATE TABLE
    IF NOT EXISTS CourseContents (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        Name TEXT,
        Description TEXT,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- Exams table
CREATE TABLE
    IF NOT EXISTS Exams (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        Name TEXT,
        Difficulty TEXT,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- Questions table
CREATE TABLE
    IF NOT EXISTS Questions (
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
        FOREIGN KEY (ExamID) REFERENCES Exams (ID) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- Discussions table
CREATE TABLE
    IF NOT EXISTS Discussions (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        StudentID VARCHAR(10),
        Discussion TEXT,
        TIMESTAMP TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID) ON DELETE CASCADE,
        FOREIGN KEY (StudentID) REFERENCES Students (ID) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- Announcements table
CREATE TABLE
    IF NOT EXISTS Announcements (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CourseID VARCHAR(10),
        Announcement TEXT,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- CourseRegistration table
CREATE TABLE
    IF NOT EXISTS CourseRegistration (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        StudentID VARCHAR(10),
        CourseID VARCHAR(10),
        DateOfRegistration TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        StatusOfCompletion TEXT,
        EndDate TIMESTAMP NULL,
        FOREIGN KEY (StudentID) REFERENCES Students (ID) ON DELETE CASCADE,
        FOREIGN KEY (CourseID) REFERENCES Courses (ID) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- Responses table
CREATE TABLE
    IF NOT EXISTS Responses (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        StudentID VARCHAR(10),
        QuestionID INT,
        Response TEXT,
        Score INT,
        FOREIGN KEY (StudentID) REFERENCES Students (ID) ON DELETE CASCADE,
        FOREIGN KEY (QuestionID) REFERENCES Questions (ID) ON DELETE CASCADE
    ) ENGINE = InnoDB;