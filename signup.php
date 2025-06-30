<?php
session_start();

// Use Railway-provided credentials
$host = 'mysql.railway.internal';
$dbname = 'railway';
$db_user = 'root';
$db_pass = 'kUkYvcPXnXvcQdwiTUPAQLmgIiwnFvfC'; // Replace with environment variable for security
$db_port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$db_port;dbname=$dbname;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $plain_password = trim($_POST['password']);
        $role = trim($_POST['role']);

        if (!$email || empty($plain_password) || !in_array($role, ['admin', 'user'])) {
            echo "<script>alert('Invalid input.'); window.location.href='signup.html';</script>";
            exit();
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email already registered!'); window.location.href='signup.html';</script>";
            exit();
        }

        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashed_password, $role]);

        echo "<script>alert('Signup successful! Please log in.'); window.location.href='index.php';</script>";
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
