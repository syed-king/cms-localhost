<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get User ID from URL
if (!isset($_GET['id'])) {
    header("Location: teacher_salary.php");
    exit();
}

$id = $_GET['id'];
$msg = "";

// --- 1. FETCH USER DATA FIRST (Moved to top) ---
// We do this first so we know their dept_id for the redirect later
$sql = "SELECT * FROM users WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$dept_id = $row['dept_id']; // <--- Save this for the redirect

// --- 2. HANDLE SALARY UPDATE ---
if (isset($_POST['update_salary'])) {
    $salary = $_POST['salary'];
    
    // Update Query
    $sql = "UPDATE users SET salary='$salary' WHERE id=$id";
    
    if ($conn->query($sql)) {
        // SUCCESS: Redirect back to the SPECIFIC Department page
        header("Location: teacher_salary.php?dept_id=$dept_id"); 
        exit();
    } else {
        $msg = "<p style='color:red;'>Error updating salary.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Salary // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container" style="display:flex; justify-content:center; align-items:center; height:100vh;">
        <div class="tech-card" style="width: 400px;">
            <h2 style="margin-bottom: 20px;">Update Teacher Salary</h2>
            <?php echo $msg; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label>Teacher Name</label>
                    <input type="text" value="<?php echo $row['name']; ?>" disabled style="opacity:0.6; cursor:not-allowed;">
                </div>

                <div class="input-group">
                    <label>Monthly Salary ($)</label>
                    <input type="number" name="salary" value="<?php echo isset($row['salary']) ? $row['salary'] : '0.00'; ?>" step="0.01" required>
                </div>
                
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" name="update_salary" class="btn-tech">Update Salary</button>
                    <a href="teacher_salary.php?dept_id=<?php echo $dept_id; ?>" style="padding: 12px 20px; color: white; text-decoration: none; opacity: 0.7;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
