<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookwebsite");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

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
    // Use password_verify if passwords are hashed, otherwise use ($password == $hashed_password)
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $user_role; // 'admin' or 'user'
        header("Location: 1.php");
        exit();
    } else {
        echo "Invalid password.";
    }
} else {
    echo "No user found.";
}
$stmt->close();
$conn->close();
?>

<!-- Add this HTML code where the form is located -->
<select id="role" name="role" required>
  <option value="user">User</option>
  <option value="admin">Admin</option>
</select>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
  <a href="admin_panel.php">Admin</a>
<?php endif; ?>

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