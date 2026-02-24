<?php
header('Content-Type: application/json');
$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$student_id = $_GET['student_id'] ?? null;
if (!$student_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT n.*, b.book_name, b.book_image
    FROM student_notification n
    LEFT JOIN books b ON n.book_id = b.book_id
    WHERE n.student_id = ?
    ORDER BY n.notified_at DESC
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
$conn->close();
?>