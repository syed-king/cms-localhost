<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get User ID
if (!isset($_GET['id'])) {
    header("Location: student_list.php");
    exit();
}

$id = $_GET['id'];
$msg = "";

// --- 1. FETCH USER DATA ---
$sql = "SELECT users.*, departments.name as dept_name, departments.fees as standard_fee 
        FROM users 
        LEFT JOIN departments ON users.dept_id = departments.id 
        WHERE users.id=$id";
$result = $conn->query($sql);
$student = $result->fetch_assoc();
$dept_id = $student['dept_id']; // Save for redirect

// --- 2. HANDLE FEE UPDATE ---
if (isset($_POST['update_fees'])) {
    $fees_due = $_POST['fees_due'];
    
    $sql = "UPDATE users SET fees_due='$fees_due' WHERE id=$id";
    
    if ($conn->query($sql)) {
        // Redirect back to the specific department list
        header("Location: student_list.php?dept_id=$dept_id");
        exit();
    } else {
        $msg = "<p style='color:red;'>Error updating fees.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student Fees // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container" style="display:flex; justify-content:center; align-items:center; height:100vh;">
        <div class="tech-card" style="width: 450px;">
            <h2 style="margin-bottom: 5px;">Manage Student Fees</h2>
            <p style="color:var(--text-muted); margin-bottom:20px;">Department: <?php echo $student['dept_name']; ?></p>
            
            <?php echo $msg; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label>Student Name</label>
                    <input type="text" value="<?php echo $student['name']; ?>" disabled style="opacity:0.6;">
                </div>

                <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="font-size: 0.85rem; color: var(--text-muted);">Standard Dept. Fee:</div>
                    <div style="font-size: 1.1rem; color: #10b981;">$ <?php echo number_format($student['standard_fee'], 2); ?></div>
                </div>

                <div class="input-group">
                    <label>Current Fees Due ($)</label>
                    <input type="number" name="fees_due" value="<?php echo $student['fees_due']; ?>" step="0.01" required 
                           style="border: 2px solid #3b82f6; background: rgba(59, 130, 246, 0.1);">
                </div>
                
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" name="update_fees" class="btn-tech">Update Balance</button>
                    <a href="student_list.php?dept_id=<?php echo $dept_id; ?>" style="padding: 12px 20px; color: white; text-decoration: none; opacity: 0.7;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>