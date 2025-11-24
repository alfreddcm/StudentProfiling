<?php
require_once 'php/db/connection.php';

// Execute database migration when form is submitted
if (isset($_POST['migrate'])) {
    // Start database transaction for safe migration
    mysqli_begin_transaction($conn);
    
    try {
        // Add new column structure (course, year_level, section)
        mysqli_query($conn, "ALTER TABLE students ADD COLUMN course VARCHAR(100) AFTER guardian_contact");
        mysqli_query($conn, "ALTER TABLE students ADD COLUMN year_level INT(11) AFTER course");
        mysqli_query($conn, "ALTER TABLE students ADD COLUMN section VARCHAR(50) AFTER year_level");
        
        // Migrate existing data from old format to new structure
        $students = mysqli_query($conn, "SELECT id, course_year_section FROM students");
        
        while ($student = mysqli_fetch_assoc($students)) {
            $courseYearSection = $student['course_year_section'];
            
            // Parse the old format (e.g., "BSIT Automotive 1A", "BSCrim 2A", etc.)
            $course = '';
            $year_level = 1;
            $section = 'A';
            
            // Try to extract year level and section
            if (preg_match('/(\d)([A-Z])$/', $courseYearSection, $matches)) {
                $year_level = $matches[1];
                $section = $matches[2];
                $course = trim(preg_replace('/\s*\d[A-Z]$/', '', $courseYearSection));
            } else if (preg_match('/NC(\d)$/', $courseYearSection, $matches)) {
                // Technical courses with NC1/NC2
                $year_level = $matches[1];
                $section = 'NC';
                $course = trim(preg_replace('/\s*NC\d$/', '', $courseYearSection));
            } else {
                // Fallback: use the whole string as course
                $course = $courseYearSection;
            }
            
            // Update the student record
            $update = "UPDATE students SET 
                      course = '" . mysqli_real_escape_string($conn, $course) . "',
                      year_level = " . $year_level . ",
                      section = '" . mysqli_real_escape_string($conn, $section) . "'
                      WHERE id = " . $student['id'];
            mysqli_query($conn, $update);
        }
        
        // Drop old column
        mysqli_query($conn, "ALTER TABLE students DROP COLUMN course_year_section");
        
        // Commit transaction
        mysqli_commit($conn);
        
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Migration Complete</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container mt-5'>
                <div class='card shadow'>
                    <div class='card-body text-center'>
                        <div class='text-success mb-3'>
                            <i class='bi bi-check-circle' style='font-size: 4rem;'></i>
                        </div>
                        <h3 class='mb-4'>Database Updated Successfully!</h3>
                        <p class='mb-4'>Your data has been migrated to the new structure.</p>
                        <a href='dashboard.php' class='btn btn-primary btn-lg'>
                            <i class='bi bi-house'></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>";
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Migration Failed</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container mt-5'>
                <div class='card shadow border-danger'>
                    <div class='card-body text-center'>
                        <div class='text-danger mb-3'>
                            <i class='bi bi-x-circle' style='font-size: 4rem;'></i>
                        </div>
                        <h3 class='mb-4'>Migration Failed</h3>
                        <p class='text-danger'>Error: " . $e->getMessage() . "</p>
                        <a href='dashboard.php' class='btn btn-secondary'>
                            <i class='bi bi-arrow-left'></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
} else {
    header('Location: dashboard.php');
    exit();
}
?>
