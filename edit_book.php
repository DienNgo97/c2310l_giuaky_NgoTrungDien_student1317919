<?php
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid book ID.");
}

// Lấy thông tin sách để chỉnh sửa
$query = "SELECT books.*, authors.author_name, categories.category_name 
          FROM books 
          JOIN authors ON books.author_id = authors.id 
          JOIN categories ON books.category_id = categories.id 
          WHERE books.id = $id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$book = mysqli_fetch_assoc($result);

if (!$book) {
    die("Book not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $title = isset($_POST['title']) ? mysqli_real_escape_string($conn, $_POST['title']) : '';
    $author_name = isset($_POST['author_name']) ? mysqli_real_escape_string($conn, $_POST['author_name']) : '';
    $category_name = isset($_POST['category_name']) ? mysqli_real_escape_string($conn, $_POST['category_name']) : '';
    $publisher = isset($_POST['publisher']) ? mysqli_real_escape_string($conn, $_POST['publisher']) : '';
    $publish_year = isset($_POST['publish_year']) ? mysqli_real_escape_string($conn, $_POST['publish_year']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    // Kiểm tra xem tất cả các trường đều được nhập
    if (empty($title) || empty($author_name) || empty($category_name) || empty($publisher) || empty($publish_year) || $quantity <= 0) {
        $error = "All fields are required.";
    } else {
        // Lấy ID của tác giả từ tên, nếu không tồn tại, thêm mới
        $author_query = "SELECT id FROM authors WHERE author_name = '$author_name'";
        $author_result = mysqli_query($conn, $author_query);
        if (!$author_result) {
            die("Author query failed: " . mysqli_error($conn));
        }
        $author = mysqli_fetch_assoc($author_result);
        if ($author) {
            $author_id = $author['id'];
        } else {
            // Thêm tác giả mới vào cơ sở dữ liệu
            $insert_author_query = "INSERT INTO authors (author_name) VALUES ('$author_name')";
            $insert_author_result = mysqli_query($conn, $insert_author_query);
            if (!$insert_author_result) {
                die("Failed to insert author: " . mysqli_error($conn));
            }
            $author_id = mysqli_insert_id($conn);
        }

        // Lấy ID của danh mục từ tên, nếu không tồn tại, thêm mới
        $category_query = "SELECT id FROM categories WHERE category_name = '$category_name'";
        $category_result = mysqli_query($conn, $category_query);
        if (!$category_result) {
            die("Category query failed: " . mysqli_error($conn));
        }
        $category = mysqli_fetch_assoc($category_result);
        if ($category) {
            $category_id = $category['id'];
        } else {
            // Thêm danh mục mới vào cơ sở dữ liệu
            $insert_category_query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
            $insert_category_result = mysqli_query($conn, $insert_category_query);
            if (!$insert_category_result) {
                die("Failed to insert category: " . mysqli_error($conn));
            }
            $category_id = mysqli_insert_id($conn);
        }

        // Cập nhật thông tin sách
        $update_query = "UPDATE books 
                         SET title = '$title', author_id = $author_id, category_id = $category_id, 
                             publisher = '$publisher', publish_year = '$publish_year', quantity = $quantity 
                         WHERE id = $id";
        $update_result = mysqli_query($conn, $update_query);

        if ($update_result) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to update book: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #d4edda; 
        }
        .container {
            margin-top: 20px;
            background-color: #ffffff; 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Edit Book</h1>
        
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit_book.php?id=<?php echo $id; ?>">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="author_name">Author</label>
                <input type="text" class="form-control" id="author_name" name="author_name" value="<?php echo htmlspecialchars($book['author_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category_name">Category</label>
                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($book['category_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="publisher">Publisher</label>
                <input type="text" class="form-control" id="publisher" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>" required>
            </div>
            <div class="form-group">
                <label for="publish_year">Publish Year</label>
                <input type="text" class="form-control" id="publish_year" name="publish_year" value="<?php echo htmlspecialchars($book['publish_year']); ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($book['quantity']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
