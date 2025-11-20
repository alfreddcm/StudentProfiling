<?php
// Database migration script
// This will update the database structure from course_year_section to course, year_level, section

require_once 'php/db/connection.php';

// Check if migration is needed
$check_query = "SHOW COLUMNS FROM students LIKE 'course'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) == 0) {
    // Migration needed
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Database Update Required</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light'>
        <div class='container mt-5'>
            <div class='card shadow'>
                <div class='card-body text-center'>
                    <h3 class='mb-4'>Database Update Required</h3>
                    <p class='mb-4'>The system has been updated with a new database structure.<br>
                    Course, Year Level, and Section are now separate fields for better management.</p>
                    <p class='text-muted mb-4'>This process will migrate your existing data automatically.</p>
                    <form method='post' action='migrate_database.php'>
                        <button type='submit' name='migrate' class='btn btn-primary btn-lg'>
                            <i class='bi bi-arrow-repeat'></i> Update Database Now
                        </button>
                    </form>
                    <p class='text-muted mt-3 small'>This is a one-time process and cannot be undone.</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
    exit();
}
?>
