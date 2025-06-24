<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Initialize variables
$show_otp_form = false;
$password_error = '';
$success_message = '';
$error_message = '';

// Handle password change request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: User requests password change
    if (isset($_POST['request_password_change'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "All fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "New passwords do not match.";
        } elseif (strlen($new_password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } else {
            // Verify current password
            $user_id = $_SESSION['user_id'];
            $result = $conn->query("SELECT password FROM users WHERE id = $user_id");
            $user = $result->fetch_assoc();
            
            if (password_verify($current_password, $user['password'])) {
                // Current password is correct, generate OTP
                $otp = rand(100000, 999999);
                $_SESSION['password_change_otp'] = [
                    'otp' => $otp,
                    'new_password' => password_hash($new_password, PASSWORD_BCRYPT)
                ];
                
                // Get user email
                $email_result = $conn->query("SELECT email FROM users WHERE id = $user_id");
                $email_data = $email_result->fetch_assoc();
                $email = $email_data['email'];
                
                // Send OTP to email (in production, this would actually send an email)
                $to = $email;
                $subject = "Password Change Verification";
                $message = "Your OTP for password change is: $otp";
                $headers = "From: noreply@yourdomain.com";
                @mail($to, $subject, $message, $headers);
                
                $show_otp_form = true;
                $success_message = "OTP has been sent to your email address.";
                
                // For development - show OTP on screen
                $dev_otp_message = "<div style='color:#b00;text-align:center;'>[DEV ONLY] Your OTP is: <b>$otp</b></div>";
            } else {
                $error_message = "Current password is incorrect.";
            }
        }
    }
    // Step 2: User submits OTP
    elseif (isset($_POST['verify_password_otp'])) {
        $entered_otp = $_POST['otp'];
        $stored_otp = $_SESSION['password_change_otp']['otp'] ?? null;
        
        if ($entered_otp == $stored_otp) {
            // OTP is correct, update password
            $new_password_hash = $_SESSION['password_change_otp']['new_password'];
            $user_id = $_SESSION['user_id'];
            
            $conn->query("UPDATE users SET password = '$new_password_hash' WHERE id = $user_id");
            
            unset($_SESSION['password_change_otp']);
            $success_message = "Password changed successfully!";
        } else {
            $show_otp_form = true;
            $error_message = "Invalid OTP. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
    .password-container { 
        max-width: 500px; 
        margin: 40px auto; 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        padding: 32px; 
    }
    .password-title {
        font-size: 1.5em;
        color: #9c6b3e;
        margin-bottom: 25px;
        text-align: center;
    }
    label { 
        display: block; 
        margin-top: 15px; 
        color: #555; 
        font-size: 0.95em; 
        font-weight: 500; 
    }
    input { 
        width: 100%; 
        padding: 12px 15px; 
        border-radius: 8px; 
        border: 1px solid #ddd; 
        margin-top: 8px; 
        font-size: 1em; 
        transition: border 0.3s;
    }
    input:focus {
        border-color: #9c6b3e;
        outline: none;
    }
    .buttons { 
        margin-top: 30px; 
        display: flex; 
        justify-content: space-between; 
    }
    button { 
        padding: 12px 24px; 
        border: none; 
        border-radius: 8px; 
        background: #9c6b3e; 
        color: #fff; 
        font-weight: 600; 
        font-size: 1em; 
        cursor: pointer; 
        transition: background 0.3s;
    }
    button:hover { 
        background: #7b522e; 
    }
    .cancel-btn {
        background: #f0f0f0;
        color: #555;
    }
    .cancel-btn:hover {
        background: #e0e0e0;
    }
    .success { 
        color: #28a745; 
        margin-bottom: 16px; 
        text-align: center; 
        padding: 10px;
        background: #e8f5e9;
        border-radius: 5px;
    }
    .error { 
        color: #dc3545; 
        margin-bottom: 16px; 
        text-align: center; 
        padding: 10px;
        background: #f8e8e8;
        border-radius: 5px;
    }
    .hidden {
        display: none;
    }
  </style>
</head>
<body>
  <div class="password-container">
    <h2 class="password-title">Change Password</h2>
    
    <?php if ($success_message): ?>
      <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
      <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($dev_otp_message)) echo $dev_otp_message; ?>

    <?php if ($show_otp_form): ?>
      <!-- OTP Verification Form -->
      <form method="post">
        <div class="form-group">
          <label>Enter the OTP sent to your email:</label>
          <input type="text" name="otp" placeholder="6-digit OTP" required maxlength="6" pattern="\d{6}">
        </div>
        <div class="buttons">
          <button type="submit" name="verify_password_otp">Verify OTP</button>
          <button type="button" class="cancel-btn" onclick="window.location.href='user.php'">Cancel</button>
        </div>
      </form>
    <?php else: ?>
      <!-- Password Change Form -->
      <form method="post">
        <div class="form-group">
          <label>Current Password:</label>
          <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
          <label>New Password:</label>
          <input type="password" name="new_password" required minlength="8">
        </div>
        <div class="form-group">
          <label>Confirm New Password:</label>
          <input type="password" name="confirm_password" required minlength="8">
        </div>
        <div class="buttons">
          <button type="submit" name="request_password_change">Change Password</button>
          <button type="button" class="cancel-btn" onclick="window.location.href='settings.php'">Cancel</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>