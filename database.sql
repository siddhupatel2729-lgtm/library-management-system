CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS issue_return (
    issue_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    issue_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('Issued','Returned') NOT NULL DEFAULT 'Issued',
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

INSERT IGNORE INTO books (title, author, isbn, category) VALUES
('The Great Gatsby','F. Scott Fitzgerald','9780743273565','Fiction'),
('Clean Code','Robert C. Martin','9780132350884','Programming'),
('Database System Concepts','Abraham Silberschatz','9780073523323','Database');

INSERT INTO students (name,email,phone) VALUES
('Aarav Shah','aarav@example.com','9876543210'),
('Diya Patel','diya@example.com','9876501234');