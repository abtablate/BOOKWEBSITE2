<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Fetch user info including profile picture
$user = null;
$profile_pic = 'profile.jpg'; // Default profile picture
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $res = $conn->query("SELECT username, email, profile_pic FROM users WHERE id=$uid");
    if ($row = $res->fetch_assoc()) {
        $user = $row;
        // Use the profile pic from database if it exists, otherwise use default
        $profile_pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'profile.jpg';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile</title>
  <link rel="stylesheet" href="haha.css" />
  <style>
    body {
      transition: opacity 0.5s;
      background: #fff8ef;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    body.fade-out {
      opacity: 0;
    }
    .header {
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
      height: 40px;
      margin: 18px auto 0 auto;
      background: #f5c748;
      color: #333;
      border: none;
      border-radius: 22px;
      font-size: 1.05rem;
      font-family: inherit;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
      text-align: center;
      display: block;
    }
    .logout:hover {
      background: #e6b800;
      color: #fff;
    }
    .main-content {
      flex: 1;
      padding: 40px 60px;
      display: flex;
      flex-direction: column;
      align-items: center;
      background: #fffbe9;
      min-height: 100vh;
    }
    .user-profile-tag {
      margin-top: 40px;
      text-align: center;
    }
    .user-profile-tag h2 {
      font-size: 2.2rem;
      font-weight: 700;
      letter-spacing: 2px;
      color: #444;
      margin-bottom: 10px;
    }
    .user-profile-tag .username {
      font-size: 1.2rem;
      color: #888;
      margin-bottom: 24px;
      display: block;
    }
    .user-profile-tag .email {
      font-size: 1.1rem;
      color: #555;
      margin-bottom: 24px;
      display: block;
    }
    .user-profile-tag a {
      color: #9c6b3e;
      text-decoration: underline;
      font-weight: 500;
    }
    .user-profile-tag a:hover {
      color: #7b522e;
    }
    .recent-book {
      background: #fff7e6;
      border-radius: 12px;
      padding: 12px 18px;
      width: 180px;
      text-align: center;
      box-shadow: 0 2px 8px #0001;
      transition: transform 0.2s;
    }
    .recent-book:hover {
      transform: translateY(-5px);
    }
    .recent-book img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 10px;
    }
    .recent-book-title {
      font-weight: bold;
      color: #222;
    }
    .recent-book-author {
      font-size: 0.95em;
      color: #7b522e;
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo-bar">
      <div class="logo-left">
        <img src="book.png" alt="Logo" class="logo-img" />
        <h1>Personal Book Website</h1>
      </div>
      <div class="topuser">
        <span>
          <strong>
            <?php echo $user ? htmlspecialchars($user['username']) : "Guest"; ?>
          </strong>
        </span>
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="User Icon" class="user-small-img" />
      </div>
    </div>
  </header>
  <div class="container">
    <aside class="sidebar">
      <div class="profile-section">
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="User Icon" class="user-img" />
        <p>
          <strong><?php echo $user ? htmlspecialchars($user['username']) : "Guest"; ?></strong>
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
      <section class="user-profile-tag">
        <h2>USER TAG</h2>
        <span class="username">
          @<?php echo $user ? htmlspecialchars($user['username']) : "Guest"; ?>
          <?php if (isset($_SESSION['role'])): ?>
            <span style="font-size:0.95em;color:#9c6b3e;">
              (<?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?>)
            </span>
          <?php endif; ?>
        </span>
        <?php if ($user): ?>
          <span class="email"><?php echo htmlspecialchars($user['email']); ?></span>
          <a href="settings.php">Edit Profile</a>
          <!-- Recently Read Books Section -->
          <div id="recently-read" style="margin-top:32px;">
            <h3 style="color:#7b522e;">Recently Read Books</h3>
            <div style="display:flex;flex-wrap:wrap;gap:18px;justify-content:center;">
              <?php
              // Fetch recently read books for the user
              $recentBooks = [];
              if (isset($_SESSION['user_id'])) {
                  $uid = intval($_SESSION['user_id']);
                  $recentRes = $conn->query(
                      "SELECT b.id, b.title, b.author, b.cover 
                       FROM recently_read rr 
                       JOIN books b ON rr.book_id = b.id 
                       WHERE rr.user_id = $uid 
                       ORDER BY rr.read_at DESC 
                       LIMIT 6"
                  );
                  if ($recentRes && $recentRes->num_rows > 0) {
                      while ($row = $recentRes->fetch_assoc()) {
                          $recentBooks[] = $row;
                      }
                  }
              }
              if (count($recentBooks) > 0):
                foreach ($recentBooks as $book): ?>
                  <div class="recent-book">
                    <a href="book.php?id=<?php echo urlencode($book['id']); ?>" style="display:block;text-decoration:none;">
                      <img 
                        src="<?php echo htmlspecialchars($book['cover']); ?>" 
                        alt="Book Cover" 
                      >
                      <div class="recent-book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                      <div class="recent-book-author"><?php echo htmlspecialchars($book['author']); ?></div>
                    </a>
                  </div>
                <?php endforeach;
              else: ?>
                <div style="color:#999;">No recently read books.</div>
              <?php endif; ?>
            </div>
          </div>
          <!-- End Recently Read Books -->
        <?php else: ?>
          <p>You are not logged in.</p>
        <?php endif; ?>
        <div style="margin-top:40px;">
          <a href="1.php">Back to Home</a>
        </div>
      </section>
    </main>
  </div>
  <script>
    document.querySelectorAll('a').forEach(function(link) {
      if (link.hostname === window.location.hostname && link.target !== "_blank" && !link.href.startsWith('javascript:')) {
        link.addEventListener('click', function(e) {
          if (link.hash && link.pathname === window.location.pathname) return;
          e.preventDefault();
          document.body.classList.add('fade-out');
          setTimeout(function() {
            window.location = link.href;
          }, 500);
        });
      }
    });
    document.querySelectorAll('.nav-buttons form').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        document.body.classList.add('fade-out');
      });
    });
    window.onload = function() {
      var recent = document.getElementById('recently-read');
      if (recent) {
        recent.scrollIntoView({ behavior: "smooth" });
      }
    };
  </script>
</body>
</html>