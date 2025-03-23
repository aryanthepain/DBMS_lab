exam_portal/
├── assets/
│   ├── default.png              // Default image used for student/admin photo
│   └── hero.png                 // Home background photo
├── css/
│   └── style.css                // Custom CSS
├── include/
│   ├── dbh.inc.php              // Database connection file (PDO)
│   └── displayAlert.inc.php     // Function to display alerts
├── pages/                       // All PHP pages
│   ├── admin_dashboard.php      // Admin dashboard with admin details and Manage Exam link
│   ├── admin_login.php          // Admin login page
│   ├── dashboard.php            // Student dashboard with details and feature links
│   ├── booking.php              // Student: Book exam & add transaction details
│   ├── process_booking.php      // Process exam booking and fee transaction
│   ├── exam_portal.php          // Student: Page before exam starts (photo upload)
│   ├── process_exam_start.php   // Process starting exam (upload photo, store booking/exam IDs)
│   ├── questions.php            // Student: Display one exam question at a time with countdown timer
│   ├── process_exam_question.php// Process each exam question response, update end_time when finished
│   ├── process_quit_exam.php    // Process when student quits exam midway (save state/resume later)
│   ├── evaluation.php           // Student: Display exam evaluation, results, admin feedback, exam details, and analysis metrics
│   ├── schedule.php             // Student: Schedule/reschedule exam slot (active exams only)
│   ├── process_schedule.php     // Process scheduling/rescheduling exam slot
│   ├── manage_exam.php          // Admin: Manage exam (create exam, add questions, manage feedback, add admins, manage time slots, exam analysis)
│   ├── process_exam.php         // Process exam submission (if applicable)
│   ├── login.php                // Student login page
│   ├── logout.php               // Logout page
│   ├── navbar.php               // Shared navigation bar
│   ├── register.php             // Student registration page
│   └── index.php                // Landing page (feature list, home)
├── sql/                         // Contains SQL scripts
│   ├── create.sql               // SQL commands to create the database
│   ├── insert.sql               // SQL commands to insert dummy data
│   └── queries.sql              // Some additional queries
├── structure.pgsql              // PostgreSQL (or SQL) script for database structure
├── README.md                    // Project README file with instructions and feature list
└── index.php                    // Top-level landing page that reroutes to pages/index.php
