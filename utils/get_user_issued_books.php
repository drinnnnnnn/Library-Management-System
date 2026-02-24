<?php
header('Content-Type: application/json');
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Kunin ang student_id ng naka-login na user
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
} else {
    // Optional: fallback kung walang session, subukan kunin sa GET/POST
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';
}

if (!$student_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT b.book_name, b.book_category, ib.date_student_received_book, ib.book_returned_date
        FROM issued_books ib
        JOIN books b ON ib.book_id = b.book_id
        WHERE ib.student_id = ?
        ORDER BY ib.date_student_received_book DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
echo json_encode($rows);
$conn->close();
?>