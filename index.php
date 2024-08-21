<?php
include 'db.php';

// Xác định số lượng mục trên mỗi trang và trang hiện tại
$items_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

// Lấy dữ liệu từ form tìm kiếm và lọc
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($conn, $_GET['search_query']) : '';
$author_filter = isset($_GET['author_filter']) ? mysqli_real_escape_string($conn, $_GET['author_filter']) : '';
$category_filter = isset($_GET['category_filter']) ? mysqli_real_escape_string($conn, $_GET['category_filter']) : '';
$publisher_filter = isset($_GET['publisher_filter']) ? mysqli_real_escape_string($conn, $_GET['publisher_filter']) : '';
$publish_year_filter = isset($_GET['publish_year_filter']) ? mysqli_real_escape_string($conn, $_GET['publish_year_filter']) : '';

// Lấy số lượng sách dựa trên tiêu chí tìm kiếm và lọc
$count_query = "SELECT COUNT(*) as total FROM books 
                JOIN authors ON books.author_id = authors.id 
                JOIN categories ON books.category_id = categories.id 
                WHERE books.is_deleted = 0 
                AND (books.title LIKE '%$search_query%' 
                OR authors.author_name LIKE '%$search_query%' 
                OR categories.category_name LIKE '%$search_query%' 
                OR books.publisher LIKE '%$search_query%' 
                OR books.publish_year LIKE '%$search_query%')";

if (!empty($author_filter)) {
    $count_query .= " AND authors.author_name LIKE '%$author_filter%'";
}
if (!empty($category_filter)) {
    $count_query .= " AND categories.category_name LIKE '%$category_filter%'";
}
if (!empty($publisher_filter)) {
    $count_query .= " AND books.publisher LIKE '%$publisher_filter%'";
}
if (!empty($publish_year_filter)) {
    $count_query .= " AND books.publish_year LIKE '%$publish_year_filter%'";
}

$count_result = mysqli_query($conn, $count_query);

if (!$count_result) {
    die("Query failed: " . mysqli_error($conn));
}

$total_items = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

// Lấy danh sách sách với phân trang và lọc
$query = "SELECT books.id, books.title, authors.author_name, categories.category_name, books.publisher, books.publish_year, books.quantity 
          FROM books 
          JOIN authors ON books.author_id = authors.id 
          JOIN categories ON books.category_id = categories.id 
          WHERE books.is_deleted = 0 
          AND (books.title LIKE '%$search_query%' 
          OR authors.author_name LIKE '%$search_query%' 
          OR categories.category_name LIKE '%$search_query%' 
          OR books.publisher LIKE '%$search_query%' 
          OR books.publish_year LIKE '%$search_query%')";

if (!empty($author_filter)) {
    $query .= " AND authors.author_name LIKE '%$author_filter%'";
}
if (!empty($category_filter)) {
    $query .= " AND categories.category_name LIKE '%$category_filter%'";
}
if (!empty($publisher_filter)) {
    $query .= " AND books.publisher LIKE '%$publisher_filter%'";
}
if (!empty($publish_year_filter)) {
    $query .= " AND books.publish_year LIKE '%$publish_year_filter%'";
}

$query .= " LIMIT $offset, $items_per_page";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Lấy dữ liệu cho các filter dropdown
$authors_query = "SELECT DISTINCT author_name FROM authors";
$authors_result = mysqli_query($conn, $authors_query);

$categories_query = "SELECT DISTINCT category_name FROM categories";
$categories_result = mysqli_query($conn, $categories_query);

$publishers_query = "SELECT DISTINCT publisher FROM books";
$publishers_result = mysqli_query($conn, $publishers_query);

$publish_years_query = "SELECT DISTINCT publish_year FROM books";
$publish_years_result = mysqli_query($conn, $publish_years_query);

// Đóng kết nối
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library</title>
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
        .table {
            background-color: #f8f9fa; 
        }
        .pagination .page-item.active .page-link {
            background-color: #28a745;
            border-color: #28a745;
        }
        .pagination .page-link {
            color: #28a745; 
        }
        .btn-primary {
            background-color: #28a745; 
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838; 
            border-color: #1e7e34;
        }
        .btn-warning {
            background-color: #ffc107; 
            border-color: #ffc107;
        }
        .btn-warning:hover {
            background-color: #e0a800; 
            border-color: #d39e00;
        }
        .btn-danger {
            background-color: #dc3545; 
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333; 
            border-color: #bd2130;
        }
        .btn-clear {
            background-color: #dfe7ee; 
            border-color: #6c757d;
        }
        .btn-clear:hover {
            background-color: #5a6268; 
            border-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Library</h1>
        
        <!-- Form tìm kiếm -->
        <form class="mb-3" method="GET" action="index.php">
            <div class="form-group">
                <label for="search_query">Search</label>
                <input type="text" class="form-control" id="search_query" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Form lọc -->
        <form class="mb-3" method="GET" action="index.php">
            <input type="hidden" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="author_filter">Author</label>
                    <select class="form-control" id="author_filter" name="author_filter">
                        <option value="">Select Author</option>
                        <?php while ($author = mysqli_fetch_assoc($authors_result)): ?>
                        <option value="<?php echo htmlspecialchars($author['author_name']); ?>" <?php echo ($author_filter == $author['author_name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($author['author_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="category_filter">Category</label>
                    <select class="form-control" id="category_filter" name="category_filter">
                        <option value="">Select Category</option>
                        <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?php echo htmlspecialchars($category['category_name']); ?>" <?php echo ($category_filter == $category['category_name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="publisher_filter">Publisher</label>
                    <select class="form-control" id="publisher_filter" name="publisher_filter">
                        <option value="">Select Publisher</option>
                        <?php while ($publisher = mysqli_fetch_assoc($publishers_result)): ?>
                        <option value="<?php echo htmlspecialchars($publisher['publisher']); ?>" <?php echo ($publisher_filter == $publisher['publisher']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($publisher['publisher']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="publish_year_filter">Publish Year</label>
                    <select class="form-control" id="publish_year_filter" name="publish_year_filter">
                        <option value="">Select Publish Year</option>
                        <?php while ($publish_year = mysqli_fetch_assoc($publish_years_result)): ?>
                        <option value="<?php echo htmlspecialchars($publish_year['publish_year']); ?>" <?php echo ($publish_year_filter == $publish_year['publish_year']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($publish_year['publish_year']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Apply Filters</button>
                    <a href="index.php" class="btn btn-clear">Clear Filters</a>
                </div>
            </div>
        </form>

        <a href="add_book.php" class="btn btn-primary mb-3">Add New Book</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Publisher</th>
                    <th>Publish Year</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['publisher']); ?></td>
                    <td><?php echo htmlspecialchars($row['publish_year']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td>
                        <a href="edit_book.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-id="<?php echo $row['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Phân Trang -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="index.php?page=<?php echo $page - 1; ?>&search_query=<?php echo urlencode($search_query); ?>&author_filter=<?php echo urlencode($author_filter); ?>&category_filter=<?php echo urlencode($category_filter); ?>&publisher_filter=<?php echo urlencode($publisher_filter); ?>&publish_year_filter=<?php echo urlencode($publish_year_filter); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="index.php?page=<?php echo $i; ?>&search_query=<?php echo urlencode($search_query); ?>&author_filter=<?php echo urlencode($author_filter); ?>&category_filter=<?php echo urlencode($category_filter); ?>&publisher_filter=<?php echo urlencode($publisher_filter); ?>&publish_year_filter=<?php echo urlencode($publish_year_filter); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="index.php?page=<?php echo $page + 1; ?>&search_query=<?php echo urlencode($search_query); ?>&author_filter=<?php echo urlencode($author_filter); ?>&category_filter=<?php echo urlencode($category_filter); ?>&publisher_filter=<?php echo urlencode($publisher_filter); ?>&publish_year_filter=<?php echo urlencode($publish_year_filter); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this book?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="delete_book.php">
                        <input type="hidden" name="id" id="bookId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $('#confirmDeleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button triggered  modal
            var bookId = button.data('id'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find('#bookId').val(bookId);
        });
    </script>
</body>
</html>
