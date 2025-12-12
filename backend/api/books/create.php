<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 3600");
    http_response_code(200);
    exit();
}

// CORS headers for actual request
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Book.php';

$database = new Database();
$db = $database->getConnection();
$book = new Book($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->title) &&
    !empty($data->author) &&
    !empty($data->isbn) &&
    !empty($data->year) &&
    !empty($data->genre) &&
    !empty($data->description)
) {
    // Set book property values
    $book->title = $data->title;
    $book->author = $data->author;
    $book->isbn = $data->isbn;
    $book->year = $data->year;
    $book->genre = $data->genre;
    $book->description = $data->description;
    $book->coverUrl = isset($data->coverUrl) ? $data->coverUrl : null;

    // Create the book
    if($book->create()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Book was created.",
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
            "message" => "Unable to create book"
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to create book. Data is incomplete."
    ));
}
?>

