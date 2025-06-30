<?php
// signup.php
session_start();

$host = '127.0.0.1';
$dbname = 'bookwebsite';
$username = 'root';
$password = ''; // Replace with your actual MySQL password if any

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['signup'])) {
        $email = $_POST['email'];
        $plain_password = $_POST['password'];
        $role = $_POST['role'];

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email already registered!'); window.location.href='signup.html';</script>";
        } else {
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $hashed_password, $role]);

            echo "<script>alert('Sign up successful! Please log in.'); window.location.href='login.html';</script>";
        }
    }
} catch (PDOException $e) {
    echo "<script>alert('Database connection failed: " . $e->getMessage() . "');</script>";
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
