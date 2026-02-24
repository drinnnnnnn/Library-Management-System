<?php
header('Content-Type: application/json');
$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(['success' => false]);
    exit;
}

// Use "i" if student_id is integer, "s" if string
$stmt = $conn->prepare("UPDATE student_notification SET is_read = 1 WHERE student_id = ?");
$stmt->bind_param("s", $student_id); // <-- use "s" for string
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();
?>