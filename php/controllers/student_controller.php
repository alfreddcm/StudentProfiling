<?php
require_once '../db/connection.php';
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$action = $_POST['action'] ?? '';

// Handle different CRUD operations based on action parameter
switch ($action) {
    case 'create':
        createStudent($conn);
        break;
    case 'read':
        readStudents($conn);
        break;
    case 'update':
        updateStudent($conn);
        break;
    case 'delete':
        deleteStudent($conn);
        break;
    case 'get':
        getStudent($conn);
        break;
    case 'search':
        searchStudents($conn);
        break;
    case 'get_sections':
        getSections($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Adding/inserting student info to database
function createStudent($conn) {
    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $guardian_name = mysqli_real_escape_string($conn, $_POST['guardian_name']);
    $guardian_contact = mysqli_real_escape_string($conn, $_POST['guardian_contact']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year_level = mysqli_real_escape_string($conn, $_POST['year_level']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $enrollment_status = mysqli_real_escape_string($conn, $_POST['enrollment_status']);

    if (empty($student_number) || empty($first_name) || empty($last_name) || empty($gender) || 
        empty($birthdate) || empty($address) || empty($contact_number) || empty($guardian_name) || 
        empty($guardian_contact) || empty($course) || empty($year_level) || empty($section) || empty($enrollment_status)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit();
    }

    $check_query = "SELECT * FROM students WHERE student_number = '$student_number'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Student number already exists']);
        exit();
    }

    $photo_path = null;
    if (!empty($_POST['photo_data'])) {
        $photo_data = $_POST['photo_data'];
        if (preg_match('/^data:image\/(\w+);base64,/', $photo_data, $type)) {
            $photo_data = substr($photo_data, strpos($photo_data, ',') + 1);
            $type = strtolower($type[1]);
            $photo_data = base64_decode($photo_data);
            
            if ($photo_data !== false) {
                $upload_dir = '../../uploads/students/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = 'student_' . time() . '_' . uniqid() . '.' . $type;
                $filepath = $upload_dir . $filename;
                
                if (file_put_contents($filepath, $photo_data)) {
                    $photo_path = 'uploads/students/' . $filename;
                }
            }
        }
    }

    $query = "INSERT INTO students (student_number, first_name, last_name, middle_name, gender, birthdate, 
              address, contact_number, guardian_name, guardian_contact, course, year_level, section, enrollment_status, 
              photo) VALUES ('$student_number', '$first_name', '$last_name', '$middle_name', '$gender', 
              '$birthdate', '$address', '$contact_number', '$guardian_name', '$guardian_contact', '$course', 
              '$year_level', '$section', '$enrollment_status', '$photo_path')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add student']);
    }
}

// Fetching student data
function readStudents($conn) {
    $query = "SELECT * FROM students ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $students]);
}

// Updating student information
function updateStudent($conn) {
    $id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $guardian_name = mysqli_real_escape_string($conn, $_POST['guardian_name']);
    $guardian_contact = mysqli_real_escape_string($conn, $_POST['guardian_contact']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year_level = mysqli_real_escape_string($conn, $_POST['year_level']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $enrollment_status = mysqli_real_escape_string($conn, $_POST['enrollment_status']);

    if (empty($id) || empty($student_number) || empty($first_name) || empty($last_name) || empty($gender) || 
        empty($birthdate) || empty($address) || empty($contact_number) || empty($guardian_name) || 
        empty($guardian_contact) || empty($course) || empty($year_level) || empty($section) || empty($enrollment_status)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit();
    }

    $check_query = "SELECT * FROM students WHERE student_number = '$student_number' AND id != '$id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Student number already exists']);
        exit();
    }

    $photo_path = mysqli_real_escape_string($conn, $_POST['existing_photo']);
    
    if (!empty($_POST['photo_data'])) {
        $photo_data = $_POST['photo_data'];
        if (preg_match('/^data:image\/(\w+);base64,/', $photo_data, $type)) {
            $photo_data = substr($photo_data, strpos($photo_data, ',') + 1);
            $type = strtolower($type[1]);
            $photo_data = base64_decode($photo_data);
            
            if ($photo_data !== false) {
                if (!empty($photo_path) && file_exists('../../' . $photo_path)) {
                    unlink('../../' . $photo_path);
                }
                
                $upload_dir = '../../uploads/students/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = 'student_' . time() . '_' . uniqid() . '.' . $type;
                $filepath = $upload_dir . $filename;
                
                if (file_put_contents($filepath, $photo_data)) {
                    $photo_path = 'uploads/students/' . $filename;
                }
            }
        }
    }

    $query = "UPDATE students SET 
              student_number = '$student_number',
              first_name = '$first_name',
              last_name = '$last_name',
              middle_name = '$middle_name',
              gender = '$gender',
              birthdate = '$birthdate',
              address = '$address',
              contact_number = '$contact_number',
              guardian_name = '$guardian_name',
              guardian_contact = '$guardian_contact',
              course = '$course',
              year_level = '$year_level',
              section = '$section',
              enrollment_status = '$enrollment_status',
              photo = '$photo_path'
              WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update student']);
    }
}

// Deleting student from database
function deleteStudent($conn) {
    $id = mysqli_real_escape_string($conn, $_POST['student_id']);
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Student ID is required']);
        exit();
    }

    $get_query = "SELECT photo FROM students WHERE id = '$id'";
    $get_result = mysqli_query($conn, $get_query);
    
    if (mysqli_num_rows($get_result) > 0) {
        $student = mysqli_fetch_assoc($get_result);
        if (!empty($student['photo']) && file_exists('../../' . $student['photo'])) {
            unlink('../../' . $student['photo']);
        }
    }

    $query = "DELETE FROM students WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete student']);
    }
}

// Getting single student by ID
function getStudent($conn) {
    $id = mysqli_real_escape_string($conn, $_POST['student_id']);
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Student ID is required']);
        exit();
    }

    $query = "SELECT * FROM students WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'data' => $student]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
}

// Searching students by various fields
function searchStudents($conn) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    
    $query = "SELECT * FROM students WHERE 
              student_number LIKE '%$search%' OR
              first_name LIKE '%$search%' OR
              last_name LIKE '%$search%' OR
              middle_name LIKE '%$search%' OR
              course LIKE '%$search%' OR
              section LIKE '%$search%' OR
              enrollment_status LIKE '%$search%'
              ORDER BY created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $students]);
}

// Getting distinct sections from students
function getSections($conn) {
    try {
        $query = "SELECT DISTINCT course, year_level, section 
                  FROM students 
                  ORDER BY course, year_level, section";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            $sections = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $sections[] = $row;
            }
            echo json_encode(['success' => true, 'sections' => $sections]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Query failed']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
