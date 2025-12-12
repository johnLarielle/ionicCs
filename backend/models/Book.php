<?php
class Book {
    // Database connection and table name
    private $conn;
    private $table_name = "books";

    // Book properties
    public $id;
    public $title;
    public $author;
    public $isbn;
    public $year;
    public $genre;
    public $description;
    public $coverUrl;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all books
    function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single book
    function readSingle() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->title = $row['title'];
            $this->author = $row['author'];
            $this->isbn = $row['isbn'];
            $this->year = $row['year'];
            $this->genre = $row['genre'];
            $this->description = $row['description'];
            $this->coverUrl = $row['coverUrl'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        
        return false;
    }

    // Create book
    function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    title=:title,
                    author=:author,
                    isbn=:isbn,
                    year=:year,
                    genre=:genre,
                    description=:description,
                    coverUrl=:coverUrl";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->year = htmlspecialchars(strip_tags($this->year));
        $this->genre = htmlspecialchars(strip_tags($this->genre));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->coverUrl = htmlspecialchars(strip_tags($this->coverUrl));

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":year", $this->year);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":coverUrl", $this->coverUrl);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Update book
    function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    title=:title,
                    author=:author,
                    isbn=:isbn,
                    year=:year,
                    genre=:genre,
                    description=:description,
                    coverUrl=:coverUrl
                WHERE
                    id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->year = htmlspecialchars(strip_tags($this->year));
        $this->genre = htmlspecialchars(strip_tags($this->genre));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->coverUrl = htmlspecialchars(strip_tags($this->coverUrl));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":year", $this->year);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":coverUrl", $this->coverUrl);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete book
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Search books
    function search($searchTerm) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE 
                    title LIKE ? OR 
                    author LIKE ? OR 
                    genre LIKE ?
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);

        $searchTerm = htmlspecialchars(strip_tags($searchTerm));
        $searchTerm = "%{$searchTerm}%";

        $stmt->bindParam(1, $searchTerm);
        $stmt->bindParam(2, $searchTerm);
        $stmt->bindParam(3, $searchTerm);

        $stmt->execute();

        return $stmt;
    }
}
?>

