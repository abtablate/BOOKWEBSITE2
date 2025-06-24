<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Initialize variables
$show_otp_form = false;
$error_message = '';
$success_message = '';

// Handle account deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: User requests account deletion
    if (isset($_POST['request_account_deletion'])) {
        // Verify password first
        $password = $_POST['password'];
        $user_id = $_SESSION['user_id'];
        
        $result = $conn->query("SELECT password FROM users WHERE id = $user_id");
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Password correct, generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['account_deletion_otp'] = $otp;
            
            // Get user email
            $email_result = $conn->query("SELECT email FROM users WHERE id = $user_id");
            $email_data = $email_result->fetch_assoc();
            $email = $email_data['email'];
            
            // Send OTP to email
            $to = $email;
            $subject = "Account Deletion Verification";
            $message = "Your OTP for account deletion is: $otp";
            $headers = "From: noreply@yourdomain.com";
            @mail($to, $subject, $message, $headers);
            
            $show_otp_form = true;
            $success_message = "OTP has been sent to your email address.";
            
            // For development - show OTP on screen
            $dev_otp_message = "<div style='color:#b00;text-align:center;'>[DEV ONLY] Your OTP is: <b>$otp</b></div>";
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    }
    // Step 2: User verifies OTP
    elseif (isset($_POST['verify_deletion_otp'])) {
        $entered_otp = $_POST['otp'];
        $stored_otp = $_SESSION['account_deletion_otp'] ?? null;
        
        if ($entered_otp == $stored_otp) {
            // OTP correct, delete account
            $user_id = $_SESSION['user_id'];
            
            // Delete user from database
            $conn->query("DELETE FROM users WHERE id = $user_id");
            
            // Clear session and redirect to login
            session_unset();
            session_destroy();
            header("Location: login.html?account_deleted=1");
            exit();
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
  <title>Delete Account</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
    .delete-container { 
        max-width: 500px; 
        margin: 40px auto; 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        padding: 32px; 
    }
    .delete-title {
        font-size: 1.5em;
        color: #721c24;
        margin-bottom: 25px;
        text-align: center;
    }
    .warning-box {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #f5c6cb;
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
    .delete-btn { 
        padding: 12px 24px; 
        border: none; 
        border-radius: 8px; 
        background: #dc3545; 
        color: #fff; 
        font-weight: 600; 
        font-size: 1em; 
        cursor: pointer; 
        transition: background 0.3s;
    }
    .delete-btn:hover { 
        background: #c82333; 
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
  <div class="delete-container">
    <h2 class="delete-title">Delete Your Account</h2>
    
    <div class="warning-box">
      <strong>Warning:</strong> This action is permanent and cannot be undone. All your data will be permanently deleted.
    </div>
    
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
          <button type="submit" name="verify_deletion_otp" class="delete-btn">Verify & Delete Account</button>
          <button type="button" class="cancel-btn" onclick="window.location.href='user.php'">Cancel</button>
        </div>
      </form>
    <?php else: ?>
      <!-- Account Deletion Form -->
      <form method="post">
        <div class="form-group">
          <label>Enter your password to confirm:</label>
          <input type="password" name="password" required>
        </div>
        <div class="buttons">
          <button type="submit" name="request_account_deletion" class="delete-btn">Delete My Account</button>
          <button type="button" class="cancel-btn" onclick="window.location.href='user.php'">Cancel</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>