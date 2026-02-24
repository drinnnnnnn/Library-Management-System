<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$connection = mysqli_connect("localhost", "root", "", "library_db", 3306);
if (!$connection) {
    echo json_encode(["error" => "DB connection failed: " . mysqli_connect_error()]);
    exit;
}

// Fetch all relevant fields, including quantity and fine_value
$sql = "SELECT book_id, book_name, book_author, book_category, book_description, book_image, 
        CASE 
          WHEN quantity = 0 THEN 'unavailable' 
          ELSE book_status 
        END AS book_status, 
        quantity, fine_value 
        FROM books 
        ORDER BY created_at DESC";
$result = mysqli_query($connection, $sql);

$books = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
} else {
    echo json_encode(["error" => "Query failed: " . mysqli_error($connection)]);
    exit;
}

mysqli_close($connection);

echo json_encode($books);
?>