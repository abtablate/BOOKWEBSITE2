<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Fetch user info
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Initialize variables
$show_otp_form = false;
$pending_email = '';
$pending_username = '';
$otp_error = '';
$success_message = '';
$error_message = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $target_dir = "uploads/profile_pics/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
    $new_filename = "user_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Update database with new profile picture path
            $conn->query("UPDATE users SET profile_pic='$target_file' WHERE id=$user_id");
            $success_message = "Profile picture updated successfully!";
            $user['profile_pic'] = $target_file; // Update local user data
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error_message = "File is not an image.";
    }
}

// Handle other profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // When Save is clicked (profile form)
    if (isset($_POST['start_verification'])) {
        // Step 1: User submits new info, send OTP
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $display_name = $conn->real_escape_string($_POST['display_name']);
        $bio = $conn->real_escape_string($_POST['bio']);
        $otp = rand(100000, 999999);

        // Store pending info and OTP in session
        $_SESSION['pending_profile'] = [
            'username' => $username,
            'email' => $email,
            'display_name' => $display_name,
            'bio' => $bio,
            'otp' => $otp
        ];

        // Send OTP to email (will fail on localhost, so show on screen)
        $to = $email;
        $subject = "Your OTP Verification Code";
        $message = "Your OTP code is: $otp";
        $headers = "From: noreply@yourdomain.com";
        @mail($to, $subject, $message, $headers); // Suppress warning on localhost

        $show_otp_form = true;
        $pending_email = $email;

        // For local development, show OTP on screen
        $dev_otp_message = "<div style='color:#b00;text-align:center;'>[DEV ONLY] Your OTP is: <b>$otp</b></div>";
    }
    // When Verify OTP is clicked (OTP form)
    elseif (isset($_POST['verify_otp'])) {
        // Step 2: User submits OTP
        $entered_otp = $_POST['otp'];
        $pending = $_SESSION['pending_profile'] ?? null;
        if ($pending && $entered_otp == $pending['otp']) {
            // OTP correct, update user
            $username = $pending['username'];
            $email = $pending['email'];
            $display_name = $pending['display_name'];
            $bio = $pending['bio'];
            
            $conn->query("UPDATE users SET 
                username='$username', 
                email='$email',
                display_name='$display_name',
                bio='$bio'
                WHERE id=$user_id");
                
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            unset($_SESSION['pending_profile']);
            $success_message = "Profile updated successfully!";
        } else {
            $show_otp_form = true;
            $otp_error = "Invalid OTP. Please try again.";
            $pending_email = $pending['email'] ?? '';
            $pending_username = $pending['username'] ?? '';
        }
    }
    // Handle Change Password button click
    elseif (isset($_POST['change_password'])) {
        header("Location: change_password.php");
        exit();
    }
    // Handle Delete Account button click
    elseif (isset($_POST['delete_account'])) {
        header("Location: delete_account.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
    .profile-container { 
        max-width: 600px; 
        margin: 40px auto; 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        padding: 32px; 
        position: relative;
    }
    .profile-header { 
        display: flex; 
        align-items: center; 
        margin-bottom: 30px; 
        padding-bottom: 20px; 
        border-bottom: 1px solid #eee;
    }
    .profile-pic-container {
        position: relative;
        width: 100px;
        height: 100px;
        margin-right: 20px;
    }
    .profile-pic {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #f0f0f0;
    }
    .upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #9c6b3e;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .profile-info {
        flex-grow: 1;
    }
    .display-name {
        font-size: 1.5em;
        font-weight: bold;
        margin: 0;
        color: #333;
    }
    .username {
        color: #777;
        margin: 5px 0;
    }
    .bio {
        color: #555;
        margin: 10px 0 0;
        font-size: 0.9em;
    }
    .settings-section {
        margin-bottom: 25px;
    }
    .settings-title {
        font-size: 1.2em;
        color: #9c6b3e;
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
    label { 
        display: block; 
        margin-top: 15px; 
        color: #555; 
        font-size: 0.95em; 
        font-weight: 500; 
    }
    input, textarea { 
        width: 100%; 
        padding: 12px 15px; 
        border-radius: 8px; 
        border: 1px solid #ddd; 
        margin-top: 8px; 
        font-size: 1em; 
        transition: border 0.3s;
    }
    input:focus, textarea:focus {
        border-color: #9c6b3e;
        outline: none;
    }
    textarea {
        min-height: 80px;
        resize: vertical;
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
    .logout-btn {
        background: #f5c748;
        color: #7b522e;
    }
    .logout-btn:hover {
        background: #e0b63a;
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
    .tab-container {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .tab {
        padding: 10px 20px;
        cursor: pointer;
        color: #777;
        font-weight: 500;
    }
    .tab.active {
        color: #9c6b3e;
        border-bottom: 2px solid #9c6b3e;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
  </style>
</head>
<body>
  <div class="profile-container">
    <?php if ($success_message): ?>
      <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
      <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="profile-header">
      <div class="profile-pic-container">
        <img src="<?php echo isset($user['profile_pic']) ? $user['profile_pic'] : 'https://via.placeholder.com/100'; ?>" class="profile-pic" id="profilePicPreview">
        <form id="profilePicForm" method="post" enctype="multipart/form-data" class="hidden">
          <input type="file" name="profile_pic" id="profilePicInput" accept="image/*">
        </form>
        <div class="upload-btn" onclick="document.getElementById('profilePicInput').click()">
          <i>+</i>
        </div>
      </div>
      <div class="profile-info">
        <h2 class="display-name"><?php echo htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'User'); ?></h2>
        <div class="username">@<?php echo htmlspecialchars($user['username'] ?? 'username'); ?></div>
        <p class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'No bio yet'); ?></p>
      </div>
    </div>

    <div class="tab-container">
      <div class="tab active" onclick="switchTab('profile')">Profile</div>
      <div class="tab" onclick="switchTab('settings')">Settings</div>
      <div class="tab" onclick="switchTab('privacy')">Privacy</div>
    </div>

    <div class="tab-content active" id="profileTab">
      <form method="post" action="logout.php" style="text-align:center; margin-bottom:30px;">
        <button type="submit" class="logout-btn" name="logoutBtn">
          Log Out
        </button>
      </form>
    </div>

    <div class="tab-content" id="settingsTab">
      <?php if ($show_otp_form): ?>
        <?php if (!empty($dev_otp_message)) echo $dev_otp_message; ?>
        <?php if ($otp_error): ?>
          <div class="error"><?php echo $otp_error; ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
          <div class="settings-section">
            <div class="settings-title">Verify Your Changes</div>
            <label>Enter the OTP sent to <br><b><?php echo htmlspecialchars($pending_email); ?></b>:</label>
            <input
              type="text"
              name="otp"
              placeholder="Enter 6-digit OTP"
              required
              maxlength="6"
              pattern="\d{6}"
            >
          </div>
          <div class="buttons">
            <button type="submit" name="verify_otp">Verify OTP</button>
            <button type="button" class="cancel-btn" onclick="window.location.href='user.php'">Cancel</button>
          </div>
        </form>
      <?php else: ?>
        <!-- Profile form (shows Save button) -->
        <form method="post" autocomplete="off" enctype="multipart/form-data">
          <div class="settings-section">
            <div class="settings-title">Account Information</div>
            <label>Username:
              <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            </label>
            <label>Display Name:
              <input type="text" name="display_name" placeholder="How your name appears" value="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>">
            </label>
            <label>Email:
              <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </label>
            <label>Bio:
              <textarea name="bio" placeholder="Tell us about yourself"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </label>
          </div>

          <div class="settings-section">
            <div class="settings-title">Profile Picture</div>
            <label>
              Upload new profile picture:
              <input type="file" name="profile_pic" accept="image/*">
            </label>
          </div>

          <div class="buttons">
            <button type="submit" name="start_verification">Save Changes</button>
            <button type="button" class="cancel-btn" onclick="window.location.href='1.php'">Cancel</button>
          </div>
        </form>
      <?php endif; ?>
    </div>

    <div class="tab-content" id="privacyTab">
      <div class="settings-section">
        <div class="settings-title">Privacy Settings</div>
        <label style="display: flex; align-items: center;">
          <input type="checkbox" name="private_account" style="width: auto; margin-right: 10px;">
          Make my account private
        </label>
        <label style="display: flex; align-items: center;">
          <input type="checkbox" name="hide_email" style="width: auto; margin-right: 10px;">
          Hide my email from other users
        </label>
      </div>
      <div class="settings-section">
        <div class="settings-title">Security</div>
        <form method="post">
          <button type="submit" name="change_password" class="cancel-btn" style="width: 100%; margin-bottom: 10px;">
            Change Password
          </button>
        </form>
        <form method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
          <button type="submit" name="delete_account" class="cancel-btn" style="width: 100%; background: #f8d7da; color: #721c24;">
            Delete Account
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Profile picture preview
    document.getElementById('profilePicInput').addEventListener('change', function(e) {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('profilePicPreview').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
        document.getElementById('profilePicForm').submit();
      }
    });

    // Tab switching
    function switchTab(tabName) {
      // Hide all tab contents and remove active class from tabs
      document.querySelectorAll('.tab-content').forEach(function(content) {
        content.classList.remove('active');
      });
      document.querySelectorAll('.tab').forEach(function(tab) {
        tab.classList.remove('active');
      });

      // Show selected tab content and mark tab as active
      document.getElementById(tabName + 'Tab').classList.add('active');
      event.currentTarget.classList.add('active');
    }

    // Page transition for links
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
