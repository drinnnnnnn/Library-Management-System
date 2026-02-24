<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
header('Content-Type: application/json');

$student_id = $_SESSION['student_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? null;

$email = null;
if ($student_id) {
    $stmt = $conn->prepare("SELECT email FROM users WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
}

echo json_encode([
  'student_id' => $student_id,
  'name' => $user_name,
  'email' => $email
]);
?>