<?php
require_once 'php/db/connection.php';

$section = isset($_GET['section']) ? mysqli_real_escape_string($conn, $_GET['section']) : '';

if ($section) {
    $query = "SELECT * FROM students WHERE course_year_section = '$section' ORDER BY last_name, first_name";
} else {
    $query = "SELECT * FROM students ORDER BY last_name, first_name";
}

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Print</title>
    <style>
        @media print {
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
        
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
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0 10px 0;
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
        }
        
        th {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        
        .btn {
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 5px;
        }
        
        .btn:hover {
            background-color: #0b5ed7;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5c636a;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn" onclick="window.print()">Print / Save as PDF</button>
        <button class="btn btn-secondary" onclick="window.close()">Close</button>
    </div>
    
    <div class="header">
        <h1>STUDENT LIST</h1>
        <p>Student Profiling System</p>
        <?php if ($section): ?>
            <p class="section-title">Section: <?php echo htmlspecialchars($section); ?></p>
        <?php else: ?>
            <p class="section-title">All Students</p>
        <?php endif; ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No.</th>
                <th width="15%">Student Number</th>
                <th width="30%">Full Name</th>
                <th width="25%">Course/Year/Section</th>
                <th width="15%" class="text-center">Status</th>
                <th width="10%" class="text-center">Gender</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $count = 0;
            if (mysqli_num_rows($result) > 0):
                while ($student = mysqli_fetch_assoc($result)): 
                    $count++;
                    $fullName = $student['first_name'] . ' ' . 
                               ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . 
                               $student['last_name'];
            ?>
            <tr>
                <td class="text-center"><?php echo $count; ?></td>
                <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                <td><?php echo htmlspecialchars($fullName); ?></td>
                <td><?php echo htmlspecialchars($student['course_year_section']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($student['enrollment_status']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($student['gender']); ?></td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="6" class="text-center">No students found</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Total Students: <?php echo $count; ?></strong></p>
        <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
    </div>
</body>
</html>
