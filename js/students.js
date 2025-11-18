let studentsData = [];

$(document).ready(function() {
    loadStudents();

    $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val();
        if (searchTerm.length > 0) {
            searchStudents(searchTerm);
        } else {
            loadStudents();
        }
    });

    $('#studentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const studentId = $('#student_id').val();
        const action = studentId ? 'update' : 'create';

        if (!validateForm()) {
            return;
        }

        $.ajax({
            url: 'php/controllers/student_controller.php',
            type: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#studentModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    loadStudents();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });
});

function loadStudents() {
    $.ajax({
        url: 'php/controllers/student_controller.php',
        type: 'POST',
        data: { action: 'read' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                studentsData = response.data;
                displayStudents(response.data);
            }
        }
    });
}

function displayStudents(students) {
    const tbody = $('#studentTableBody');
    tbody.empty();

    if (students.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">No students found</td></tr>');
        return;
    }

    const today = new Date().toISOString().split('T')[0];

    students.forEach(student => {
        const fullName = `${student.first_name} ${student.middle_name || ''} ${student.last_name}`.trim();
        const createdDate = student.created_at.split(' ')[0];
        const isNew = createdDate === today;
        const rowClass = isNew ? 'table-success' : '';
        
        let statusBadge = 'badge bg-success';
        if (student.enrollment_status === 'Inactive') statusBadge = 'badge bg-secondary';
        if (student.enrollment_status === 'Dropped') statusBadge = 'badge bg-danger';
        if (student.enrollment_status === 'Graduated') statusBadge = 'badge bg-primary';
        
        let riskBadge = 'badge bg-success';
        if (student.academic_risk_level === 'Medium') riskBadge = 'badge bg-warning';
        if (student.academic_risk_level === 'High') riskBadge = 'badge bg-danger';

        const row = `
            <tr class="${rowClass}">
                <td>${student.student_number} ${isNew ? '<span class="badge bg-success">New</span>' : ''}</td>
                <td>${fullName}</td>
                <td>${student.course_year_section}</td>
                <td><span class="${statusBadge}">${student.enrollment_status}</span></td>
                <td><span class="${riskBadge}">${student.academic_risk_level}</span></td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editStudent(${student.id})" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteStudent(${student.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                    <a href="pdf/generate_student_profile.php?id=${student.id}" target="_blank" class="btn btn-sm btn-secondary" title="Download PDF">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function editStudent(id) {
    $.ajax({
        url: 'php/controllers/student_controller.php',
        type: 'POST',
        data: { action: 'get', student_id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const student = response.data;
                $('#student_id').val(student.id);
                $('#student_number').val(student.student_number);
                $('#first_name').val(student.first_name);
                $('#last_name').val(student.last_name);
                $('#middle_name').val(student.middle_name);
                $('#gender').val(student.gender);
                $('#birthdate').val(student.birthdate);
                $('#address').val(student.address);
                $('#contact_number').val(student.contact_number);
                $('#guardian_name').val(student.guardian_name);
                $('#guardian_contact').val(student.guardian_contact);
                $('#course_year_section').val(student.course_year_section);
                $('#enrollment_status').val(student.enrollment_status);
                $('#academic_risk_level').val(student.academic_risk_level);
                $('#modalTitle').text('Edit Student');
                $('#submitBtn').text('Update Student');
                $('#studentModal').modal('show');
            }
        }
    });
}

function deleteStudent(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'php/controllers/student_controller.php',
                type: 'POST',
                data: { action: 'delete', student_id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        loadStudents();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                }
            });
        }
    });
}

function searchStudents(searchTerm) {
    $.ajax({
        url: 'php/controllers/student_controller.php',
        type: 'POST',
        data: { action: 'search', search: searchTerm },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayStudents(response.data);
            }
        }
    });
}

function resetForm() {
    $('#studentForm')[0].reset();
    $('#student_id').val('');
    $('#modalTitle').text('Add Student');
    $('#submitBtn').text('Save Student');
}

function validateForm() {
    const studentNumber = $('#student_number').val().trim();
    const firstName = $('#first_name').val().trim();
    const lastName = $('#last_name').val().trim();
    const gender = $('#gender').val();
    const birthdate = $('#birthdate').val();
    const address = $('#address').val().trim();
    const contactNumber = $('#contact_number').val().trim();
    const guardianName = $('#guardian_name').val().trim();
    const guardianContact = $('#guardian_contact').val().trim();
    const course = $('#course_year_section').val();
    const enrollmentStatus = $('#enrollment_status').val();
    const riskLevel = $('#academic_risk_level').val();

    if (!studentNumber || !firstName || !lastName || !gender || !birthdate || !address || 
        !contactNumber || !guardianName || !guardianContact || !course || !enrollmentStatus || !riskLevel) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fill in all required fields'
        });
        return false;
    }

    return true;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
