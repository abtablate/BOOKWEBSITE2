<?php
session_start();

// Database credentials
$host = '127.0.0.1';
$dbname = 'bookwebsite';
$db_user = 'root';
$db_pass = ''; // Replace with your real password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
        // Sanitize and validate input
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $plain_password = trim($_POST['password']);
        $role = trim($_POST['role']);

        if (!$email || empty($plain_password) || !in_array($role, ['admin', 'user'])) {
            echo "<script>alert('Invalid input.'); window.location.href='signup.html';</script>";
            exit();
        }

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email already registered!'); window.location.href='signup.html';</script>";
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashed_password, $role]);

        echo "<script>alert('Sign up successful! Please log in.'); window.location.href='login.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "<script>alert('Database error: " . addslashes($e->getMessage()) . "'); window.location.href='signup.html';</script>";
    exit();
}
?>



<!-- Add this HTML code where the signup form is located -->
<select id="role" name="role" required>
  <option value="user">User</option>
  <option value="admin">Admin</option>
</select>

<style>
body {
  transition: opacity 0.5s;
}
body.fade-out {
  opacity: 0;
}
</style>

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
