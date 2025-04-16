-- ATP - insert.sql
-- This file inserts dummy data for the lab8_elearn database.
USE lab8_elearn;

-- Insert Students
INSERT INTO
    Students (ID, Name, PhoneNo, Email, Username, Password)
VALUES
    (
        'S001',
        'John Doe',
        '1234567890',
        'johndoe@example.com',
        'user1',
        'pass1'
    ),
    (
        'S002',
        'Jane Smith',
        '0987654321',
        'janesmith@example.com',
        'user2',
        'pass2'
    ),
    (
        'S003',
        'Michael Brown',
        '1122334455',
        'michaelbrown@example.com',
        'user3',
        'pass3'
    ),
    (
        'S004',
        'Emily White',
        '2233445566',
        'emilywhite@example.com',
        'user4',
        'pass4'
    ),
    (
        'S005',
        'David Green',
        '3344556677',
        'davidgreen@example.com',
        'user5',
        'pass5'
    );

-- Insert Instructors
INSERT INTO
    Instructors (ID, Name, PhoneNo, Email, Username, Password)
VALUES
    (
        'I001',
        'Dr. Alice Johnson',
        '5551234567',
        'alice.johnson@example.com',
        'user1',
        'pass1'
    ),
    (
        'I002',
        'Prof. Robert Davis',
        '5552345678',
        'robert.davis@example.com',
        'user2',
        'pass2'
    ),
    (
        'I003',
        'Dr. Susan Clark',
        '5553456789',
        'susan.clark@example.com',
        'user3',
        'pass3'
    ),
    (
        'I004',
        'Prof. William Harris',
        '5554567890',
        'william.harris@example.com',
        'user4',
        'pass4'
    ),
    (
        'I005',
        'Dr. Mary Martinez',
        '5555678901',
        'mary.martinez@example.com',
        'user5',
        'pass5'
    );

-- Insert Courses
INSERT INTO
    Courses (
        ID,
        Name,
        Category,
        Difficulty,
        Description,
        Price,
        PreRequsites,
        EstimatedTimeOfCompletion,
        InstructorID
    )
VALUES
    (
        'CS01',
        'Introduction to Programming',
        'Computer Science',
        'Beginner',
        'Fundamentals of programming using Python.',
        '100',
        'None',
        '4 weeks',
        'I001'
    ),
    (
        'CS02',
        'Data Structures and Algorithms',
        'Computer Science',
        'Intermediate',
        'Learn essential data structures and algorithms.',
        '150',
        'Introduction to Programming',
        '6 weeks',
        'I001'
    ),
    (
        'CS03',
        'Web Development with HTML, CSS, and JavaScript',
        'Web Development',
        'Intermediate',
        'Build dynamic websites using front-end technologies.',
        '200',
        'Basic Programming Knowledge',
        '8 weeks',
        'I001'
    ),
    (
        'ML05',
        'Introduction to Machine Learning',
        'Machine Learning',
        'Beginner',
        'Basics of machine learning using popular libraries.',
        '200',
        'None',
        '6 weeks',
        'I002'
    ),
    (
        'DL06',
        'Deep Learning with TensorFlow',
        'Machine Learning',
        'Advanced',
        'Build deep learning models with TensorFlow.',
        '350',
        'Introduction to Machine Learning',
        '10 weeks',
        'I002'
    ),
    (
        'PY07',
        'Data Analysis with Python',
        'Data Science',
        'Intermediate',
        'Analyze and visualize data with Python libraries.',
        '150',
        'Introduction to Programming',
        '8 weeks',
        'I002'
    ),
    (
        'NLP8',
        'Introduction to Natural Language Processing',
        'Machine Learning',
        'Intermediate',
        'Explore natural language processing techniques.',
        '180',
        'Introduction to Machine Learning',
        '8 weeks',
        'I002'
    ),
    (
        'DS09',
        'Introduction to Data Science',
        'Data Science',
        'Beginner',
        'Overview of data science tools and techniques.',
        '120',
        'None',
        '6 weeks',
        'I003'
    ),
    (
        'PY10',
        'Advanced Data Visualization with Python',
        'Data Science',
        'Advanced',
        'Master data visualization using Python.',
        '220',
        'Introduction to Data Science',
        '8 weeks',
        'I003'
    ),
    (
        'DB11',
        'Introduction to Cloud Computing',
        'Cloud Computing',
        'Beginner',
        'Learn the basics of cloud computing.',
        '180',
        'None',
        '5 weeks',
        'I004'
    ),
    (
        'DB12',
        'AWS Certified Solutions Architect',
        'Cloud Computing',
        'Advanced',
        'Prepare for the AWS Solutions Architect certification.',
        '350',
        'Basic Cloud Knowledge',
        '10 weeks',
        'I004'
    ),
    (
        'CS13',
        'Introduction to Cyber Security',
        'Cyber Security',
        'Beginner',
        'Learn basic cyber security principles.',
        '200',
        'None',
        '6 weeks',
        'I005'
    ),
    (
        'CS14',
        'Ethical Hacking and Penetration Testing',
        'Cyber Security',
        'Advanced',
        'Advanced course on ethical hacking techniques.',
        '300',
        'Introduction to Cyber Security',
        '8 weeks',
        'I005'
    );

-- Insert CourseContents
INSERT INTO
    CourseContents (CourseID, Name, Description)
VALUES
    (
        'CS01',
        'Week 1: Python Basics',
        'Learn variables, data types, and simple operations in Python.'
    ),
    (
        'CS01',
        'Week 2: Control Structures',
        'Learn conditionals and loops in Python.'
    );

-- Insert Exams
INSERT INTO
    Exams (CourseID, Name, Difficulty)
VALUES
    ('CS01', 'Midterm Exam', 'Intermediate'),
    ('CS02', 'Final Exam', 'Advanced');

-- Insert Questions
INSERT INTO
    Questions (
        ExamID,
        QuestionName,
        OptionA,
        OptionB,
        OptionC,
        OptionD,
        Score,
        Difficulty,
        CorrectOption
    )
VALUES
    (
        1,
        'What is Python?',
        'A snake',
        'A programming language',
        'A car',
        'A food',
        '10',
        'Easy',
        'B'
    ),
    (
        2,
        'What is Big O notation?',
        'A measure of speed',
        'A type of algorithm',
        'A programming language',
        'None',
        '10',
        'Medium',
        'A'
    );

-- Insert CourseRegistrations
INSERT INTO
    CourseRegistration (
        StudentID,
        CourseID,
        DateOfRegistration,
        StatusOfCompletion,
        EndDate
    )
VALUES
    (
        'S002',
        'CS01',
        '2025-01-15 09:30:00',
        'Completed',
        '2025-02-20 15:45:00'
    ),
    (
        'S002',
        'CS02',
        '2025-02-22 14:20:00',
        'In Progress',
        NULL
    ),
    (
        'S003',
        'CS01',
        '2025-01-10 11:15:00',
        'Completed',
        '2025-02-12 16:30:00'
    ),
    (
        'S003',
        'CS03',
        '2025-02-15 10:00:00',
        'In Progress',
        NULL
    ),
    (
        'S004',
        'CS02',
        '2025-01-20 13:45:00',
        'In Progress',
        NULL
    ),
    (
        'S004',
        'CS03',
        '2025-02-01 09:00:00',
        'In Progress',
        NULL
    ),
    (
        'S005',
        'CS01',
        '2025-01-05 10:30:00',
        'In Progress',
        NULL
    );

-- Insert Responses
INSERT INTO
    Responses (StudentID, QuestionID, Response, Score)
VALUES
    ('S002', 1, 'A programming language', 10),
    ('S003', 2, 'A measure of speed', 0);

-- Insert Discussions
INSERT INTO
    Discussions (CourseID, StudentID, Discussion, TIMESTAMP)
VALUES
    (
        'CS01',
        'S002',
        'Excited about this course!',
        '2025-01-16 14:30:00'
    ),
    (
        'CS01',
        'S003',
        'Looking forward to learning Python.',
        '2025-01-16 15:20:00'
    );

-- Insert Announcements
INSERT INTO
    Announcements (CourseID, Announcement)
VALUES
    (
        'CS01',
        'Welcome to Introduction to Programming. Please review the syllabus.'
    ),
    (
        'CS01',
        'Assignment deadline extended for Week 2.'
    );