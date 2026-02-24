<?php
header('Content-Type: application/json');
$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$book_id = $_GET['book_id'] ?? '';
if (!$book_id) {
    echo json_encode(['error' => 'No book_id provided']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("s", $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    // If your image is stored as a filename, prepend the path
    if (!empty($row['book_image']) 
        && strpos($row['book_image'], 'http') !== 0 
        && strpos($row['book_image'], '/WebDev_Repository/uploads/') !== 0
    ) {
        $row['book_image'] = 'http://localhost/WebDev_Repository/uploads/' . $row['book_image'];
    }
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Book not found']);
}
$stmt->close();
$conn->close();
?>