<?php
require_once 'fpdf.php';
require_once '../php/db/connection.php';

if (!isset($_GET['id'])) {
    die('Student ID is required');
}

$student_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM students WHERE id = '$student_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die('Student not found');
}

$student = mysqli_fetch_assoc($result);

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'STUDENT PROFILE', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'College Student Profiling System', 0, 1, 'C');
        $this->Ln(5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Personal Information', 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Student Number:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['student_number'], 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Full Name:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$fullName = $student['first_name'] . ' ' . ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . $student['last_name'];
$pdf->Cell(0, 8, $fullName, 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Gender:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['gender'], 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Birthdate:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, date('F d, Y', strtotime($student['birthdate'])), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Address:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 8, $student['address'], 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Contact Number:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['contact_number'], 0, 1);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Guardian Information', 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Guardian Name:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['guardian_name'], 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Guardian Contact:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['guardian_contact'], 0, 1);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Academic Information', 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Course/Year/Section:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['course_year_section'], 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Enrollment Status:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['enrollment_status'], 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Academic Risk Level:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $student['academic_risk_level'], 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Date Registered:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, date('F d, Y', strtotime($student['created_at'])), 0, 1);

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'Generated on: ' . date('F d, Y h:i A'), 0, 1);

$pdf->Output('D', 'Student_Profile_' . $student['student_number'] . '.pdf');
?>
