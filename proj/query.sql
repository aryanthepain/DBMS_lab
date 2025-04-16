-- ATP - query.sql
-- Sample queries for fetching data for the lab8_elearn website.
USE lab8_elearn;

-- List all courses with instructor names
SELECT
    c.ID,
    c.Name,
    c.Description,
    i.Name AS InstructorName
FROM
    Courses c
    LEFT JOIN Instructors i ON c.InstructorID = i.ID;

-- Get course contents for a specific course (example: CS01)
SELECT
    *
FROM
    CourseContents
WHERE
    CourseID = 'CS01';

-- Get student registration details for a course (example: CS01)
SELECT
    cr.StudentID,
    s.Name,
    cr.DateOfRegistration,
    cr.StatusOfCompletion,
    cr.EndDate
FROM
    CourseRegistration cr
    LEFT JOIN Students s ON cr.StudentID = s.ID
WHERE
    cr.CourseID = 'CS01';

-- Get all announcements for a course (example: CS01)
SELECT
    *
FROM
    Announcements
WHERE
    CourseID = 'CS01'
ORDER BY
    ID DESC;

-- Get exam questions for an exam of a course (example: for CS01)
SELECT
    q.*
FROM
    Questions q
    JOIN Exams e ON q.ExamID = e.ID
WHERE
    e.CourseID = 'CS01';

-- Get discussion posts for a course (example: CS01)
SELECT
    d.Discussion,
    s.Name AS StudentName,
    d.TimeStamp
FROM
    Discussions d
    JOIN Students s ON d.StudentID = s.ID
WHERE
    d.CourseID = 'CS01'
ORDER BY
    d.TimeStamp DESC;