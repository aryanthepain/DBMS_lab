exam_portal/
├── css/
│   └── style.css           // Custom CSS
├── include/
│   └── dbh.inc.php         // Database connection file
├── pages/                  // All pages are here
│   ├── index.php           // Landing page
│   ├── navbar.php          // Shared navigation bar
│   ├── register.php        // Student registration page
│   ├── login.php           // Student login page
│   ├── dashboard.php       // Student dashboard
│   ├── admin_login.php     // Admin login page
│   ├── admin_dashboard.php // Admin dashboard (admin-only view)
│   ├── exam_registration.php  // Exam registration page (admin)
│   ├── create_timeslot.php // Admin: Create a time slot
│   ├── process_create_timeslot.php // Processing time slot creation
│   ├── book_exam_slot.php  // Admin: Book an exam on a time slot
│   ├── process_book_exam_slot.php // Process exam-slot booking
│   ├── booking.php         // Student: Booking & Fees page
│   ├── process_booking.php // Process booking for exam
│   ├── schedule.php        // Student: Schedule/reschedule exam page
│   ├── process_schedule.php// Process scheduling
│   ├── exam_portal.php     // Page before exam starts (with webcam/timer)
│   ├── evaluation.php      // Display exam evaluation
│   └── dashboard_analysis.php // Overall strengths/weakness dashboard
