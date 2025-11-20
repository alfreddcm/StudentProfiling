<?php
require 'vendor/autoload.php';
require_once 'php/db/connection.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$section = isset($_GET['section']) ? mysqli_real_escape_string($conn, $_GET['section']) : '';

if ($section) {
    $query = "SELECT * FROM students WHERE course_year_section = '$section' ORDER BY last_name, first_name";
} else {
    $query = "SELECT * FROM students ORDER BY last_name, first_name";
}

$result = mysqli_query($conn, $query);

// Build table rows
$tableRows = '';
$count = 0;

if (mysqli_num_rows($result) > 0) {
    while ($student = mysqli_fetch_assoc($result)) {
        $count++;
        $fullName = $student['first_name'] . ' ' . 
                   ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . 
                   $student['last_name'];
        
        $tableRows .= '<tr>
            <td style="text-align: center;">' . $count . '</td>
            <td>' . htmlspecialchars($student['student_number']) . '</td>
            <td>' . htmlspecialchars($fullName) . '</td>
            <td>' . htmlspecialchars($student['course_year_section']) . '</td>
            <td style="text-align: center;">' . htmlspecialchars($student['enrollment_status']) . '</td>
            <td style="text-align: center;">' . htmlspecialchars($student['gender']) . '</td>
        </tr>';
    }
} else {
    $tableRows = '<tr><td colspan="6" style="text-align: center;">No students found</td></tr>';
}

$sectionTitle = $section ? "Section: " . htmlspecialchars($section) : "All Students";

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
    </style>
</head>
<body>
    <div class="header">
        <h1>STUDENT LIST</h1>
        <p>Student Profiling System</p>
        <p class="section-title">' . $sectionTitle . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No.</th>
                <th width="15%">Student Number</th>
                <th width="30%">Full Name</th>
                <th width="25%">Course/Year/Section</th>
                <th width="15%" style="text-align: center;">Status</th>
                <th width="10%" style="text-align: center;">Gender</th>
            </tr>
        </thead>
        <tbody>
            ' . $tableRows . '
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Total Students: ' . $count . '</strong></p>
        <p>Generated on: ' . date('F d, Y h:i A') . '</p>
    </div>
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

