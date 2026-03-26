<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$msg = "";

// Handle Update
if (isset($_POST['update_dept'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $fees = $_POST['fees']; // Get the fees from the form
    
    // Update Name AND Fees
    $sql = "UPDATE departments SET name='$name', fees='$fees' WHERE id=$id";
    
    if ($conn->query($sql)) {
        header("Location: departments.php"); // Redirect back to list
        exit();
    } else {
        $msg = "<p style='color:red;'>Error updating department.</p>";
    }
}

// Fetch Current Data
$sql = "SELECT * FROM departments WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Department // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container" style="display:flex; justify-content:center; align-items:center; height:100vh;">
        <div class="tech-card" style="width: 400px;">
            <h2 style="margin-bottom: 20px;">Edit Department</h2>
            <?php echo $msg; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label>Department Name</label>
                    <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Annual Tuition Fees ($)</label>
                    <input type="number" name="fees" value="<?php echo isset($row['fees']) ? $row['fees'] : '0.00'; ?>" step="0.01" required>
                </div>
                
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" name="update_dept" class="btn-tech">Save Changes</button>
                    <a href="departments.php" style="padding: 12px 20px; color: white; text-decoration: none; opacity: 0.7;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
