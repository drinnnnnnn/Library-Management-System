<?php
header('Content-Type: application/json');
$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(['success' => false, 'error' => 'No student_id']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM student_notification WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();
?>