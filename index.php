<?php
session_start();

// Database connection (use Railway credentials if hosted there)
require "db_connection.php";

// Login process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $user_role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $user_role; // 'admin' or 'user'
            header("Location: 1.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No user found.');</script>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Personal Book Website</title>
  <link rel="stylesheet" href="login.css">
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

    header {
      background: #ffe7b3;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 50px;
      margin-right: 10px;
    }

    .logo h1 {
      font-size: 1.5rem;
      color: #3a2a13;
    }

    .user {
      display: flex;
      align-items: center;
    }

    .user span {
      margin-right: 10px;
      font-weight: 600;
      color: #3a2a13;
    }

    .user-small-img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
    }

    .login-box {
      max-width: 400px;
      margin: 40px auto;
      padding: 20px;
      background: #fff3d1;
      border: 2px solid #9c6b3e;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .login-box h2 {
      text-align: center;
      color: #3a2a13;
    }

    .login-box label {
      display: block;
      margin: 10px 0 5px;
      color: #3a2a13;
      font-weight: 500;
    }

    .login-box input {
      width: 100%;
      padding: 10px;
      border-radius: 7px;
      border: 2px solid #9c6b3e;
      background: #fff6e3;
      color: #3a2a13;
      font-size: 1rem;
      outline: none;
      margin-bottom: 12px;
    }

    .login-box select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 7px;
      border: 2px solid #9c6b3e;
      background: #fff6e3;
      color: #3a2a13;
      font-size: 1rem;
      margin-bottom: 8px;
      appearance: none;
      box-shadow: 0 2px 8px rgba(42, 93, 255, 0.05);
    }

    .login-box select:focus {
      border: 2px solid #7b522e;
      background: #fff3d1;
    }

    .login-box option {
      background: #fff6e3;
      color: #3a2a13;
    }

    .login-box .buttons {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .login-box button {
      flex: 1;
      padding: 10px 0;
      border: none;
      border-radius: 30px;
      background: #9c6b3e;
      color: #fff;
      font-size: 1.08rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
    }

    .login-box button:hover {
      background: #7b522e;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <img src="book.png" alt="Logo">
      <h1>Personal Book Website</h1>
    </div>
    <div class="user">
      <span>User</span>
      <img src="github_logo.png" alt="User Icon" class="user-small-img" />
    </div>
  </header>

  <main>
    <div class="login-box">
      <h2>Log in</h2>
      <form method="post" action="login.php">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="password" required>

        <!-- Optional role dropdown if needed in the future -->
        <!--
        <label for="role">Login as:</label>
        <select id="role" name="role" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
        -->

        <div class="buttons">
          <button type="submit" name="login">Log In</button>
          <button type="button" onclick="window.location.href='signup.html'">Sign Up</button>
        </div>
      </form>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div style="margin-top: 15px; text-align: center;">
          <a href="admin_panel.php">Go to Admin Panel</a>
        </div>
      <?php endif; ?>
    </div>
  </main>

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
  </script>
</body>
</html>
