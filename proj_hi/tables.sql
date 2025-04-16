CREATE DATABASE `DA215_Project2`;

-- Students table
CREATE TABLE Students (
    ID VARCHAR(10) PRIMARY KEY,
    Name TEXT,
    PhoneNo TEXT,
    Email TEXT,
    Username TEXT UNIQUE,
    Password TEXT
);

-- Instructors table
CREATE TABLE Instructors (
    ID VARCHAR(10) PRIMARY KEY,
    Name TEXT,
    PhoneNo TEXT,
    Email TEXT,
    Username TEXT UNIQUE,
    Password TEXT
);

-- Courses table
CREATE TABLE Courses (
    ID VARCHAR(10) PRIMARY KEY,
    Name TEXT,
    Category TEXT,
    Difficulty TEXT,
    Description TEXT,
    Price TEXT,
    PreRequsites TEXT,
    EstimatedTimeOfCompletion TEXT,
    InstructorID VARCHAR(10),
    FOREIGN KEY (InstructorID) REFERENCES Instructors(ID)
);

-- CourseContents table
CREATE TABLE CourseContents (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    CourseID VARCHAR(10),
    Name TEXT,
    Description TEXT,
    FOREIGN KEY (CourseID) REFERENCES Courses(ID)
);

-- Exams table
CREATE TABLE Exams (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    CourseID VARCHAR(10),
    Name TEXT,
    Difficulty TEXT,
    FOREIGN KEY (CourseID) REFERENCES Courses(ID)
);

-- Questions table
CREATE TABLE Questions (
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
    FOREIGN KEY (ExamID) REFERENCES Exams(ID)
);

-- Discussions table
CREATE TABLE Discussions (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    CourseID VARCHAR(10),
    StudentID VARCHAR(10),
    Discussion TEXT,
    TimeStamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CourseID) REFERENCES Courses(ID),
    FOREIGN KEY (StudentID) REFERENCES Students(ID)
);

-- Announcements table
CREATE TABLE Announcements (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    CourseID VARCHAR(10),
    Announcement TEXT,
    FOREIGN KEY (CourseID) REFERENCES Courses(ID)
);

-- CourseRegistration table
CREATE TABLE CourseRegistration (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID VARCHAR(10),
    CourseID VARCHAR(10),
    DateOfRegistration TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    StatusOfCompletion TEXT,
    EndDate TIMESTAMP NULL,
    FOREIGN KEY (StudentID) REFERENCES Students(ID),
    FOREIGN KEY (CourseID) REFERENCES Courses(ID)
);


CREATE TABLE Respones (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID VARCHAR(10),
    QuestionID INT,
    Response TEXT,
    Score INT,
    FOREIGN KEY (StudentID) REFERENCES Students(ID),
    FOREIGN KEY (QuestionID) REFERENCES Questions(ID)
);



-- Sample Data

INSERT INTO `Students` (`ID`, `Name`, `PhoneNo`, `Email`, `Username`, `Password`) VALUES
('S001', 'John Doe', '1234567890', 'johndoe@example.com', 'user1', 'pass1'),
('S002', 'Jane Smith', '0987654321', 'janesmith@example.com', 'user2', 'pass2'),
('S003', 'Michael Brown', '1122334455', 'michaelbrown@example.com', 'user3', 'pass3'),
('S004', 'Emily White', '2233445566', 'emilywhite@example.com', 'user4', 'pass4'),
('S005', 'David Green', '3344556677', 'davidgreen@example.com', 'user5', 'pass5');


INSERT INTO `Instructors` (`ID`, `Name`, `PhoneNo`, `Email`, `Username`, `Password`) VALUES
('I001', 'Dr. Alice Johnson', '5551234567', 'alice.johnson@example.com', 'user1', 'pass1'),
('I002', 'Prof. Robert Davis', '5552345678', 'robert.davis@example.com', 'user2', 'pass2'),
('I003', 'Dr. Susan Clark', '5553456789', 'susan.clark@example.com', 'user3', 'pass3'),
('I004', 'Prof. William Harris', '5554567890', 'william.harris@example.com', 'user4', 'pass4'),
('I005', 'Dr. Mary Martinez', '5555678901', 'mary.martinez@example.com', 'user5', 'pass5');


INSERT INTO `Courses` (`ID`, `Name`, `Category`, `Difficulty`, `Description`, `Price`, `PreRequsites`, `EstimatedTimeOfCompletion`, `InstructorID`) VALUES
('CS01', 'Introduction to Programming', 'Computer Science', 'Beginner', 'This course introduces the fundamentals of programming using Python. Students will learn about variables, control structures, functions, and basic algorithms. By the end of the course, students will be able to write basic Python programs and understand programming concepts. It is ideal for those new to programming.', '100', 'None', '4 weeks', 'I001'),
('CS02', 'Data Structures and Algorithms', 'Computer Science', 'Intermediate', 'In this course, students will dive deep into essential data structures such as arrays, linked lists, stacks, and queues, as well as algorithms like sorting and searching. The course also covers time and space complexity analysis. It is perfect for those looking to build strong problem-solving skills for coding interviews.', '150', 'Introduction to Programming', '6 weeks', 'I001'),
('CS03', 'Web Development with HTML, CSS, and JavaScript', 'Web Development', 'Intermediate', 'This course covers the essentials of web development, focusing on building dynamic websites using HTML, CSS, and JavaScript. Students will learn how to create responsive websites, handle user interactions, and make websites interactive. By the end of the course, students will have a fully functional website to showcase.', '200', 'Basic Programming Knowledge', '8 weeks', 'I001'),
('DB04', 'Database Design and Management', 'Database Systems', 'Advanced', 'This course provides an in-depth understanding of relational databases and the techniques used to design and manage them. Students will learn about database normalization, SQL queries, data modeling, and database optimization. It’s perfect for those looking to advance their skills in database administration and development.', '250', 'Data Structures and Algorithms', '10 weeks', 'I001');



INSERT INTO `Courses` (`ID`, `Name`, `Category`, `Difficulty`, `Description`, `Price`, `PreRequsites`, `EstimatedTimeOfCompletion`, `InstructorID`) VALUES
('ML05', 'Introduction to Machine Learning', 'Machine Learning', 'Beginner', 'This course introduces students to the basics of machine learning, including supervised and unsupervised learning. Students will learn how to use popular machine learning libraries like Scikit-learn and how to build simple models for classification and regression tasks. It is ideal for those starting in the field of data science and machine learning.', '200', 'None', '6 weeks', 'I002'),
('DL06', 'Deep Learning with TensorFlow', 'Machine Learning', 'Advanced', 'Deep learning is a subfield of machine learning focused on neural networks with many layers. In this course, students will learn how to use TensorFlow to build deep learning models for tasks like image recognition, natural language processing, and more. This course is designed for students who already have some background in machine learning.', '350', 'Introduction to Machine Learning', '10 weeks', 'I002'),
('PY07', 'Data Analysis with Python', 'Data Science', 'Intermediate', 'In this course, students will learn how to analyze and manipulate data using Python, with a focus on using libraries such as Pandas, NumPy, and Matplotlib. The course covers data cleaning, exploratory data analysis, and creating visualizations. It’s perfect for those who want to become proficient in data analysis and statistical analysis using Python.', '150', 'Introduction to Programming', '8 weeks', 'I002'),
('NLP8', 'Introduction to Natural Language Processing', 'Machine Learning', 'Intermediate', 'Natural Language Processing (NLP) is a field of AI that focuses on the interaction between computers and human language. In this course, students will learn basic NLP tasks such as tokenization, part-of-speech tagging, and sentiment analysis using Python. This course is ideal for students interested in AI applications involving text data.', '180', 'Introduction to Machine Learning', '8 weeks', 'I002');

INSERT INTO `Courses` (`ID`, `Name`, `Category`, `Difficulty`, `Description`, `Price`, `PreRequsites`, `EstimatedTimeOfCompletion`, `InstructorID`) VALUES
('DS09', 'Introduction to Data Science', 'Data Science', 'Beginner', 'This course provides an overview of data science, covering topics such as data collection, cleaning, and analysis. Students will learn to use Python for data manipulation, as well as basic statistics and machine learning concepts. It is ideal for students looking to transition into the data science field.', '120', 'None', '6 weeks', 'I003'),
('PY10', 'Advanced Data Visualization with Python', 'Data Science', 'Advanced', 'In this course, students will master the art of data visualization using Python libraries like Matplotlib, Seaborn, and Plotly. The course covers advanced visualization techniques and how to interpret and present complex datasets clearly. It is perfect for those wanting to build their data storytelling skills.', '220', 'Introduction to Data Science', '8 weeks', 'I003');

INSERT INTO `Courses` (`ID`, `Name`, `Category`, `Difficulty`, `Description`, `Price`, `PreRequsites`, `EstimatedTimeOfCompletion`, `InstructorID`) VALUES
('DB11', 'Introduction to Cloud Computing', 'Cloud Computing', 'Beginner', 'This course introduces the fundamentals of cloud computing, including the various types of cloud services like IaaS, PaaS, and SaaS. Students will learn how cloud platforms like AWS, Google Cloud, and Microsoft Azure work, and how to deploy applications in the cloud. It is ideal for those new to cloud technologies.', '180', 'None', '5 weeks', 'I004'),
('DB12', 'AWS Certified Solutions Architect', 'Cloud Computing', 'Advanced', 'In this course, students will prepare for the AWS Certified Solutions Architect certification. They will learn about AWS services, cloud architecture best practices, and how to design scalable, reliable, and cost-efficient systems. It’s perfect for those who want to advance their cloud computing expertise.', '350', 'Basic Cloud Knowledge', '10 weeks', 'I004');

INSERT INTO `Courses` (`ID`, `Name`, `Category`, `Difficulty`, `Description`, `Price`, `PreRequsites`, `EstimatedTimeOfCompletion`, `InstructorID`) VALUES
('CS13', 'Introduction to Cyber Security', 'Cyber Security', 'Beginner', 'This course introduces the fundamentals of cyber security, including network security, cryptography, and ethical hacking. Students will learn how to protect digital assets from common cyber threats. It is ideal for those looking to start a career in the field of cyber security.', '200', 'None', '6 weeks', 'I005'),
('CS14', 'Ethical Hacking and Penetration Testing', 'Cyber Security', 'Advanced', 'In this advanced course, students will learn about ethical hacking techniques and penetration testing tools. The course covers topics like network penetration, vulnerability assessment, and exploitation techniques, with hands-on labs. It is designed for students who want to pursue careers as ethical hackers or penetration testers.', '300', 'Introduction to Cyber Security', '8 weeks', 'I005');

-- Course Content for CS01: Introduction to Programming
INSERT INTO `CourseContents` (`CourseID`, `Name`, `Description`) VALUES
('CS01', 'Week 1: Introduction to Python and Programming Concepts', 'Get started with Python programming basics. Learn about variables, data types, and simple operations.\n\nLecture Slides: https://example.com/cs01/week1/slides\nLecture Video: https://example.com/cs01/week1/video\nPractice Exercises: https://example.com/cs01/week1/exercises'),
('CS01', 'Week 2: Control Structures', 'Learn about conditional statements (if-else) and loops (for, while) in Python. Apply these concepts in simple programs.\n\nLecture Slides: https://example.com/cs01/week2/slides\nLecture Video: https://example.com/cs01/week2/video\nCoding Assignment: https://example.com/cs01/week2/assignment'),
('CS01', 'Week 3: Functions and Modules', 'Understand how to create and use functions. Learn about Python modules and how to import them in your programs.\n\nLecture Slides: https://example.com/cs01/week3/slides\nLecture Video: https://example.com/cs01/week3/video\nLab Session: https://example.com/cs01/week3/lab'),
('CS01', 'Week 4: Lists and Dictionaries', 'Explore Python data structures including lists, tuples, and dictionaries. Learn how to manipulate and process collections of data.\n\nLecture Slides: https://example.com/cs01/week4/slides\nLecture Video: https://example.com/cs01/week4/video\nPractice Problems: https://example.com/cs01/week4/problems'),
('CS01', 'Week 5: File Handling', 'Learn how to read from and write to files in Python. Understand different file modes and error handling.\n\nLecture Slides: https://example.com/cs01/week5/slides\nLecture Video: https://example.com/cs01/week5/video\nFile Processing Assignment: https://example.com/cs01/week5/assignment'),
('CS01', 'Week 6: Exception Handling', 'Understand how to handle errors and exceptions in Python programs. Learn to write robust code that can recover from errors.\n\nLecture Slides: https://example.com/cs01/week6/slides\nLecture Video: https://example.com/cs01/week6/video\nDebugging Exercise: https://example.com/cs01/week6/debugging'),
('CS01', 'Week 7: Introduction to Object-Oriented Programming', 'Learn the basics of object-oriented programming in Python. Understand classes, objects, and inheritance.\n\nLecture Slides: https://example.com/cs01/week7/slides\nLecture Video: https://example.com/cs01/week7/video\nOOP Mini-Project: https://example.com/cs01/week7/project'),
('CS01', 'Week 8: Final Project and Review', 'Apply all concepts learned throughout the course in a comprehensive final project. Review key concepts for the final exam.\n\nProject Guidelines: https://example.com/cs01/week8/project-guide\nReview Materials: https://example.com/cs01/week8/review\nFinal Exam Preparation: https://example.com/cs01/week8/exam-prep');

-- Course Content for CS02: Data Structures and Algorithms
INSERT INTO `CourseContents` (`CourseID`, `Name`, `Description`) VALUES
('CS02', 'Week 1: Algorithm Analysis and Big O Notation', 'Introduction to algorithm analysis and computational complexity. Learn how to measure efficiency using Big O notation.\n\nLecture Slides: https://example.com/cs02/week1/slides\nLecture Video: https://example.com/cs02/week1/video\nComplexity Analysis Problems: https://example.com/cs02/week1/problems'),
('CS02', 'Week 2: Arrays and Linked Lists', 'Understand the implementation and operations of arrays and linked lists. Compare their performance characteristics.\n\nLecture Slides: https://example.com/cs02/week2/slides\nLecture Video: https://example.com/cs02/week2/video\nData Structure Implementation Exercise: https://example.com/cs02/week2/exercise'),
('CS02', 'Week 3: Stacks and Queues', 'Learn about stack and queue data structures, their implementations, and applications in solving problems.\n\nLecture Slides: https://example.com/cs02/week3/slides\nLecture Video: https://example.com/cs02/week3/video\nStack/Queue Programming Assignment: https://example.com/cs02/week3/assignment'),
('CS02', 'Week 4: Trees and Binary Search Trees', 'Explore tree data structures with a focus on binary search trees. Learn traversal methods and search operations.\n\nLecture Slides: https://example.com/cs02/week4/slides\nLecture Video: https://example.com/cs02/week4/video\nTree Implementation Lab: https://example.com/cs02/week4/lab'),
('CS02', 'Week 5: Heaps and Priority Queues', 'Understand heap data structures and priority queues. Learn about heap operations and heapsort algorithm.\n\nLecture Slides: https://example.com/cs02/week5/slides\nLecture Video: https://example.com/cs02/week5/video\nHeap Operations Assignment: https://example.com/cs02/week5/assignment'),
('CS02', 'Week 6: Sorting Algorithms', 'Study various sorting algorithms including bubble sort, insertion sort, merge sort, and quicksort. Analyze their time complexity.\n\nLecture Slides: https://example.com/cs02/week6/slides\nLecture Video: https://example.com/cs02/week6/video\nSorting Algorithm Comparison: https://example.com/cs02/week6/comparison'),
('CS02', 'Week 7: Graph Algorithms', 'Introduction to graph representations and algorithms such as BFS, DFS, shortest path, and minimum spanning tree.\n\nLecture Slides: https://example.com/cs02/week7/slides\nLecture Video: https://example.com/cs02/week7/video\nGraph Algorithm Implementation: https://example.com/cs02/week7/implementation'),
('CS02', 'Week 8: Dynamic Programming', 'Learn about dynamic programming techniques to solve optimization problems. Study classic examples and applications.\n\nLecture Slides: https://example.com/cs02/week8/slides\nLecture Video: https://example.com/cs02/week8/video\nDynamic Programming Problems: https://example.com/cs02/week8/problems');

-- Course Content for CS03: Web Development with HTML, CSS, and JavaScript
INSERT INTO `CourseContents` (`CourseID`, `Name`, `Description`) VALUES
('CS03', 'Week 1: Introduction to HTML', 'Learn the fundamentals of HTML and document structure. Understand tags, elements, and basic page layout.\n\nLecture Slides: https://example.com/cs03/week1/slides\nLecture Video: https://example.com/cs03/week1/video\nHTML Practice Exercise: https://example.com/cs03/week1/exercise'),
('CS03', 'Week 2: CSS Fundamentals', 'Introduction to Cascading Style Sheets. Learn about selectors, properties, and styling HTML elements.\n\nLecture Slides: https://example.com/cs03/week2/slides\nLecture Video: https://example.com/cs03/week2/video\nStyling Assignment: https://example.com/cs03/week2/assignment'),
('CS03', 'Week 3: CSS Layout and Responsive Design', 'Learn about CSS box model, flexbox, and grid layouts. Understand media queries and responsive web design principles.\n\nLecture Slides: https://example.com/cs03/week3/slides\nLecture Video: https://example.com/cs03/week3/video\nResponsive Layout Project: https://example.com/cs03/week3/project'),
('CS03', 'Week 4: Introduction to JavaScript', 'Fundamentals of JavaScript programming. Learn about variables, data types, functions, and basic DOM manipulation.\n\nLecture Slides: https://example.com/cs03/week4/slides\nLecture Video: https://example.com/cs03/week4/video\nJavaScript Basics Exercise: https://example.com/cs03/week4/exercise'),
('CS03', 'Week 5: JavaScript Events and DOM Manipulation', 'Learn how to handle user events and dynamically modify webpage content using JavaScript.\n\nLecture Slides: https://example.com/cs03/week5/slides\nLecture Video: https://example.com/cs03/week5/video\nInteractive Webpage Assignment: https://example.com/cs03/week5/assignment'),
('CS03', 'Week 6: Forms and Input Validation', 'Understand how to create and process forms. Learn client-side validation techniques using JavaScript.\n\nLecture Slides: https://example.com/cs03/week6/slides\nLecture Video: https://example.com/cs03/week6/video\nForm Validation Project: https://example.com/cs03/week6/project'),
('CS03', 'Week 7: Introduction to APIs and AJAX', 'Learn how to make asynchronous requests and interact with APIs using JavaScript fetch API and AJAX.\n\nLecture Slides: https://example.com/cs03/week7/slides\nLecture Video: https://example.com/cs03/week7/video\nAPI Integration Exercise: https://example.com/cs03/week7/exercise'),
('CS03', 'Week 8: Final Project: Building a Complete Website', 'Apply all concepts learned to create a fully functional, responsive website with interactive elements.\n\nProject Guidelines: https://example.com/cs03/week8/guidelines\nResource Materials: https://example.com/cs03/week8/resources\nProject Submission: https://example.com/cs03/week8/submission');

-- Announcements for CS01: Introduction to Programming
INSERT INTO `Announcements` (`CourseID`, `Announcement`) VALUES
('CS01', 'Welcome to Introduction to Programming! I am Dr. Alice Johnson and I will be your instructor for this course. Please take some time to review the syllabus and course schedule. Our first live session will be this Thursday at 3:00 PM. Looking forward to meeting you all!'),
('CS01', 'IMPORTANT: Assignment deadline extended for Week 2 Control Structures assignment. Many of you requested additional time to complete the exercises on loops. The new deadline is Sunday at 11:59 PM. No further extensions will be granted.'),
('CS01', 'Common Question Addressed: I have noticed several students struggling with the concept of list comprehensions in Python. I have uploaded an additional video tutorial under Week 4 materials that walks through more examples. Please check it out if you are having difficulties!'),
('CS01', 'Reminder: Mid-term project proposals are due next Monday. Please submit your one-page proposal describing what your Python program will do and which concepts from the course you plan to implement. Feel free to email me if you need guidance on selecting a topic.'),
('CS01', 'Office hours update: I will be holding extended office hours before the final project deadline. You can find me in the virtual classroom on Monday and Tuesday from 4-6 PM. Please come with specific questions to make the most of our time together.');

-- Announcements for CS02: Data Structures and Algorithms
INSERT INTO `Announcements` (`CourseID`, `Announcement`) VALUES
('CS02', 'Welcome to Data Structures and Algorithms! This course builds on your programming foundation and will challenge you to think more deeply about code efficiency and organization. Please review the prerequisite materials if you need a refresher on Python basics.'),
('CS02', 'REMINDER: The programming assignment on linked lists implementation is due this Friday. Remember to include proper documentation and test cases as specified in the requirements. This assignment counts for 15% of your final grade.'),
('CS02', 'I have noticed some confusion in the discussion forum regarding Big O notation. I have created a supplementary document with additional examples that has been added to Week 1 materials. Please review this before attempting the Week 2 exercises.'),
('CS02', 'Exciting news! Next week we will have a guest lecturer, Dr. James Morgan from Google, who will discuss how algorithms are applied in real-world search engines. Attendance is highly encouraged, and there will be a Q&A session afterwards.'),
('CS02', 'Several students have asked about recommended resources for additional practice with graph algorithms. I have updated the Week 7 resources with links to practice problems on LeetCode and HackerRank that are relevant to our course material.');

-- Announcements for CS03: Web Development with HTML, CSS, and JavaScript
INSERT INTO `Announcements` (`CourseID`, `Announcement`) VALUES
('CS03', 'Welcome to Web Development with HTML, CSS, and JavaScript! I am excited to guide you through building your first interactive websites. Be sure to set up your development environment following the instructions in the Week 1 materials.'),
('CS03', 'IMPORTANT: For our responsive design project in Week 3, please test your layouts on at least three different screen sizes (mobile, tablet, and desktop). A significant portion of the grade depends on how well your design adapts across devices.'),
('CS03', 'Clarification on JavaScript assignment: Several students have asked if jQuery is allowed for the DOM manipulation exercises. For this course, we want you to learn vanilla JavaScript first, so please do not use jQuery or other libraries for assignments until Week 7.'),
('CS03', 'Peer review assignments have been posted for the Week 5 interactive webpage project. Please review two of your classmates projects by next Monday and provide constructive feedback based on the rubric provided in the course materials.'),
('CS03', 'Final project guidelines have been updated with more detailed requirements and evaluation criteria. The most significant change is that your website must include at least one feature that uses data from an external API. See Week 8 materials for the full updated guidelines.');

-- Course Registrations for students
INSERT INTO `CourseRegistration` (`StudentID`, `CourseID`, `DateOfRegistration`, `StatusOfCompletion`, `EndDate`) VALUES
-- Jane Smith registrations
('S002', 'CS01', '2025-01-15 09:30:00', 'completed', '2025-02-20 15:45:00'),
('S002', 'CS02', '2025-02-22 14:20:00', 'in Progress', NULL),

-- Michael Brown registrations
('S003', 'CS01', '2025-01-10 11:15:00', 'completed', '2025-02-12 16:30:00'),
('S003', 'CS03', '2025-02-15 10:00:00', 'in Progress', NULL),

-- Emily White registrations
('S004', 'CS02', '2025-01-20 13:45:00', 'in Progress', NULL),
('S004', 'CS03', '2025-02-01 09:00:00', 'in Progress', NULL),

-- David Green registrations
('S005', 'CS01', '2025-01-05 10:30:00', 'in Progress', NULL);

-- Discussions for CS01: Introduction to Programming
INSERT INTO `Discussions` (`CourseID`, `StudentID`, `Discussion`, `TimeStamp`) VALUES
('CS01', 'S002', 'Hi everyone! Just started this course and I am excited to learn Python. Anyone else new to programming?', '2025-01-16 14:30:00'),
('CS01', 'S003', 'Welcome Jane! I am also new to programming. The first weeks content seems approachable so far.', '2025-01-16 15:20:00'),
('CS01', 'S005', 'I am finding the for loop exercises challenging. Can anyone explain the difference between range(5) and range(1,5)?', '2025-01-22 09:45:00'),
('CS01', 'S003', 'David, range(5) goes from 0 to 4, while range(1,5) goes from 1 to 4. Hope that helps!', '2025-01-22 10:30:00'),
('CS01', 'S002', 'Just completed the Week 3 assignment on functions. The recursive examples were particularly interesting. Has anyone started the challenge problems yet?', '2025-01-30 16:15:00'),
('CS01', 'S005', 'Thanks for the explanation on range(), Michael! I also had a question about the file handling exercise - are we supposed to handle exceptions in our code or is that covered later?', '2025-02-01 11:20:00'),
('CS01', 'S003', 'David, exception handling is covered in Week 6, but it is good practice to include it even now. Check out the Python docs on try/except blocks.', '2025-02-01 13:45:00');

-- Discussions for CS02: Data Structures and Algorithms
INSERT INTO `Discussions` (`CourseID`, `StudentID`, `Discussion`, `TimeStamp`) VALUES
('CS02', 'S002', 'The Big O notation lecture was eye-opening! I never realized how much efficiency matters in programming.', '2025-02-23 13:15:00'),
('CS02', 'S004', 'I am struggling to understand the difference between O(n log n) and O(n²). Could someone provide a real-world example?', '2025-02-25 10:40:00'),
('CS02', 'S002', 'Emily, think of O(n log n) like sorting a deck of cards using merge sort - you divide and conquer. O(n²) is like checking each card against every other card, much less efficient for large datasets.', '2025-02-25 12:30:00'),
('CS02', 'S004', 'Thanks Jane! That analogy really helps. Has anyone started implementing the linked list exercise yet?', '2025-02-26 15:20:00'),
('CS02', 'S002', 'I have started the linked list implementation. The tricky part is handling the edge cases like deleting the head node.', '2025-02-27 09:10:00'),
('CS02', 'S004', 'Is anyone else finding the tree traversal algorithms confusing? Especially the difference between in-order, pre-order, and post-order traversal.', '2025-03-10 14:25:00'),
('CS02', 'S002', 'Emily, I found this visualization helpful: pre-order (visit, left, right), in-order (left, visit, right), post-order (left, right, visit). Try drawing a small tree and trace through each algorithm step by step.', '2025-03-10 16:05:00');

-- Discussions for CS03: Web Development with HTML, CSS, and JavaScript
INSERT INTO `Discussions` (`CourseID`, `StudentID`, `Discussion`, `TimeStamp`) VALUES
('CS03', 'S003', 'Just starting this course after completing Python. Excited to build actual websites!', '2025-02-16 11:00:00'),
('CS03', 'S004', 'Has anyone figured out how to center a div properly? I am finding CSS positioning quite challenging.', '2025-02-05 13:30:00'),
('CS03', 'S003', 'Emily, try using display: flex with justify-content: center and align-items: center on the parent container. That has worked well for me!', '2025-02-05 14:15:00'),
('CS03', 'S004', 'Thanks Michael! That worked perfectly. I was trying to use margin: auto but it was not behaving as expected.', '2025-02-05 15:00:00'),
('CS03', 'S003', 'The responsive design project is challenging but fun. I am using CSS Grid for the layout and media queries for different screen sizes.', '2025-02-22 16:40:00'),
('CS03', 'S004', 'I am struggling with JavaScript event listeners. My click events work fine but I cannot get the form submission event to prevent the default action.', '2025-03-01 10:20:00'),
('CS03', 'S003', 'Emily, make sure you are using event.preventDefault() as the first line in your submission handler function. Also check that your listener is attached to the form element, not the submit button.', '2025-03-01 11:05:00');