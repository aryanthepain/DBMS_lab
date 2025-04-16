-- File: insert.sql
-- Author: ATP
-- ==========================================
-- Insert Dummy Data into Students Table
-- ==========================================
INSERT INTO
    `Students` (
        `ID`,
        `Name`,
        `PhoneNo`,
        `Email`,
        `Username`,
        `Password`
    )
VALUES
    (
        'S200',
        'Sam Wilson',
        '9998887777',
        'sam.wilson@example.com',
        'samw',
        'pass200'
    ),
    (
        'S201',
        'Tina Fey',
        '8887776666',
        'tina.fey@example.com',
        'tinaf',
        'pass201'
    ),
    (
        'S202',
        'Bruce Banner',
        '7776665555',
        'bruce.banner@example.com',
        'bruceb',
        'pass202'
    ),
    (
        'S203',
        'Natasha Romanoff',
        '6665554444',
        'natasha.romanoff@example.com',
        'natashar',
        'pass203'
    ),
    (
        'S204',
        'Steve Rogers',
        '5554443333',
        'steve.rogers@example.com',
        'stever',
        'pass204'
    );

-- ==========================================
-- Insert Dummy Data into Instructors Table
-- ==========================================
INSERT INTO
    `Instructors` (
        `ID`,
        `Name`,
        `PhoneNo`,
        `Email`,
        `Username`,
        `Password`
    )
VALUES
    (
        'I200',
        'Dr. Stephen Strange',
        '1112223333',
        'stephen.strange@example.com',
        'strange',
        'magic'
    ),
    (
        'I201',
        'Prof. Wanda Maximoff',
        '2223334444',
        'wanda.maximoff@example.com',
        'wanda',
        'hex'
    ),
    (
        'I202',
        'Dr. Pepper Potts',
        '3334445555',
        'pepper.potts@example.com',
        'pepper',
        'potts'
    ),
    (
        'I203',
        'Prof. Bruce Wayne',
        '4445556666',
        'bruce.wayne@example.com',
        'wayne',
        'batman'
    ),
    (
        'I204',
        'Dr. Clark Kent',
        '5556667777',
        'clark.kent@example.com',
        'clark',
        'superman'
    );

-- ==========================================
-- Insert Dummy Data into Courses Table
-- ==========================================
INSERT INTO
    `Courses` (
        `ID`,
        `Name`,
        `Category`,
        `Difficulty`,
        `Description`,
        `Price`,
        `PreRequsites`,
        `EstimatedTimeOfCompletion`,
        `InstructorID`
    )
VALUES
    (
        'CR100',
        'Foundations of Web Design',
        'Web Development',
        'Beginner',
        'Learn the fundamentals of designing engaging websites.',
        '100',
        'None',
        '4 weeks',
        'I200'
    ),
    (
        'CR101',
        'Advanced JavaScript',
        'Web Development',
        'Advanced',
        'Master complex JavaScript concepts and asynchronous programming.',
        '150',
        'Foundations of Web Design',
        '6 weeks',
        'I200'
    ),
    (
        'CR102',
        'Database Systems',
        'Database',
        'Intermediate',
        'Explore relational databases, normalization, and SQL queries.',
        '120',
        'None',
        '5 weeks',
        'I201'
    ),
    (
        'CR103',
        'Cyber Security Essentials',
        'Cyber Security',
        'Beginner',
        'Introduction to cyber security principles and best practices.',
        '130',
        'None',
        '4 weeks',
        'I201'
    ),
    (
        'CR104',
        'Cloud Computing',
        'IT & Networking',
        'Intermediate',
        'Understand cloud services, architecture, and deployment models.',
        '160',
        'Database Systems',
        '6 weeks',
        'I202'
    );

-- ==========================================
-- Insert Dummy Data into CourseContents Table
-- ==========================================
INSERT INTO
    `CourseContents` (`CourseID`, `Name`, `Description`)
VALUES
    (
        'CR100',
        'Week 1: HTML Basics',
        'Understand HTML structure, tags, and creating basic web pages.'
    ),
    (
        'CR100',
        'Week 2: CSS Fundamentals',
        'Learn the basics of CSS styling, selectors, and layout techniques.'
    ),
    (
        'CR101',
        'Week 1: ES6 Features',
        'Explore new features introduced in ES6 including let, const, and arrow functions.'
    ),
    (
        'CR101',
        'Week 2: Asynchronous JavaScript',
        'Deep dive into promises, async/await, and handling asynchronous operations.'
    );

-- ==========================================
-- Insert Dummy Data into Announcements Table
-- ==========================================
INSERT INTO
    `Announcements` (`CourseID`, `Announcement`)
VALUES
    (
        'CR100',
        'Welcome to Foundations of Web Design. Check your email for the introductory schedule.'
    ),
    (
        'CR101',
        'Advanced JavaScript project proposal deadline extended by 2 days.'
    ),
    (
        'CR102',
        'Database Systems: New labs available, please review before next class.'
    ),
    (
        'CR103',
        'Cyber Security Essentials: A new webinar on threat analysis is scheduled for next week.'
    );

-- ==========================================
-- Insert Dummy Data into CourseRegistration Table
-- ==========================================
INSERT INTO
    `CourseRegistration` (
        `StudentID`,
        `CourseID`,
        `DateOfRegistration`,
        `StatusOfCompletion`,
        `EndDate`
    )
VALUES
    (
        'S200',
        'CR100',
        '2025-03-01 10:00:00',
        'completed',
        '2025-04-01 15:00:00'
    ),
    (
        'S201',
        'CR101',
        '2025-03-02 11:30:00',
        'in Progress',
        NULL
    ),
    (
        'S202',
        'CR102',
        '2025-03-03 12:00:00',
        'completed',
        '2025-04-05 16:00:00'
    ),
    (
        'S203',
        'CR103',
        '2025-03-04 09:45:00',
        'in Progress',
        NULL
    ),
    (
        'S204',
        'CR104',
        '2025-03-05 14:15:00',
        'completed',
        '2025-04-10 18:00:00'
    );

-- ==========================================
-- Insert Dummy Data into Discussions Table
-- ==========================================
INSERT INTO
    `Discussions` (
        `CourseID`,
        `StudentID`,
        `Discussion`,
        `TimeStamp`
    )
VALUES
    (
        'CR100',
        'S200',
        'I am excited to learn HTML and CSS, starting this week!',
        '2025-03-01 10:15:00'
    ),
    (
        'CR101',
        'S201',
        'Can someone help me understand the nuances of asynchronous JavaScript?',
        '2025-03-02 11:45:00'
    ),
    (
        'CR102',
        'S202',
        'The lab sessions in Database Systems are really helpful.',
        '2025-03-03 12:30:00'
    ),
    (
        'CR103',
        'S203',
        'Looking forward to the upcoming webinar on cyber security threats.',
        '2025-03-04 10:00:00'
    ),
    (
        'CR104',
        'S204',
        'Cloud Computing course material is challenging but exciting.',
        '2025-03-05 14:30:00'
    );