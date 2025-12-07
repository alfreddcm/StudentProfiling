<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/php/db/connection.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Get section filter from URL parameter
$section = isset($_GET['section']) ? mysqli_real_escape_string($conn, $_GET['section']) : '';

// Initialize variables
$groupedStudents = [];
$totalCount = 0;
$tableRows = '';
$count = 0;

// Build query based on section filter
if ($section) {
    $parts = explode(' ', $section);
    $sectionPart = array_pop($parts);
    $yearSection = explode('-', $sectionPart);
    $yearLevel = $yearSection[0];
    $sectionName = isset($yearSection[1]) ? $yearSection[1] : '';
    $course = implode(' ', $parts);
    
    if ($sectionName) {
        $query = "SELECT * FROM students WHERE course = '$course' AND year_level = '$yearLevel' AND section = '$sectionName' ORDER BY last_name, first_name";
    } else {
        $query = "SELECT * FROM students WHERE course = '$course' AND year_level = '$yearLevel' ORDER BY last_name, first_name";
    }
    
    $result = mysqli_query($conn, $query);
    
    // Build table rows for PDF (single section)
    $count = 0;
    
    if (mysqli_num_rows($result) > 0) {
        while ($student = mysqli_fetch_assoc($result)) {
            $count++;
            $fullName = $student['first_name'] . ' ' . 
                       ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . 
                       $student['last_name'];
            
            $courseYearSection = $student['course'] . ' ' . $student['year_level'] . '-' . $student['section'];
            
            $tableRows .= '<tr>
                <td style="text-align: center; font-size: 10px;">' . $count . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['student_number']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($fullName) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['gender']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['birthdate']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['address']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['contact_number']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['guardian_name']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['guardian_contact']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($courseYearSection) . '</td>
                <td style="text-align: center; font-size: 10px;">' . htmlspecialchars($student['enrollment_status']) . '</td>
            </tr>';
        }
    } else {
        $tableRows = '<tr><td colspan="11" style="text-align: center;">No students found</td></tr>';
    }
    
    $sectionTitle = "Section: " . htmlspecialchars($section);
    
} else {
    // Export all students grouped by course/year/section
    $query = "SELECT * FROM students ORDER BY course, year_level, section, last_name, first_name";
    $result = mysqli_query($conn, $query);
    
    // Check for query errors
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($result) > 0) {
        while ($student = mysqli_fetch_assoc($result)) {
            $key = $student['course'] . ' ' . $student['year_level'] . '-' . $student['section'];
            if (!isset($groupedStudents[$key])) {
                $groupedStudents[$key] = [];
            }
            $groupedStudents[$key][] = $student;
            $totalCount++;
        }
    }
    
    $sectionTitle = "Student List - All Sections";
}

// HTML content for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            color: #333;
        }
        
        .section-header {
            background-color: #f0f0f0;
            padding: 10px;
            margin-top: 25px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
            font-size: 14px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        
        th {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .section-count {
            text-align: right;
            font-size: 11px;
            color: #666;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>STUDENT LIST</h1>
        <p>Student Profiling System</p>
        <p class="section-title">' . $sectionTitle . '</p>
    </div>';

if ($section) {
    // Single section export
    $html .= '
    <table>
        <thead>
            <tr>
                <th style="text-align: center; font-size: 10px;">No.</th>
                <th style="font-size: 10px;">Student Number</th>
                <th style="font-size: 10px;">Full Name</th>
                <th style="font-size: 10px;">Gender</th>
                <th style="font-size: 10px;">Birthdate</th>
                <th style="font-size: 10px;">Address</th>
                <th style="font-size: 10px;">Contact</th>
                <th style="font-size: 10px;">Guardian</th>
                <th style="font-size: 10px;">Guardian Contact</th>
                <th style="font-size: 10px;">Course/Year/Section</th>
                <th style="text-align: center; font-size: 10px;">Status</th>
            </tr>
        </thead>
        <tbody>
            ' . $tableRows . '
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Total Students: ' . $count . '</strong></p>
        <p>Generated on: ' . date('F d, Y h:i A') . '</p>
    </div>';
} else {
    // All sections export - grouped by course/year/section
    if (empty($groupedStudents)) {
        // No students found
        $html .= '
        <table>
            <thead>
                <tr>
                    <th style="text-align: center; font-size: 10px;">No.</th>
                    <th style="font-size: 10px;">Student Number</th>
                    <th style="font-size: 10px;">Full Name</th>
                    <th style="font-size: 10px;">Gender</th>
                    <th style="font-size: 10px;">Birthdate</th>
                    <th style="font-size: 10px;">Address</th>
                    <th style="font-size: 10px;">Contact</th>
                    <th style="font-size: 10px;">Guardian</th>
                    <th style="font-size: 10px;">Guardian Contact</th>
                    <th style="font-size: 10px;">Course/Year/Section</th>
                    <th style="text-align: center; font-size: 10px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="11" style="text-align: center;">No students found</td></tr>
            </tbody>
        </table>
        
        <div class="footer">
            <p><strong>Total Students: 0</strong></p>
            <p>Generated on: ' . date('F d, Y h:i A') . '</p>
        </div>';
    } else {
        // Export grouped students
        $isFirst = true;
        foreach ($groupedStudents as $sectionKey => $students) {
        if (!$isFirst) {
            $html .= '<div class="page-break"></div>';
        }
        $isFirst = false;
        
        $html .= '
        <div class="section-header">' . htmlspecialchars($sectionKey) . '</div>
        <div class="section-count">Students: ' . count($students) . '</div>
        
        <table>
            <thead>
                <tr>
                    <th style="text-align: center; font-size: 10px;">No.</th>
                    <th style="font-size: 10px;">Student Number</th>
                    <th style="font-size: 10px;">Full Name</th>
                    <th style="font-size: 10px;">Gender</th>
                    <th style="font-size: 10px;">Birthdate</th>
                    <th style="font-size: 10px;">Address</th>
                    <th style="font-size: 10px;">Contact</th>
                    <th style="font-size: 10px;">Guardian</th>
                    <th style="font-size: 10px;">Guardian Contact</th>
                    <th style="font-size: 10px;">Course/Year/Section</th>
                    <th style="text-align: center; font-size: 10px;">Status</th>
                </tr>
            </thead>
            <tbody>';
        
        $count = 0;
        foreach ($students as $student) {
            $count++;
            $fullName = $student['first_name'] . ' ' . 
                       ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . 
                       $student['last_name'];
            
            $courseYearSection = $student['course'] . ' ' . $student['year_level'] . '-' . $student['section'];
            
            $html .= '<tr>
                <td style="text-align: center; font-size: 10px;">' . $count . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['student_number']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($fullName) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['gender']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['birthdate']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['address']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['contact_number']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['guardian_name']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($student['guardian_contact']) . '</td>
                <td style="font-size: 10px;">' . htmlspecialchars($courseYearSection) . '</td>
                <td style="text-align: center; font-size: 10px;">' . htmlspecialchars($student['enrollment_status']) . '</td>
            </tr>';
        }
        
        $html .= '
            </tbody>
        </table>';
    }
    
    $html .= '
    <div class="footer">
        <p><strong>Total Students: ' . $totalCount . '</strong></p>
        <p><strong>Total Sections: ' . count($groupedStudents) . '</strong></p>
        <p>Generated on: ' . date('F d, Y h:i A') . '</p>
    </div>';
    }
}

$html .= '
</body>
</html>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = $section ? 'Student_List_' . str_replace(' ', '_', $section) . '_' . date('Y-m-d') . '.pdf' 
                      : 'Student_List_All_' . date('Y-m-d') . '.pdf';

$dompdf->stream($filename, array("Attachment" => true));
?>

