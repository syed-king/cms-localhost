<?php
include 'db.php';

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// --- FETCH USER DATA + DEPARTMENT NAME ---
$u_sql = "SELECT users.*, departments.name as dept_name 
          FROM users 
          LEFT JOIN departments ON users.dept_id = departments.id 
          WHERE users.id=$user_id";
$u_res = $conn->query($u_sql);
$user = $u_res->fetch_assoc();

// --- 1. HANDLE PROFILE PICTURE UPLOAD ---
if (isset($_POST['upload_pic'])) {
    if (!empty($_FILES['profile_pic']['name'])) {
        $file_name = time() . '_' . $_FILES['profile_pic']['name'];
        $target = "uploads/" . $file_name;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
            $conn->query("UPDATE users SET profile_pic='$target' WHERE id=$user_id");
            header("Refresh:0"); 
        } else {
            $msg = "<div style='color:#ef4444; margin-bottom:15px;'>Error uploading file.</div>";
        }
    }
}

// --- 2. HANDLE EMAIL & PHONE UPDATE ---
if (isset($_POST['update_details'])) {
    $new_email = $conn->real_escape_string($_POST['email']);
    $new_phone = $conn->real_escape_string($_POST['phone']);
    $verify_pass = $_POST['verify_pass'];

    // Check Password
    if ($verify_pass == $user['password']) {
        $sql = "UPDATE users SET email='$new_email', phone='$new_phone' WHERE id=$user_id";
        if ($conn->query($sql)) {
            $msg = "<div style='background:rgba(16, 185, 129, 0.2); color:#10b981; padding:10px; border-radius:8px; margin-bottom:15px;'>Contact details updated successfully!</div>";
            // Refresh data
            $u_res = $conn->query($u_sql);
            $user = $u_res->fetch_assoc();
        } else {
            $msg = "<div style='color:red;'>Error updating details.</div>";
        }
    } else {
        $msg = "<div style='background:rgba(239, 68, 68, 0.2); color:#ef4444; padding:10px; border-radius:8px; margin-bottom:15px;'>Incorrect Password! Changes not saved.</div>";
    }
}

// --- 3. HANDLE PASSWORD CHANGE ---
if (isset($_POST['change_pass'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($old_pass != $user['password']) {
        $msg = "<div style='color:#ef4444; margin-bottom:15px;'>Current password is incorrect.</div>";
    } elseif ($new_pass != $confirm_pass) {
        $msg = "<div style='color:#ef4444; margin-bottom:15px;'>New passwords do not match.</div>";
    } else {
        $conn->query("UPDATE users SET password='$new_pass' WHERE id=$user_id");
        $msg = "<div style='color:#10b981; margin-bottom:15px;'>Password changed successfully!</div>";
    }
}

// Profile Pic Logic
$profile_src = !empty($user['profile_pic']) ? $user['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($user['name'])."&background=random&size=128";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .password-wrapper { position: relative; width: 100%; }
        .password-wrapper input { width: 100%; padding-right: 40px; }
        .toggle-password {
            position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: rgba(255, 255, 255, 0.5); transition: color 0.3s;
        }
        .toggle-password:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px;">SUPER TECH</h2>
            <nav>
                <?php 
                if($_SESSION['role'] == 'admin') echo '<a href="admin_dashboard.php" class="nav-item">← Back to Dashboard</a>';
                elseif($_SESSION['role'] == 'teacher') echo '<a href="teacher_dashboard.php" class="nav-item">← Back to Dashboard</a>';
                else echo '<a href="student_dashboard.php" class="nav-item">← Back to Dashboard</a>';
                ?>
                <a href="settings.php" class="nav-item active">Settings</a>
            </nav>
            <div style="margin-top: auto;">
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <h1>Account Settings</h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Manage your profile and security.</p>
            
            <?php echo $msg; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                
                <div class="tech-card">
                    <h3 style="margin-bottom: 20px;">Profile Picture</h3>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="<?php echo $profile_src; ?>" style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid #3b82f6; object-fit: cover;">
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="input-group">
                            <label>Upload New Photo</label>
                            <input type="file" name="profile_pic" required style="color: white;">
                        </div>
                        <button type="submit" name="upload_pic" class="btn-tech" style="width:100%;">Update Photo</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="margin-bottom: 20px;">User Profile</h3>
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" value="<?php echo $user['name']; ?>" disabled style="opacity:0.6; cursor: not-allowed;">
                    </div>
                    <div class="input-group">
                        <label>Role</label>
                        <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled style="opacity:0.6; cursor: not-allowed;">
                    </div>
                    <div class="input-group">
                        <label>Department</label>
                        <input type="text" value="<?php echo $user['dept_name'] ? $user['dept_name'] : '-'; ?>" disabled style="opacity:0.6; cursor: not-allowed;">
                    </div>
                </div>

                <div class="tech-card">
                    <h3 style="margin-bottom: 20px; color: #3b82f6;">Contact Settings</h3>
                    <form method="POST">
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo $user['email']; ?>" required 
                                   style="border: 1px solid #3b82f6; background: rgba(59, 130, 246, 0.1);">
                        </div>
                        <div class="input-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?php echo isset($user['phone']) ? $user['phone'] : ''; ?>" required
                                   style="border: 1px solid #3b82f6; background: rgba(59, 130, 246, 0.1);">
                        </div>

                        <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin-top: 15px;">
                            <label style="color: #f59e0b; font-size: 0.85rem; margin-bottom: 5px; display: block;">
                                🔒 Verify password to save changes:
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="verify_pass" id="verify_pass" required placeholder="Enter Current Password">
                                <i class="fa fa-eye toggle-password" onclick="togglePass('verify_pass', this)"></i>
                            </div>
                        </div>

                        <button type="submit" name="update_details" class="btn-tech" style="width:100%; margin-top: 15px;">Save Contact Info</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="margin-bottom: 20px; color: #f59e0b;">Password Manager</h3>
                    
                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1);">
                        <label style="color: var(--text-muted); font-size: 0.9rem;">Your Saved Password</label>
                        <div class="password-wrapper" style="margin-top: 5px;">
                            <input type="password" value="<?php echo $user['password']; ?>" id="my_real_pass" readonly style="opacity: 0.8; cursor: text; background: transparent; border: none; padding: 0; font-size: 1.2rem; letter-spacing: 2px;">
                            <i class="fa fa-eye toggle-password" onclick="togglePass('my_real_pass', this)" style="right: 0;"></i>
                        </div>
                    </div>

                    <form method="POST">
                        <div class="input-group">
                            <label>New Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="new_pass" id="new_pass" required placeholder="••••">
                                <i class="fa fa-eye toggle-password" onclick="togglePass('new_pass', this)"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Confirm New Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="confirm_pass" id="confirm_pass" required placeholder="••••">
                                <i class="fa fa-eye toggle-password" onclick="togglePass('confirm_pass', this)"></i>
                            </div>
                        </div>
                        <input type="hidden" name="old_pass" value="<?php echo $user['password']; ?>">
                        
                        <button type="submit" name="change_pass" class="btn-tech" style="width:100%; background: #ef4444;">Change Password</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePass(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
