<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get User ID
if (!isset($_GET['id'])) {
    header("Location: user_management.php");
    exit();
}

$id = $_GET['id'];
$msg = "";

// --- HANDLE UPDATE ---
if (isset($_POST['update_user'])) {
    $phone = $conn->real_escape_string($_POST['phone']);
    $designation = $conn->real_escape_string($_POST['designation']);
    
    $sql = "UPDATE users SET phone='$phone', designation='$designation' WHERE id=$id";
    
    if ($conn->query($sql)) {
        header("Location: user_management.php"); // Redirect back to list
        exit();
    } else {
        $msg = "<div style='color:red; margin-bottom:15px;'>Error updating user.</div>";
    }
}

// --- FETCH CURRENT DATA ---
$sql = "SELECT users.*, departments.name as dept_name 
        FROM users 
        LEFT JOIN departments ON users.dept_id = departments.id 
        WHERE users.id=$id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container" style="display:flex; justify-content:center; align-items:center; height:100vh;">
        <div class="tech-card" style="width: 450px;">
            <h2 style="margin-bottom: 5px;">Edit User Details</h2>
            <p style="color:var(--text-muted); margin-bottom:20px;">Update contact info for <?php echo $user['name']; ?></p>
            
            <?php echo $msg; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" value="<?php echo $user['name']; ?>" disabled style="opacity:0.6; cursor:not-allowed;">
                </div>
                <div class="input-group">
                    <label>Role</label>
                    <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled style="opacity:0.6; cursor:not-allowed;">
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required 
                           placeholder="+91..." style="border: 2px solid #3b82f6; background: rgba(59, 130, 246, 0.1);">
                </div>

                <div class="input-group">
                    <label>Designation</label>
                    <input type="text" name="designation" value="<?php echo $user['designation']; ?>" required 
                           placeholder="e.g. Student / Senior Lecturer" style="border: 2px solid #3b82f6; background: rgba(59, 130, 246, 0.1);">
                </div>
                
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" name="update_user" class="btn-tech">Save Changes</button>
                    <a href="user_management.php" style="padding: 12px 20px; color: white; text-decoration: none; opacity: 0.7;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>