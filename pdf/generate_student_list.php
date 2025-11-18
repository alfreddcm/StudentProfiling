<?php
require_once 'fpdf.php';
require_once '../php/db/connection.php';

$query = "SELECT * FROM students ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'STUDENT LIST', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'College Student Profiling System', 0, 1, 'C');
        $this->Ln(5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(3);

        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(30, 8, 'Student No.', 1, 0, 'C', true);
        $this->Cell(60, 8, 'Full Name', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Course/Yr/Sec', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Status', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Risk Level', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' - Generated on ' . date('F d, Y'), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

$count = 0;
while ($student = mysqli_fetch_assoc($result)) {
    $fullName = $student['first_name'] . ' ' . ($student['middle_name'] ? substr($student['middle_name'], 0, 1) . '. ' : '') . $student['last_name'];
    
    $pdf->Cell(30, 7, $student['student_number'], 1, 0, 'C');
    $pdf->Cell(60, 7, $fullName, 1, 0, 'L');
    $pdf->Cell(40, 7, $student['course_year_section'], 1, 0, 'C');
    $pdf->Cell(30, 7, $student['enrollment_status'], 1, 0, 'C');
    $pdf->Cell(30, 7, $student['academic_risk_level'], 1, 1, 'C');
    
    $count++;
}

if ($count == 0) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'No students found', 0, 1, 'C');
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, 'Total Students: ' . $count, 0, 1);

$pdf->Output('D', 'Student_List_' . date('Y-m-d') . '.pdf');
?>
