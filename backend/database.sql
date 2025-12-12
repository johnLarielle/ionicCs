-- eLibrary Database Setup
-- Run this in phpMyAdmin or MySQL command line

-- Create database
CREATE DATABASE IF NOT EXISTS elibrary_db;

-- Use database
USE elibrary_db;

-- Create books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    genre VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    coverUrl VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO books (title, author, isbn, year, genre, description, coverUrl) VALUES
('To Kill a Mockingbird', 'Harper Lee', '978-0-06-112008-4', 1960, 'Fiction', 
 'A gripping tale of racial injustice and childhood innocence in the American South.', 
 'https://via.placeholder.com/150/4A90E2/FFFFFF?text=Book+1'),
 
('1984', 'George Orwell', '978-0-452-28423-4', 1949, 'Dystopian', 
 'A dystopian social science fiction novel and cautionary tale about totalitarianism.', 
 'https://via.placeholder.com/150/E94B3C/FFFFFF?text=Book+2'),
 
('The Great Gatsby', 'F. Scott Fitzgerald', '978-0-7432-7356-5', 1925, 'Classic', 
 'A story of decadence and excess in the Jazz Age.', 
 'https://via.placeholder.com/150/6FCC76/FFFFFF?text=Book+3'),
 
('Pride and Prejudice', 'Jane Austen', '978-0-14-143951-8', 1813, 'Romance', 
 'A romantic novel of manners set in Georgian England.', 
 'https://via.placeholder.com/150/9C27B0/FFFFFF?text=Book+4'),
 
('The Catcher in the Rye', 'J.D. Salinger', '978-0-316-76948-0', 1951, 'Fiction', 
 'A story about teenage rebellion and alienation.', 
 'https://via.placeholder.com/150/FF5722/FFFFFF?text=Book+5');

-- Show tables
SHOW TABLES;

-- Show sample data
SELECT * FROM books;

