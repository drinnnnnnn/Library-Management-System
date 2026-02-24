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

$student_id = $_SESSION['student_id'];
$sql = "SELECT r.request_id, b.book_name, b.book_category, r.status, 
        DATE_FORMAT(r.request_date, '%H:%i') as request_time, 
        DATE_FORMAT(r.request_date, '%m/%d/%Y') as request_date
        FROM request r
        JOIN books b ON r.book_id = b.book_id
        WHERE r.student_id = ?
        ORDER BY r.request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($row = $result->fetch_assoc()) $rows[] = $row;
echo json_encode($rows);
?>