<?php
// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 3600");
    http_response_code(200);
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Book.php';

$database = new Database();
$db = $database->getConnection();
$book = new Book($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Get ID from URL
$book->id = isset($_GET['id']) ? $_GET['id'] : die();

// Set book property values
$book->title = $data->title;
$book->author = $data->author;
$book->isbn = $data->isbn;
$book->year = $data->year;
$book->genre = $data->genre;
$book->description = $data->description;
$book->coverUrl = isset($data->coverUrl) ? $data->coverUrl : null;

// Update the book
if($book->update()) {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "message" => "Book was updated.",
        "data" => array(
            "id" => (int)$book->id,
            "title" => $book->title,
            "author" => $book->author,
            "isbn" => $book->isbn,
            "year" => (int)$book->year,
            "genre" => $book->genre,
            "description" => $book->description,
            "coverUrl" => $book->coverUrl
        )
    ));
} else {
    http_response_code(503);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to update book"
    ));
}
?>

