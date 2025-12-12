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

include_once '../../config/database.php';
include_once '../../models/Book.php';

$database = new Database();
$db = $database->getConnection();
$book = new Book($db);

// Get ID from URL parameter
$book->id = isset($_GET['id']) ? $_GET['id'] : die();

// Get book
if($book->readSingle()) {
    $book_arr = array(
        "success" => true,
        "data" => array(
            "id" => (int)$book->id,
            "title" => $book->title,
            "author" => $book->author,
            "isbn" => $book->isbn,
            "year" => (int)$book->year,
            "genre" => $book->genre,
            "description" => html_entity_decode($book->description),
            "coverUrl" => $book->coverUrl
        )
    );

    http_response_code(200);
    echo json_encode($book_arr);
} else {
    http_response_code(404);
    echo json_encode(array(
        "success" => false,
        "message" => "Book not found"
    ));
}
?>

