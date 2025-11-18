COLLEGE STUDENT PROFILING SYSTEM
=================================

A complete web-based student profiling system with authentication, AJAX CRUD operations, and PDF generation capabilities.


SYSTEM REQUIREMENTS
-------------------
- XAMPP (Apache + MySQL + PHP 7.4 or higher)
- Web Browser (Chrome, Firefox, Edge)
- Internet connection (for CDN resources: Bootstrap 5, jQuery, SweetAlert2)


INSTALLATION INSTRUCTIONS
--------------------------

1. INSTALL XAMPP
   - Download XAMPP from https://www.apachefriends.org/
   - Install XAMPP to C:\xampp (default location)
   - Start Apache and MySQL services from XAMPP Control Panel

2. SETUP PROJECT FILES
   - The project is already installed at: C:\xampp\htdocs\StudentProfiling
   - All files and folders are in place

3. CREATE DATABASE
   - Open phpMyAdmin in your browser: http://localhost/phpmyadmin
   - Click "New" to create a new database
   - Database name: student_profiling
   - Collation: utf8mb4_general_ci
   - Click "Create"

4. IMPORT DATABASE TABLES
   - Select the "student_profiling" database
   - Click "Import" tab
   - Click "Choose File" and select: C:\xampp\htdocs\StudentProfiling\database.sql
   - Click "Go" to import
   - This will create:
        * users table (with default admin account)
        * students table (empty, ready for data)

5. VERIFY DATABASE CONNECTION
   - Open: C:\xampp\htdocs\StudentProfiling\php\db\connection.php
   - Default settings:
        Host: localhost
        Username: root
        Password: (empty)
        Database: student_profiling
   - Modify if your MySQL settings are different

6. ACCESS THE SYSTEM
   - Open browser and go to: http://localhost/StudentProfiling/login.php
   - Default login credentials:
        Username: admin
        Password: password


SYSTEM FEATURES
---------------

1. AUTHENTICATION SYSTEM
   - Secure login with password hashing (bcrypt)
   - Session-based authentication
   - Login required for all pages except login.php
   - Logout functionality

2. DASHBOARD
   - Total students count
   - Active students count
   - Recently added students (today)
   - Students by course distribution with percentages
   - Quick action buttons

3. STUDENT MANAGEMENT (AJAX CRUD)
   - Add student (modal form)
   - Edit student (modal form)
   - Delete student (with SweetAlert confirmation)
   - Live search filter
   - Student fields:
        * Student Number
        * Full Name (First, Middle, Last)
        * Gender (Male/Female/Other)
        * Birthdate
        * Address
        * Contact Number
        * Guardian Name
        * Guardian Contact Number
        * Course/Year/Section
        * Enrollment Status (Active/Inactive/Graduated/Dropped)
        * Academic Risk Level (Low/Medium/High)
   - Newly added students highlighted in green
   - Status badges with colors
   - Risk level badges with colors
   - All operations without page reload

4. MY ACCOUNT
   - Update profile information
   - Change password (with current password verification)
   - View account details

5. PDF GENERATION
   - Student Profile PDF (individual student details)
   - Student List PDF (all students in table format)
   - Automatic download on click

6. RESPONSIVE DESIGN
   - Bootstrap 5 layout
   - Collapsible sidebar navigation
   - Mobile-friendly interface
   - Modern card-based UI

7. VALIDATION & ERROR HANDLING
   - Client-side validation (JavaScript)
   - Server-side validation (PHP)
   - SweetAlert2 for all notifications
   - JSON-based error responses


FILE STRUCTURE
--------------

StudentProfiling/
├── css/
│   └── style.css
├── js/
│   ├── login.js
│   ├── main.js
│   ├── students.js
│   └── account.js
├── php/
│   ├── auth/
│   │   ├── check_auth.php
│   │   ├── check_guest.php
│   │   ├── login_process.php
│   │   └── logout.php
│   ├── controllers/
│   │   ├── student_controller.php
│   │   └── account_controller.php
│   └── db/
│       └── connection.php
├── pdf/
│   ├── fpdf.php
│   ├── generate_student_profile.php
│   └── generate_student_list.php
├── login.php
├── dashboard.php
├── students.php
├── account.php
├── header.php
├── sidebar.php
├── navbar.php
├── database.sql
└── README.txt


USAGE GUIDE
-----------

1. LOGIN
   - Go to http://localhost/StudentProfiling/login.php
   - Enter credentials (admin / password)
   - Click Login button

2. ADD STUDENT
   - Navigate to "Student List" from sidebar
   - Click "Add Student" button
   - Fill in all required fields in the modal form
   - Click "Save Student"
   - Success toast notification will appear

3. EDIT STUDENT
   - Click the blue pencil icon on any student row
   - Modal form opens with existing data
   - Update fields as needed
   - Click "Update Student"

4. DELETE STUDENT
   - Click the red trash icon on any student row
   - Confirm deletion in SweetAlert dialog
   - Student is removed with success notification

5. SEARCH STUDENTS
   - Use search box above the student table
   - Type any keyword (name, student number, course, etc.)
   - Results update automatically as you type

6. DOWNLOAD PDF
   - For individual profile: Click PDF icon on student row
   - For student list: Click "Download PDF Report" on dashboard
   - PDF downloads automatically to your Downloads folder

7. UPDATE ACCOUNT
   - Navigate to "My Account" from sidebar
   - Update your full name or change password
   - Click respective save buttons

8. LOGOUT
   - Click "Logout" from sidebar or navbar dropdown
   - Session ends and redirects to login


TECHNOLOGY STACK
----------------

Frontend:
- HTML5
- CSS3 (Custom + Bootstrap 5.3.0)
- JavaScript (ES6+)
- jQuery 3.7.0
- SweetAlert2 11.x
- Bootstrap Icons 1.10.0

Backend:
- PHP 7.4+ (Procedural, no OOP)
- MySQL 8.0+
- Sessions for authentication
- FPDF for PDF generation

Architecture:
- MVC-inspired structure
- AJAX-based CRUD operations
- JSON API responses
- RESTful design patterns


SECURITY FEATURES
-----------------

1. Password hashing using password_hash() and password_verify()
2. SQL injection prevention using mysqli_real_escape_string()
3. Session-based authentication on all protected pages
4. Input validation (client-side and server-side)
5. Prepared statements pattern ready for implementation


EXTRA FEATURES IMPLEMENTED
---------------------------

1. Live Search Filter
   - Real-time AJAX search across all student fields
   - Instant results without page reload

2. Highlight Newly Added Students
   - Students added today are highlighted in green
   - "New" badge displayed next to student number

3. Status and Risk Level Badges
   - Color-coded badges for enrollment status
   - Color-coded badges for academic risk level
   - Visual indicators for quick assessment


TROUBLESHOOTING
---------------

Problem: Cannot access http://localhost/StudentProfiling/
Solution: 
- Ensure Apache is running in XAMPP Control Panel
- Check if project folder exists at C:\xampp\htdocs\StudentProfiling

Problem: Database connection error
Solution:
- Ensure MySQL is running in XAMPP Control Panel
- Verify database name is "student_profiling"
- Check credentials in php/db/connection.php

Problem: Login not working
Solution:
- Verify database was imported correctly
- Default password is "password" (hashed in database)
- Check browser console for JavaScript errors

Problem: PDF not downloading
Solution:
- Ensure php/pdf/fpdf.php exists
- Check browser popup blocker settings
- Verify student data exists in database

Problem: AJAX not working
Solution:
- Check browser console for errors
- Ensure jQuery and other libraries are loading (check internet connection)
- Verify controller file paths are correct


BROWSER COMPATIBILITY
--------------------
- Google Chrome (Recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari
- Opera


DEFAULT CREDENTIALS
-------------------
Username: admin
Password: password


SUPPORT & MAINTENANCE
---------------------

For system modifications:
1. Database: Edit database.sql and re-import
2. Styling: Modify css/style.css
3. JavaScript: Update respective .js files in js/ folder
4. PHP Logic: Edit controller files in php/controllers/
5. Pages: Modify .php files in root directory


VERSION INFORMATION
-------------------
Version: 1.0.0
Release Date: November 2025
PHP Version: 7.4+
MySQL Version: 8.0+
Bootstrap Version: 5.3.0


NOTES
-----
- All code is production-ready with no comments
- System uses CDN for external libraries
- Responsive design works on all screen sizes
- All forms use validation before submission
- PDF generation uses FPDF library (included)
- Session timeout: Default PHP session settings


QUICK START CHECKLIST
----------------------
[ ] XAMPP installed and running
[ ] Database "student_profiling" created
[ ] database.sql imported successfully
[ ] Apache and MySQL services started
[ ] Accessed http://localhost/StudentProfiling/login.php
[ ] Logged in with admin/password
[ ] Added first student record
[ ] Tested search functionality
[ ] Generated PDF report
[ ] Updated account password


END OF DOCUMENTATION
