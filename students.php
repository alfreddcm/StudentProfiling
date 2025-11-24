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

                <!-- Student Management Card -->
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
                        <!-- Search and Filter Controls -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search students...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="sectionFilter">
                                    <option value="">All Sections</option>
                                    <optgroup label="BSEd - Filipino">
                                        <option value="BSEd Filipino 1A">BSEd Filipino 1A</option>
                                        <option value="BSEd Filipino 2A">BSEd Filipino 2A</option>
                                        <option value="BSEd Filipino 3A">BSEd Filipino 3A</option>
                                        <option value="BSEd Filipino 4A">BSEd Filipino 4A</option>
                                    </optgroup>
                                    <optgroup label="BSEd - Mathematics">
                                        <option value="BSEd Mathematics 1A">BSEd Mathematics 1A</option>
                                        <option value="BSEd Mathematics 2A">BSEd Mathematics 2A</option>
                                        <option value="BSEd Mathematics 3A">BSEd Mathematics 3A</option>
                                        <option value="BSEd Mathematics 4A">BSEd Mathematics 4A</option>
                                    </optgroup>
                                    <optgroup label="BSIT - Automotive">
                                        <option value="BSIT Automotive 1A">BSIT Automotive 1A</option>
                                        <option value="BSIT Automotive 2A">BSIT Automotive 2A</option>
                                        <option value="BSIT Automotive 3A">BSIT Automotive 3A</option>
                                        <option value="BSIT Automotive 4A">BSIT Automotive 4A</option>
                                    </optgroup>
                                    <optgroup label="BSIT - Electrical">
                                        <option value="BSIT Electrical 1A">BSIT Electrical 1A</option>
                                        <option value="BSIT Electrical 2A">BSIT Electrical 2A</option>
                                        <option value="BSIT Electrical 3A">BSIT Electrical 3A</option>
                                        <option value="BSIT Electrical 4A">BSIT Electrical 4A</option>
                                    </optgroup>
                                    <optgroup label="BSIT - Electronics">
                                        <option value="BSIT Electronics 1A">BSIT Electronics 1A</option>
                                        <option value="BSIT Electronics 2A">BSIT Electronics 2A</option>
                                        <option value="BSIT Electronics 3A">BSIT Electronics 3A</option>
                                        <option value="BSIT Electronics 4A">BSIT Electronics 4A</option>
                                    </optgroup>
                                    <optgroup label="BSIT - Food Service">
                                        <option value="BSIT Food Service 1A">BSIT Food Service 1A</option>
                                        <option value="BSIT Food Service 2A">BSIT Food Service 2A</option>
                                        <option value="BSIT Food Service 3A">BSIT Food Service 3A</option>
                                        <option value="BSIT Food Service 4A">BSIT Food Service 4A</option>
                                    </optgroup>
                                    <optgroup label="BSCrim">
                                        <option value="BSCrim 1A">BSCrim 1A</option>
                                        <option value="BSCrim 2A">BSCrim 2A</option>
                                        <option value="BSCrim 3A">BSCrim 3A</option>
                                        <option value="BSCrim 4A">BSCrim 4A</option>
                                    </optgroup>
                                    <optgroup label="BTVTEd - Automotive">
                                        <option value="BTVTEd Automotive 1A">BTVTEd Automotive 1A</option>
                                        <option value="BTVTEd Automotive 2A">BTVTEd Automotive 2A</option>
                                        <option value="BTVTEd Automotive 3A">BTVTEd Automotive 3A</option>
                                        <option value="BTVTEd Automotive 4A">BTVTEd Automotive 4A</option>
                                    </optgroup>
                                    <optgroup label="BTVTEd - Food Service">
                                        <option value="BTVTEd Food Service 1A">BTVTEd Food Service 1A</option>
                                        <option value="BTVTEd Food Service 2A">BTVTEd Food Service 2A</option>
                                        <option value="BTVTEd Food Service 3A">BTVTEd Food Service 3A</option>
                                        <option value="BTVTEd Food Service 4A">BTVTEd Food Service 4A</option>
                                    </optgroup>
                                    <optgroup label="BSIT - Web Development">
                                        <option value="BSIT Web Development 1A">BSIT Web Development 1A</option>
                                        <option value="BSIT Web Development 2A">BSIT Web Development 2A</option>
                                        <option value="BSIT Web Development 3A">BSIT Web Development 3A</option>
                                        <option value="BSIT Web Development 4A">BSIT Web Development 4A</option>
                                    </optgroup>
                                    <optgroup label="BSIT - Networking">
                                        <option value="BSIT Networking 1A">BSIT Networking 1A</option>
                                        <option value="BSIT Networking 2A">BSIT Networking 2A</option>
                                        <option value="BSIT Networking 3A">BSIT Networking 3A</option>
                                        <option value="BSIT Networking 4A">BSIT Networking 4A</option>
                                    </optgroup>
                                    <optgroup label="BSHM">
                                        <option value="BSHM 1A">BSHM 1A</option>
                                        <option value="BSHM 2A">BSHM 2A</option>
                                        <option value="BSHM 3A">BSHM 3A</option>
                                        <option value="BSHM 4A">BSHM 4A</option>
                                    </optgroup>
                                    <optgroup label="BAT">
                                        <option value="BAT 1A">BAT 1A</option>
                                        <option value="BAT 2A">BAT 2A</option>
                                        <option value="BAT 3A">BAT 3A</option>
                                        <option value="BAT 4A">BAT 4A</option>
                                    </optgroup>
                                    <optgroup label="Technical - Automotive">
                                        <option value="Automotive Technology NC1">Automotive Technology NC1</option>
                                        <option value="Automotive Technology NC2">Automotive Technology NC2</option>
                                    </optgroup>
                                    <optgroup label="Technical - Electronics">
                                        <option value="Electronics Technology NC1">Electronics Technology NC1</option>
                                        <option value="Electronics Technology NC2">Electronics Technology NC2</option>
                                    </optgroup>
                                    <optgroup label="Technical - Electrical">
                                        <option value="Electrical Technology NC1">Electrical Technology NC1</option>
                                        <option value="Electrical Technology NC2">Electrical Technology NC2</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-5 text-end">
                                <button class="btn btn-success" onclick="exportToPDF()">
                                    <i class="bi bi-file-pdf"></i> Export to PDF
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Student Number</th>
                                        <th>Name</th>
                                        <th>Course/Year/Section</th>
                                        <th>Status</th>
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
            <form id="studentForm" enctype="multipart/form-data">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" id="student_id" name="student_id">
                    <input type="hidden" id="existing_photo" name="existing_photo">
                    
                    <h6 class="text-primary mb-3">Student Photo</h6>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="text-center">
                                <div class="photo-preview-container mb-3">
                                    <div id="photoPreview" class="photo-preview">
                                        <i class="fas fa-user fa-5x text-secondary"></i>
                                    </div>
                                </div>
                                <div class="btn-group mb-2" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('photo_file').click()">
                                        <i class="fas fa-upload"></i> Upload Photo
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="cameraBtn">
                                        <i class="fas fa-camera"></i> Take Photo
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="removePhotoBtn" style="display:none;">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                                <input type="file" id="photo_file" name="photo_file" accept="image/*" style="display:none;">
                                <input type="hidden" id="photo_data" name="photo_data">
                                <div id="cameraContainer" style="display:none;" class="mt-3">
                                    <video id="cameraStream" width="320" height="240" autoplay class="border rounded"></video>
                                    <canvas id="cameraCanvas" style="display:none;"></canvas>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-success btn-sm" id="captureBtn">
                                            <i class="fas fa-camera"></i> Capture
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="closeCameraBtn">
                                            <i class="fas fa-times"></i> Close Camera
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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
                            <input type="text" class="form-control" id="contact_number" name="contact_number" pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
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
                            <input type="text" class="form-control" id="guardian_contact" name="guardian_contact" pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>
                    
                    <h6 class="text-primary mb-3 mt-4">Academic Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="course" class="form-label">Course</label>
                            <select class="form-select" id="course" name="course" required>
                                <option value="">Select Course</option>
                                <optgroup label="Bachelor of Secondary Education">
                                    <option value="BSEd Filipino">BSEd - Filipino</option>
                                    <option value="BSEd Mathematics">BSEd - Mathematics</option>
                                </optgroup>
                                <optgroup label="Bachelor of Science in Industrial Technology">
                                    <option value="BSIT Automotive Technology">BSIT - Automotive Technology</option>
                                    <option value="BSIT Electrical Technology">BSIT - Electrical Technology</option>
                                    <option value="BSIT Electronics Technology">BSIT - Electronics Technology</option>
                                    <option value="BSIT Food Service Management">BSIT - Food Service Management</option>
                                </optgroup>
                                <optgroup label="Bachelor of Science in Criminology">
                                    <option value="BSCrim">BSCrim</option>
                                </optgroup>
                                <optgroup label="Bachelor of Technical-Vocational Teacher Education">
                                    <option value="BTVTEd Automotive Technology">BTVTEd - Automotive Technology</option>
                                    <option value="BTVTEd Food Service Management">BTVTEd - Food Service Management</option>
                                </optgroup>
                                <optgroup label="Bachelor of Science in Information Technology">
                                    <option value="BSIT Web and Mobile Development">BSIT - Web and Mobile Development</option>
                                    <option value="BSIT Networking and Security">BSIT - Networking and Security</option>
                                </optgroup>
                                <optgroup label="Bachelor of Science in Hospitality Management">
                                    <option value="BSHM">BSHM</option>
                                </optgroup>
                                <optgroup label="Bachelor of Agricultural Technology">
                                    <option value="BAT">BAT</option>
                                </optgroup>
                                <optgroup label="Technical Courses">
                                    <option value="Automotive Technology">Automotive Technology</option>
                                    <option value="Electronics Technology">Electronics Technology</option>
                                    <option value="Electrical Technology">Electrical Technology</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="year_level" class="form-label">Year Level</label>
                            <select class="form-select" id="year_level" name="year_level" required>
                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                                <option value="5">5th Year</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="section" class="form-label">Section</label>
                            <input type="text" class="form-control" id="section" name="section" placeholder="e.g., A, B, C" required>
                        </div>
                    </div>
                    <div class="row">
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
