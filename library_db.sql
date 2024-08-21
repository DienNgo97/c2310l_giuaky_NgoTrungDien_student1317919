CREATE DATABASE library;

USE library;

-- Bảng tác giả
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_name NVARCHAR(255) NOT NULL,
    book_numbers INT
);

-- Bảng thể loại sách
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name NVARCHAR(255) NOT NULL
);

-- Bảng sách
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title NVARCHAR(255) NOT NULL,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    publisher NVARCHAR(255),
    publish_year YEAR,
    quantity INT,
    FOREIGN KEY (author_id) REFERENCES authors(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);


INSERT INTO authors (author_name, book_numbers) VALUES
('J.K. Rowling', 7),
('George R.R. Martin', 5),
('J.R.R. Tolkien', 3),
('Stephen King', 60),
('Agatha Christie', 66),
('Dan Brown', 10),
('John Grisham', 25),
('Isaac Asimov', 30),
('Arthur Conan Doyle', 56),
('Terry Pratchett', 41);

INSERT INTO categories (category_name) VALUES
('Fiction'),
('Science Fiction'),
('Fantasy'),
('Mystery'),
('Thriller'),
('Non-Fiction'),
('Biography'),
('History'),
('Science'),
('Romance');

INSERT INTO books (title, author_id, category_id, publisher, publish_year, quantity) VALUES
('Harry Potter and the Sorcerer\'s Stone', 1, 3, 'Bloomsbury', 1997, 100),
('A Game of Thrones', 2, 1, 'Bantam Books', 1996, 50),
('The Hobbit', 3, 3, 'Houghton Mifflin', 1937, 70),
('The Shining', 4, 1, 'Doubleday', 1977, 80),
('Murder on the Orient Express', 5, 4, 'Collins Crime Club', 1934, 90),
('The Da Vinci Code', 6, 1, 'Doubleday', 2003, 60),
('The Firm', 7, 1, 'Doubleday', 1991, 85),
('Foundation', 8, 2, 'Gnome Press', 1951, 75),
('The Adventures of Sherlock Holmes', 9, 4, 'George Newnes', 1934, 65),
('Discworld: The Colour of Magic', 10, 3, 'Colin Smythe', 1983, 55);


