<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    // Câu lệnh SQL để đánh dấu sách là đã xóa
    $query = "UPDATE books SET is_deleted = 1 WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        header('Location: index.php');
    } else {
        // Hiển thị lỗi SQL chi tiết
        echo "Error: " . mysqli_error($conn);
    }
}
?>
