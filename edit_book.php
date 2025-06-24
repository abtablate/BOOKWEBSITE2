<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: 1.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Get book ID
if (!isset($_GET['id'])) {
    header("Location: admin_panel.php");
    exit();
}
$id = intval($_GET['id']);

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_book'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $cover = $conn->real_escape_string($_POST['cover']);
    $description = $conn->real_escape_string($_POST['description']);
    $conn->query("UPDATE books SET title='$title', author='$author', cover='$cover', description='$description' WHERE id=$id");
    header("Location: admin_panel.php");
    exit();
}

// Fetch book data
$book = $conn->query("SELECT * FROM books WHERE id=$id")->fetch_assoc();
if (!$book) {
    echo "Book not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
    <link rel="stylesheet" href="haha.css">
    <style>
        body {
  transition: opacity 0.5s;
}
body.fade-out {
  opacity: 0;
}
        body { background: #fff6e3; font-family: 'Segoe UI', Arial, sans-serif; }
        .edit-container { max-width: 500px; margin: 60px auto; background: #fff; border-radius: 14px; box-shadow: 0 4px 24px rgba(42,93,255,0.10); padding: 32px; }
        h2 { color: #9c6b3e; }
        label { font-weight: 500; color: #7b522e; display: block; margin-top: 12px; }
        input, textarea {
            width: 100%; padding: 8px; margin-top: 4px; border-radius: 7px; border: 1px solid #9c6b3e; background: #fff; color: #3a2a13;
        }
        .admin-btn {
            margin-top: 18px;
            padding: 10px 24px;
            border: none;
            border-radius: 20px;
            background: #9c6b3e;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .admin-btn:hover { background: #7b522e; }
        .back-link { display: inline-block; margin-bottom: 18px; color: #9c6b3e; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="edit-container">
        <a href="admin_panel.php" class="back-link">&larr; Back to Admin Panel</a>
        <h2>Edit Book</h2>
        <form method="post">
            <label>Title:
                <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </label>
            <label>Author:
                <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </label>
            <label>Cover Image Filename:
                <input type="text" name="cover" value="<?php echo htmlspecialchars($book['cover']); ?>" required>
            </label>
            <label>Description:
                <textarea name="description" rows="3"><?php echo htmlspecialchars($book['description']); ?></textarea>
            </label>
            <button type="submit" name="update_book" class="admin-btn">Update Book</button>
        </form>
    </div>
    <script>
document.querySelectorAll('a').forEach(function(link) {
  // Only apply to internal links
  if (link.hostname === window.location.hostname && link.target !== "_blank" && !link.href.startsWith('javascript:')) {
    link.addEventListener('click', function(e) {
      // Ignore anchor links
      if (link.hash && link.pathname === window.location.pathname) return;
      e.preventDefault();
      document.body.classList.add('fade-out');
      setTimeout(function() {
        window.location = link.href;
      }, 500); // Match the CSS transition duration
    });
  }
});
</script>
</body>
</html>