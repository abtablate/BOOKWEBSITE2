<?php
// filepath: c:\xampp\htdocs\EnrollmentCollege\signup.php
$conn = new mysqli("localhost", "root", "", "bookwebsite");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // Get the role from the form

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already registered!');window.location.href='signup.html';</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $password, $role);
        if ($stmt->execute()) {
            echo "<script>alert('Sign up successful! Please log in.');window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Error: Could not sign up.');window.location.href='signup.html';</script>";
        }
        $stmt->close();
    }
    $check->close();
}
$conn->close();
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