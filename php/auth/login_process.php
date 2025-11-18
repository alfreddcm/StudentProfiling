<?php
session_start();
require_once '../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit();
}

$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit();
}

$user = mysqli_fetch_assoc($result);

if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit();
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['role'] = $user['role'];

echo json_encode(['success' => true, 'message' => 'Login successful']);
?>
