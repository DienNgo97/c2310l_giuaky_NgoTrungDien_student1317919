<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form và xử lý
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $publisher = mysqli_real_escape_string($conn, $_POST['publisher']);
    $publish_year = intval($_POST['publish_year']);
    $quantity = intval($_POST['quantity']);

    // Kiểm tra xem tất cả các biến đã được gán giá trị hợp lệ
    if (empty($title) || empty($author_name) || empty($category_name) || empty($publisher) || empty($publish_year) || empty($quantity)) {
        die("All fields are required.");
    }

    // Kiểm tra và thêm mới tác giả nếu không tồn tại
    $author_query = "SELECT id FROM authors WHERE author_name = '$author_name'";
    $author_result = mysqli_query($conn, $author_query);

    if (mysqli_num_rows($author_result) > 0) {
        $author_id = mysqli_fetch_assoc($author_result)['id'];
    } else {
        $insert_author_query = "INSERT INTO authors (author_name) VALUES ('$author_name')";
        mysqli_query($conn, $insert_author_query);
        $author_id = mysqli_insert_id($conn);
    }

    // Kiểm tra và thêm mới thể loại nếu không tồn tại
    $category_query = "SELECT id FROM categories WHERE category_name = '$category_name'";
    $category_result = mysqli_query($conn, $category_query);

    if (mysqli_num_rows($category_result) > 0) {
        $category_id = mysqli_fetch_assoc($category_result)['id'];
    } else {
        $insert_category_query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
        mysqli_query($conn, $insert_category_query);
        $category_id = mysqli_insert_id($conn);
    }

    // Câu lệnh SQL để thêm sách
    $query = "INSERT INTO books (title, author_id, category_id, publisher, publish_year, quantity) 
              VALUES ('$title', $author_id, $category_id, '$publisher', $publish_year, $quantity)";
    
    if (mysqli_query($conn, $query)) {
        header('Location: index.php');
    } else {
        // Hiển thị lỗi SQL chi tiết
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Add New Book</h1>
        <form action="add_book.php" method="POST">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="author_name">Author</label>
                <input type="text" class="form-control" id="author_name" name="author_name" required>
            </div>
            <div class="form-group">
                <label for="category_name">Category</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="publisher">Publisher</label>
                <input type="text" class="form-control" id="publisher" name="publisher" required>
            </div>
            <div class="form-group">
                <label for="publish_year">Publish Year</label>
                <input type="number" class="form-control" id="publish_year" name="publish_year" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
