<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database and model
include_once '../../config/database.php';
include_once '../../models/Book.php';

// Instantiate database and book object
$database = new Database();
$db = $database->getConnection();
$book = new Book($db);

// Query books
$stmt = $book->read();
$num = $stmt->rowCount();

// Check if more than 0 books
if($num > 0) {
    $books_arr = array();
    $books_arr["success"] = true;
    $books_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $book_item = array(
            "id" => (int)$id,
            "title" => $title,
            "author" => $author,
            "isbn" => $isbn,
            "year" => (int)$year,
            "genre" => $genre,
            "description" => html_entity_decode($description),
            "coverUrl" => $coverUrl
        );

        array_push($books_arr["data"], $book_item);
    }

    http_response_code(200);
    echo json_encode($books_arr);
} else {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => array()
    ));
}
?>

