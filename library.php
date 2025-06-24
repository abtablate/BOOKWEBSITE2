<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookwebsite");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all books
$books = [];
$result = $conn->query("SELECT * FROM books");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// Fetch user data from users table
$username = '';
$profile_pic = 'profile.jpg'; // default image
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $res = $conn->query("SELECT username, profile_pic FROM users WHERE id=$uid");
    if ($row = $res->fetch_assoc()) {
        $username = $row['username'];
        // Use the profile pic from database if it exists, otherwise use default
        $profile_pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'profile.jpg';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Library</title>
  <link rel="stylesheet" href="haha.css" />
  <style>
    body {
      background: #fff8ef;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      transition: opacity 0.5s;
    }
    body.fade-out {
      opacity: 0;
    }
    header.header {
      background: #ffe7b3;
      box-shadow: 0 2px 12px #e0c9a6;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 80px;
      padding: 0;
    }
    .logo-bar {
      display: flex;
      align-items: center;
      width: 100%;
      justify-content: space-between;
      padding: 0 40px;
    }
    .logo-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .logo-img {
      height: 48px;
    }
    .logo-bar h1 {
      font-size: 1.6rem;
      color: #444;
      font-family: inherit;
      font-weight: 700;
      margin: 0;
    }
    .topuser {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: bold;
      color: #9c6b3e;
    }
    .topuser strong {
      font-size: 1.08rem;
      color: #222;
    }
    .user-small-img {
      height: 32px;
      width: 32px;
      border-radius: 50%;
      object-fit: cover;
    }
    .container {
      display: flex;
      margin: 32px auto;
      max-width: 1200px;
    }
    .sidebar {
      background: #fdf1d6;
      width: 220px;
      min-height: 100vh;
      padding-top: 32px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .profile-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 18px;
    }
    .user-img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 8px;
      object-fit: cover;
      background: #fff;
    }
    .profile-section p {
      margin: 0;
      font-size: 1.08rem;
      color: #222;
      font-weight: 500;
      text-align: center;
    }
    .nav-buttons {
      background: #fff7e6;
      border-radius: 22px;
      padding: 24px 10px 32px 10px;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .main-buttons {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .main-buttons form {
      width: 100%;
    }
    .main-buttons form button {
      width: 90%;
      height: 48px;
      margin: 0 auto 18px auto;
      background: #ffe49c;
      color: #222;
      border: none;
      border-radius: 22px;
      font-size: 1.08rem;
      font-family: inherit;
      font-weight: 700;
      cursor: pointer;
      box-shadow: none;
      text-align: center;
      letter-spacing: 0.2px;
      transition: background 0.2s, color 0.2s;
      outline: none;
      display: block;
    }
    .main-buttons form button:hover,
    .main-buttons form button:focus {
      background: #f5c748;
      color: #7b522e;
    }
    .admin-btn {
      width: 90%;
      height: 48px;
      margin: 0 auto 18px auto;
      background: #9c6b3e;
      color: #fff;
      border: none;
      border-radius: 22px;
      font-size: 1.08rem;
      font-family: inherit;
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      letter-spacing: 0.2px;
      transition: background 0.2s, color 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }
    .admin-btn:hover,
    .admin-btn:focus {
      background: #7b522e;
      color: #fff;
    }
    .logout {
      width: 90%;
      height: 48px;
      margin: 0 auto 0 auto;
      background: #9c6b3e;
      color: #fff;
      border: none;
      border-radius: 22px;
      font-size: 1.08rem;
      font-family: inherit;
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      letter-spacing: 0.2px;
      transition: background 0.2s, color 0.2s;
      display: block;
      line-height: 48px;
      text-decoration: none;
    }
    .logout:hover,
    .logout:focus {
      background: #7b522e;
      color: #fff;
    }
    .main-content {
      flex: 1;
      padding: 24px 0 0 0;
    }
    .library-title {
      font-size: 2em;
      font-weight: bold;
      color: #444;
      margin-bottom: 18px;
      margin-top: 0;
      letter-spacing: 1px;
    }
    .section-title {
      margin-top: 32px;
      margin-bottom: 12px;
      font-size: 1.3em;
      color: #444;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .book-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 32px;
      margin-bottom: 24px;
    }
    .book-card {
      background: #fff8ef;
      border-radius: 18px;
      box-shadow: 0 2px 12px #0001;
      padding: 18px 18px 12px 18px;
      width: 200px;
      min-height: 340px;
      text-align: center;
      transition: box-shadow 0.2s, transform 0.2s;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
    }
    .book-card:hover {
      box-shadow: 0 4px 18px #9c6b3e33;
      transform: translateY(-4px) scale(1.03);
    }
    .book-card img {
      width: 140px;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 12px;
      background: #eee;
      box-shadow: 0 2px 8px #0001;
    }
    .book-card strong {
      font-size: 1.08em;
      color: #9c6b3e;
      font-weight: bold;
      display: block;
      margin-bottom: 2px;
      margin-top: 8px;
    }
    .book-card p {
      margin: 4px 0 0 0;
      color: #444;
      font-size: 1em;
    }
    .empty-msg {
      color: #aaa;
      font-style: italic;
      margin: 0 0 16px 8px;
    }
    @media (max-width: 900px) {
      .container { flex-direction: column; }
      .sidebar { width: 100%; margin-right: 0; margin-bottom: 24px; }
      .main-content { padding: 0; }
      .book-cards { justify-content: center; }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo-bar">
      <div class="logo-left">
        <img src="book.png" alt="Logo" class="logo-img" />
        <h1 style="font-family:'Segoe UI',Arial,sans-serif;font-weight:bold;letter-spacing:1px;">Personal Book Website</h1>
      </div>
      <div class="topuser">
        <span>
          <strong>
            <?php echo $username ? htmlspecialchars($username) : 'Set your username'; ?>
          </strong>
        </span>
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="User Icon" class="user-small-img" />
      </div>
    </div>
    <!-- Back to Home Button -->
    <div style="text-align:right; max-width:1200px; margin:0 auto;">
      <form action="1.php" method="get" style="display:inline;">
        <button type="submit" style="margin:12px 0 0 0; padding:8px 22px; border-radius:12px; border:none; background:#9c6b3e; color:#fff; font-weight:bold; font-size:1em; cursor:pointer;">
          Back to Home
        </button>
      </form>
    </div>
  </header>

  <div class="container">
    <aside class="sidebar">
      <div class="profile-section">
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="User Icon" class="user-img" />
        <p>
          <strong><?php echo htmlspecialchars($username); ?></strong>
          <br>
          <span style="font-size:0.95em;color:#9c6b3e;">
            <?php echo isset($_SESSION['role']) ? ucfirst(htmlspecialchars($_SESSION['role'])) : 'User'; ?>
          </span>
        </p>
      </div>
      <div class="nav-buttons">
        <div class="main-buttons">
          <form method="get" action="user_profile.php">
            <button type="submit" style="background:#f5c748;color:#7b522e;">User</button>
          </form>
          <form method="post" action="library.php">
            <button type="submit">Library</button>
          </form>
          <form method="post" action="settings.php">
            <button type="submit">Settings</button>
          </form>
        </div>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="admin_panel.php" class="admin-btn">Admin Panel</a>
        <?php endif; ?>
        <form method="post" action="logout.php">
          <button type="submit" class="logout" name="logoutBtn">Log Out</button>
        </form>
      </div>
    </aside>
    <main class="main-content">
      <h2 class="library-title">Library</h2>
      <section>
        <h3 class="section-title">All Books</h3>
        <div class="book-cards">
          <?php foreach ($books as $book): ?>
            <div class="book-card">
              <a href="book.php?id=<?php echo $book['id']; ?>">
                <img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" />
              </a>
              <strong><?php echo htmlspecialchars($book['title']); ?></strong>
              <p><?php echo htmlspecialchars($book['author']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </main>
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