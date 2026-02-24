<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['student_id']) || !isset($data['book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
    exit;
}

$student_id = $data['student_id'];
$book_id = intval($data['book_id']);

$conn = new mysqli("localhost", "root", "", "library_db");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed.']);
    exit;
}

// Prevent duplicate favorites
$stmt = $conn->prepare("SELECT favorite_id FROM favorites WHERE student_id = ? AND book_id = ?");
$stmt->bind_param("si", $student_id, $book_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Book already in favorites.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO favorites (student_id, book_id) VALUES (?, ?)");
$stmt->bind_param("si", $student_id, $book_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add favorite.']);
}

$stmt->close();
$conn->close();
?>