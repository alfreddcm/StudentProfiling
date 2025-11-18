<?php
require_once 'php/auth/check_auth.php';
$page_title = 'Student List';
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
                        <h2>Student List</h2>
                        <p class="text-muted">Manage student records</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">All Students</h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal" onclick="resetForm()">
                                    <i class="bi bi-plus-circle"></i> Add Student
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search students...">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th>Student Number</th>
                                        <th>Name</th>
                                        <th>Course/Year/Section</th>
                                        <th>Status</th>
                                        <th>Risk Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="studentForm">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" id="student_id" name="student_id">
                    
                    <h6 class="text-primary mb-3">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="student_number" class="form-label">Student Number</label>
                            <input type="text" class="form-control" id="student_number" name="student_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                    </div>
                    
                    <h6 class="text-primary mb-3 mt-4">Guardian Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="guardian_name" class="form-label">Guardian Name</label>
                            <input type="text" class="form-control" id="guardian_name" name="guardian_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="guardian_contact" class="form-label">Guardian Contact</label>
                            <input type="text" class="form-control" id="guardian_contact" name="guardian_contact" required>
                        </div>
                    </div>
                    
                    <h6 class="text-primary mb-3 mt-4">Academic Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_year_section" class="form-label">Course/Year/Section</label>
                            <select class="form-select" id="course_year_section" name="course_year_section" required>
                                <option value="">Select Course</option>
                                <option value="BSIT 1A">BSIT 1A</option>
                                <option value="BSIT 1B">BSIT 1B</option>
                                <option value="BSIT 2A">BSIT 2A</option>
                                <option value="BSIT 2B">BSIT 2B</option>
                                <option value="BSIT 3A">BSIT 3A</option>
                                <option value="BSIT 3B">BSIT 3B</option>
                                <option value="BSIT 4A">BSIT 4A</option>
                                <option value="BSIT 4B">BSIT 4B</option>
                                <option value="BSCS 1A">BSCS 1A</option>
                                <option value="BSCS 1B">BSCS 1B</option>
                                <option value="BSCS 2A">BSCS 2A</option>
                                <option value="BSCS 3A">BSCS 3A</option>
                                <option value="BSCS 4A">BSCS 4A</option>
                                <option value="BSBA 1A">BSBA 1A</option>
                                <option value="BSBA 2A">BSBA 2A</option>
                                <option value="BSBA 3A">BSBA 3A</option>
                                <option value="BSBA 3C">BSBA 3C</option>
                                <option value="BSBA 4A">BSBA 4A</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="enrollment_status" class="form-label">Enrollment Status</label>
                            <select class="form-select" id="enrollment_status" name="enrollment_status" required>
                                <option value="Enrolled">Enrolled</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Graduated">Graduated</option>
                                <option value="Dropped">Dropped</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="academic_risk_level" class="form-label">Academic Risk Level</label>
                        <select class="form-select" id="academic_risk_level" name="academic_risk_level" required>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/main.js"></script>
<script src="js/students.js"></script>

</body>
</html>
