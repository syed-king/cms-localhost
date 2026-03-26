<?php
include 'db.php';

// (Keep your existing PHP Login Logic here - no changes needed to logic)
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['dept_id'] = $row['dept_id'];

        if ($row['role'] == 'admin') header("Location: admin_dashboard.php");
        elseif ($row['role'] == 'teacher') header("Location: teacher_dashboard.php");
        elseif ($row['role'] == 'student') header("Location: student_dashboard.php");
    } else {
        $error = "Invalid Credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Super Tech | Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <div style="position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(59,130,246,0.05) 0%, transparent 70%); top: -200px; left: -200px;"></div>
        
        <div class="glass-card">
            <div class="logo-area">
                <h1>SUPER TECH</h1>
                <p>Management System v2.0</p>
            </div>

            <?php if(isset($error)) echo "<p style='color: #ef4444; font-size: 0.9rem; background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 8px;'>$error</p>"; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="admin@supertech.com" required>
                </div>
                
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="btn-tech">Access Dashboard</button>
            </form>
        </div>
    </div>
</body>
</html>