<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: 1.php");
    exit();
}

require "db_connection.php";

$uploadDir = 'uploads/chapters/';
$bookCoverDir = 'uploads/covers/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
if (!file_exists($bookCoverDir)) mkdir($bookCoverDir, 0777, true);

// Delete book
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT cover FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($book && file_exists($book['cover'])) unlink($book['cover']);

    $stmt = $pdo->prepare("SELECT content, image FROM chapters WHERE book_id = ?");
    $stmt->execute([$id]);
    while ($chapter = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($chapter['image'] && file_exists($chapter['image'])) unlink($chapter['image']);
        if (strpos($chapter['content'], 'uploads/chapters/') !== false) {
            foreach (explode("\n", $chapter['content']) as $img)
                if (file_exists($img)) unlink($img);
        }
    }

    $pdo->prepare("DELETE FROM chapters WHERE book_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$id]);
    header("Location: admin_panel.php");
    exit();
}

// Add or Update Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['addBook']) || isset($_POST['updateBook']))) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $available = isset($_POST['available']) ? 1 : 0;

    $coverPath = '';
    if (!empty($_FILES['cover']['name'])) {
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        $destination = $bookCoverDir . $newName;
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $destination)) {
            $coverPath = $destination;
        }
    }

    if (isset($_POST['addBook'])) {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, cover, description, available) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $coverPath, $description, $available]);
    } else {
        $book_id = $_POST['book_id'];
        $query = "UPDATE books SET title=?, author=?, description=?, available=?";
        $params = [$title, $author, $description, $available];
        if ($coverPath) {
            $query .= ", cover=?";
            $params[] = $coverPath;
        }
        $query .= " WHERE id=?";
        $params[] = $book_id;
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
    }
    header("Location: admin_panel.php");
    exit();
}

// Add or Update Chapter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addChapter'])) {
    $chapter_id = isset($_POST['chapter_id']) ? intval($_POST['chapter_id']) : 0;
    $book_id = $_POST['book_id'];
    $chapter_number = $_POST['chapter_number'];
    $title = $_POST['title'];
    $chapter_type = $_POST['chapter_type'];
    $content = $chapter_type === 'text' ? $_POST['content'] : '';
    $coverImagePath = '';

    if (!empty($_FILES['cover_image']['name'])) {
        $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $coverName = uniqid('chapter_cover_') . '.' . $ext;
        $coverDestination = $uploadDir . $coverName;
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverDestination)) {
            $coverImagePath = $coverDestination;
        }
    }

    if ($chapter_type === 'image' && isset($_FILES['chapter_images'])) {
        $imagePaths = [];
        foreach ($_FILES['chapter_images']['tmp_name'] as $index => $tmpPath) {
            if (!empty($_FILES['chapter_images']['name'][$index])) {
                $ext = pathinfo($_FILES['chapter_images']['name'][$index], PATHINFO_EXTENSION);
                $imgName = uniqid('img_') . '.' . $ext;
                $targetPath = $uploadDir . $imgName;
                if (move_uploaded_file($tmpPath, $targetPath)) {
                    $imagePaths[] = $targetPath;
                }
            }
        }
        $content = implode("\n", $imagePaths);
    }

    if ($chapter_id > 0) {
        $stmt = $pdo->prepare("UPDATE chapters SET chapter_number=?, title=?, content=?, image=?, chapter_type=? WHERE id=?");
        $stmt->execute([$chapter_number, $title, $content, $coverImagePath, $chapter_type, $chapter_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO chapters (book_id, chapter_number, title, content, image, chapter_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$book_id, $chapter_number, $title, $content, $coverImagePath, $chapter_type]);
    }
    header("Location: admin_panel.php?book_id=$book_id");
    exit();
}

// Fetch data
$books = $pdo->query("SELECT * FROM books")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, username, email FROM users")->fetchAll(PDO::FETCH_ASSOC);

$current_book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : null;
$current_book = null;
$chapters = [];
$editingBook = null;

if ($current_book_id) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$current_book_id]);
    $current_book = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM chapters WHERE book_id = ? ORDER BY chapter_number ASC");
    $stmt->execute([$current_book_id]);
    $chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_GET['edit_book'])) {
    $edit_id = intval($_GET['edit_book']);
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$edit_id]);
    $editingBook = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Book Library</title>
    <link rel="stylesheet" href="haha.css">
    <style>
        /* Admin panel styles */
        body { 
            background: #fff6e3; 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0;
            padding: 0;
        }
        
        .admin-container { 
            max-width: 1200px; 
            margin: 40px auto; 
            background: #fff; 
            border-radius: 18px; 
            box-shadow: 0 4px 24px rgba(42,93,255,0.10); 
            padding: 32px; 
        }
        
        /* Chapter upload form styles */
        .add-form { 
            margin-top: 32px; 
            background: #fff6e3; 
            padding: 18px; 
            border-radius: 12px; 
        }
        
        .chapter-type-selector { 
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .chapter-type-selector label {
            display: inline-block;
            margin-right: 15px;
            cursor: pointer;
        }
        
        .upload-preview {
            border: 2px dashed #9c6b3e;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .file-list {
            list-style-type: none;
            padding: 0;
            margin: 10px 0;
        }
        
        .file-item {
            display: flex;
            align-items: center;
            padding: 8px;
            margin: 5px 0;
            background: #f9f9f9;
            border-radius: 4px;
        }
        
        .file-order {
            font-weight: bold;
            margin-right: 10px;
            min-width: 25px;
            text-align: center;
        }
        
        .file-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }
        
        .file-name {
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Button Styles */
        .admin-btn, .chapter-btn, .edit-btn, .delete-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
        }
        
        /* Main Admin Button */
        .admin-btn {
            background-color: #9c6b3e;
            color: white;
            border: 2px solid #9c6b3e;
        }
        
        .admin-btn:hover {
            background-color: #8a5d34;
            border-color: #8a5d34;
        }
        
        /* Chapter Management Button */
        .chapter-btn {
            background-color: #4a6fa5;
            color: white;
            border: 2px solid #4a6fa5;
        }
        
        .chapter-btn:hover {
            background-color: #3d5d8a;
            border-color: #3d5d8a;
        }
        
        /* Edit Button */
        .edit-btn {
            background-color: #4caf50;
            color: white;
            border: 2px solid #4caf50;
        }
        
        .edit-btn:hover {
            background-color: #3e8e41;
            border-color: #3e8e41;
        }
        
        /* Delete Button */
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: 2px solid #f44336;
        }
        
        .delete-btn:hover {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }
        
        /* Action Buttons Container */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        /* Form Submit Buttons */
        button[type="submit"] {
            background-color: #2e7d32;
            color: white;
            padding: 12px 24px;
            margin-top: 15px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        button[type="submit"]:hover {
            background-color: #1b5e20;
        }
        
        /* Modal Styles */
        #userModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        #userModal > div {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        #userModal label {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        #userModal input[type="text"],
        #userModal input[type="email"],
        #userModal input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .admin-container {
                margin: 20px;
                padding: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .admin-btn, .chapter-btn, .edit-btn, .delete-btn {
                padding: 8px 12px;
                font-size: 12px;
            }
            
            button[type="submit"] {
                padding: 10px 18px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Panel</h1>
        <a href="1.php" class="admin-btn" style="margin-top:16px; display:inline-block;">Back to Library</a>
        
        <?php if ($current_book_id): ?>
            <a href="admin_panel.php" class="admin-btn">Back to All Books</a>
            <h2>Managing Chapters for: <?php echo htmlspecialchars($current_book['title']); ?></h2>
            
            <!-- Chapters List -->
            <table>
                <tr>
                    <th>Chapter #</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Cover</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($chapters as $chapter): ?>
                <tr>
                    <td><?php echo $chapter['chapter_number']; ?></td>
                    <td><?php echo htmlspecialchars($chapter['title']); ?></td>
                    <td><?php echo $chapter['chapter_type'] === 'image' ? 'Image Story' : 'Text Story'; ?></td>
                    <td>
                        <?php if ($chapter['image']): ?>
                            <img src="<?php echo htmlspecialchars($chapter['image']); ?>" alt="Chapter cover" style="height:60px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="edit-btn" onclick="openChapterModal(
                                <?php echo $chapter['id']; ?>,
                                <?php echo $chapter['chapter_number']; ?>,
                                '<?php echo htmlspecialchars($chapter['title'], ENT_QUOTES); ?>',
                                `<?php echo htmlspecialchars(str_replace(array("\r", "\n"), '', $chapter['content']), ENT_QUOTES); ?>`,
                                '<?php echo htmlspecialchars($chapter['image'], ENT_QUOTES); ?>',
                                '<?php echo $chapter['chapter_type']; ?>'
                            )">Edit</button>
                            <a href="admin_panel.php?book_id=<?php echo $current_book_id; ?>&delete_chapter=<?php echo $chapter['id']; ?>" class="delete-btn" onclick="return confirm('Delete this chapter?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <!-- Add Chapter Form -->
            <form class="add-form" method="post" id="chapterForm" enctype="multipart/form-data">
                <h2>Add New Chapter</h2>
                <input type="hidden" name="id" id="chapter_id">
                
                <div class="chapter-type-selector">
                    <label>
                        <input type="radio" name="chapter_type" value="text" id="type-text" onchange="toggleChapterType()"> Text Story
                    </label>
                    <label>
                        <input type="radio" name="chapter_type" value="image" id="type-image" onchange="toggleChapterType()" checked> Image Story
                    </label>
                </div>
                
                <label>Chapter Number:</label>
                <input type="number" name="chapter_number" id="chapter_number" required min="1">
                
                <label>Title:</label>
                <input type="text" name="title" id="chapter_title" required>
                
                <!-- Text Content Field -->
                <div id="text-content-field" class="chapter-content-field">
                    <label>Story Content:</label>
                    <textarea name="content" id="chapter_content" rows="5"></textarea>
                </div>
                
                <!-- Image Content Field -->
                <div id="image-content-field" class="chapter-content-field active">
                    <label>Upload Images (Multiple):</label>
                    <input type="file" name="chapter_images[]" id="chapter_images" multiple accept="image/*" required>
                    <div id="image-preview" class="upload-preview">
                        <p>No images selected</p>
                        <ul class="file-list" id="file-list"></ul>
                    </div>
                    <small>Images will be displayed in the order you select them</small>
                </div>
                
                <label>Cover Image (optional):</label>
                <input type="file" name="cover_image" id="cover_image" accept="image/*">
                <img src="" id="cover_image_preview" class="chapter-image-preview" style="display:none;">
                
                <button type="submit" name="addChapter" class="admin-btn">Add Chapter</button>
            </form>
            
        <?php else: ?>
            <!-- Regular Admin Panel View -->
            <h2>All Books</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo $book['id']; ?></td>
                    <td><img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="cover" style="height:60px;"></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo $book['available'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="admin_panel.php?book_id=<?php echo $book['id']; ?>" class="chapter-btn">Chapters</a>
                            <a href="admin_panel.php?edit_book=<?php echo $book['id']; ?>" class="edit-btn">Edit</a>
                            <a href="admin_panel.php?delete=<?php echo $book['id']; ?>" class="delete-btn" onclick="return confirm('Delete this book and all its chapters?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <!-- Add New Book Form -->
            <form class="add-form" method="post" enctype="multipart/form-data">
                <h2>Add New Book</h2>
                <label>Title:</label>
                <input type="text" name="title" required>
                
                <label>Author:</label>
                <input type="text" name="author" required>
                
                <label>Cover Image:</label>
                <input type="file" name="cover" accept="image/*" required>
                <small>Upload the book cover image</small>
                
                <label>Description:</label>
                <textarea name="description" rows="3"></textarea>
                
                <?php if (isset($error)): ?>
                    <p style="color:red;"><?php echo $error; ?></p>
                <?php endif; ?>
                
                <button type="submit" name="addBook" class="admin-btn">Add Book</button>
            </form>

            <!-- Edit Book Form (shown only when editing) -->
            <?php if ($editingBook): ?>
                <form class="add-form" method="post" enctype="multipart/form-data">
                    <h2>Edit Book</h2>
                    <label>Title:</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($editingBook['title']); ?>" required>
                    
                    <label>Author:</label>
                    <input type="text" name="author" value="<?php echo htmlspecialchars($editingBook['author']); ?>" required>
                    
                    <label>Current Cover:</label>
                    <img src="<?php echo htmlspecialchars($editingBook['cover']); ?>" alt="Current cover" style="height:100px; display:block; margin-bottom:10px;">
                    
                    <label>New Cover Image (leave blank to keep current):</label>
                    <input type="file" name="cover" accept="image/*">
                    
                    <label>Description:</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($editingBook['description']); ?></textarea>
                    
                    <label>
                        <input type="checkbox" name="available" <?php echo $editingBook['available'] ? 'checked' : ''; ?>> Available
                    </label>
                    
                    <button type="submit" name="updateBook" class="admin-btn">Update Book</button>
                    <a href="admin_panel.php" class="delete-btn" style="text-decoration:none; padding:10px 20px;">Cancel</a>
                </form>
            <?php endif; ?>

            <h2>Manage Users</h2>
            <table border="1" cellpadding="8" style="border-collapse:collapse; width:100%;">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo isset($user['id']) ? htmlspecialchars($user['id']) : ''; ?></td>
                    <td><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?></td>
                    <td><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="edit-btn" onclick="openUserModal('<?php echo isset($user['id']) ? $user['id'] : ''; ?>', '<?php echo isset($user['username']) ? htmlspecialchars($user['username'], ENT_QUOTES) : ''; ?>', '<?php echo isset($user['email']) ? htmlspecialchars($user['email'], ENT_QUOTES) : ''; ?>'); return false;">
                                Update
                            </a>
                            <a href="admin_panel.php?delete_user=<?php echo isset($user['id']) ? $user['id'] : ''; ?>" class="delete-btn" onclick="return confirm('Delete this user?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- User Modal (hidden by default) -->
    <div id="userModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:8px; width:400px; max-width:90%;">
            <h2>Update User</h2>
            <form method="post" id="userForm">
                <input type="hidden" name="user_id" id="modal_user_id">
                
                <label>Username:</label>
                <input type="text" name="username" id="modal_username" required>
                
                <label>Email:</label>
                <input type="email" name="email" id="modal_email" required>
                
                <label>New Password (leave blank to keep current):</label>
                <input type="password" name="password" id="modal_password">
                
                <div style="margin-top:20px; display:flex; justify-content:space-between;">
                    <button type="button" onclick="document.getElementById('userModal').style.display='none'" class="delete-btn">Cancel</button>
                    <button type="submit" name="update_user" class="edit-btn">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Enhanced image preview for admin panel
        document.getElementById('chapter_images').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('image-preview');
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';
            
            if (this.files && this.files.length > 0) {
                previewContainer.querySelector('p').style.display = 'none';
                
                Array.from(this.files).forEach((file, index) => {
                    if (file.type.match('image.*')) {
                        const listItem = document.createElement('li');
                        listItem.className = 'file-item';
                        
                        const orderBadge = document.createElement('div');
                        orderBadge.className = 'file-order';
                        orderBadge.textContent = `${index + 1}`;
                        
                        const preview = document.createElement('img');
                        preview.className = 'file-thumb';
                        preview.src = URL.createObjectURL(file);
                        
                        const fileName = document.createElement('div');
                        fileName.className = 'file-name';
                        fileName.textContent = file.name;
                        
                        listItem.appendChild(orderBadge);
                        listItem.appendChild(preview);
                        listItem.appendChild(fileName);
                        fileList.appendChild(listItem);
                    }
                });
            } else {
                previewContainer.querySelector('p').style.display = 'block';
            }
        });
        
        // Toggle between text and image fields
        function toggleChapterType() {
            const isImage = document.getElementById('type-image').checked;
            document.getElementById('text-content-field').style.display = isImage ? 'none' : 'block';
            document.getElementById('image-content-field').style.display = isImage ? 'block' : 'none';
            document.getElementById('chapter_content').required = !isImage;
            document.getElementById('chapter_images').required = isImage;
        } 
        
        // User management functions
        function openUserModal(id, username, email) {
            document.getElementById('modal_user_id').value = id;
            document.getElementById('modal_username').value = username;
            document.getElementById('modal_email').value = email;
            document.getElementById('modal_password').value = '';
            document.getElementById('userModal').style.display = 'flex';
        }
        
        // Close modal when clicking outside
        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
        
        // Initialize
        toggleChapterType();
    </script>
</body>
</html>
