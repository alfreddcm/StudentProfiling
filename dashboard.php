<?php
require_once 'php/auth/check_auth.php';
require_once 'php/db/connection.php';

// Check if database migration is needed
include_once 'check_migration.php';

$page_title = 'Dashboard';

$total_students_query = "SELECT COUNT(*) as total FROM students";
$total_students_result = mysqli_query($conn, $total_students_query);
$total_students = mysqli_fetch_assoc($total_students_result)['total'];

$active_students_query = "SELECT COUNT(*) as total FROM students WHERE enrollment_status = 'Active'";
$active_students_result = mysqli_query($conn, $active_students_query);
$active_students = mysqli_fetch_assoc($active_students_result)['total'];

$today = date('Y-m-d');
$today_students_query = "SELECT COUNT(*) as total FROM students WHERE DATE(created_at) = '$today'";
$today_students_result = mysqli_query($conn, $today_students_query);
$today_students = mysqli_fetch_assoc($today_students_result)['total'];

$courses_query = "SELECT course, year_level, section, COUNT(*) as total FROM students GROUP BY course, year_level, section";
$courses_result = mysqli_query($conn, $courses_query);
$courses_data = [];
while ($row = mysqli_fetch_assoc($courses_result)) {
    $row['course_year_section'] = $row['course'] . ' ' . $row['year_level'] . '-' . $row['section'];
    $courses_data[] = $row;
}

include 'header.php';
?>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'navbar.php'; ?>
        
        <div class="content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h2>Dashboard</h2>
                        <p class="text-muted">Welcome to Student Profiling System</p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card card-stat bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Total Students</h6>
                                        <h2 class="card-title mb-0"><?php echo $total_students; ?></h2>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-stat bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Active Students</h6>
                                        <h2 class="card-title mb-0"><?php echo $active_students; ?></h2>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-person-check-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-stat bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Added Today</h6>
                                        <h2 class="card-title mb-0"><?php echo $today_students; ?></h2>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="bi bi-person-plus-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Students by Course</h5>
                            </div>
                            <div class="card-body">
                                <?php if (count($courses_data) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Course/Year/Section</th>
                                                    <th>Total Students</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($courses_data as $course): ?>
                                                    <?php $percentage = $total_students > 0 ? round(($course['total'] / $total_students) * 100, 1) : 0; ?>
                                                    <tr>
                                                        <td><strong><?php echo $course['course_year_section']; ?></strong></td>
                                                        <td><?php echo $course['total']; ?></td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                                                    <?php echo $percentage; ?>%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-4">No students enrolled yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="students.php" class="btn btn-primary">
                                        <i class="bi bi-person-plus"></i> Add New Student
                                    </a>
                                    <a href="students.php" class="btn btn-outline-primary">
                                        <i class="bi bi-list-ul"></i> View All Students
                                    </a>
                                    <a href="pdf/generate_student_list.php" target="_blank" class="btn btn-outline-secondary">
                                        <i class="bi bi-file-pdf"></i> Download PDF Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/main.js"></script>

</body>
</html>
