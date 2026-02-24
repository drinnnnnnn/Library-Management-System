
<?php
session_start();

header('Content-Type: application/json');
$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Debug log (after reading POST data)
file_put_contents(__DIR__ . '/debug.log', "SID: " . ($_SESSION['student_id'] ?? 'null') . " | POST: " . ($data['student_id'] ?? 'null') . "\n", FILE_APPEND);

$student_id = $_SESSION['student_id'] ?? $data['student_id'];
$book_id = $data['book_id'];

if (!$student_id || !$book_id) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

// Check if already requested and still pending/approved
$stmt = $conn->prepare("SELECT * FROM request WHERE student_id = ? AND book_id = ? AND status IN ('pending', 'approved', 'ready to pick up')");
$stmt->bind_param("si", $student_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already requested this book.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Insert request as pending
$stmt = $conn->prepare("INSERT INTO request (student_id, book_id, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("si", $student_id, $book_id);
if ($stmt->execute()) {
    updatePendingRequestsCount($conn);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

function updatePendingRequestsCount($conn) {
    $result = $conn->query("SELECT COUNT(*) AS cnt FROM request WHERE status = 'pending'");
    $row = $result->fetch_assoc();
    $total = (int)$row['cnt'];
    // If count_items table is empty, insert a row
    $conn->query("INSERT INTO count_items (pending_requests) SELECT $total WHERE NOT EXISTS (SELECT 1 FROM count_items)");
    // Otherwise, update the row
    $conn->query("UPDATE count_items SET pending_requests = $total");
}
$stmt->close();
$conn->close();
?>