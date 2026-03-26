<?php
include 'db.php';

// Security: Ensure only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// --- LOGIC 1: Handle Deletion (New Code) ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Run the Delete Query
    $del_sql = "DELETE FROM departments WHERE id = $id";
    if ($conn->query($del_sql) === TRUE) {
        // Refresh page to clear the URL and show updated list
        header("Location: departments.php"); 
        exit();
    } else {
        $msg = "<span style='color: #ef4444;'>Error deleting: " . $conn->error . "</span>";
    }
}

// --- LOGIC 2: Handle Adding New Department ---
$msg = "";
if (isset($_POST['add_dept'])) {
    $dept_name = $_POST['dept_name'];
    $dept_fees = $_POST['dept_fees']; 
    
    if (!empty($dept_name) && !empty($dept_fees)) {
        $sql = "INSERT INTO departments (name, fees) VALUES ('$dept_name', '$dept_fees')";
        if ($conn->query($sql) === TRUE) {
            $msg = "<span style='color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 5px 10px; border-radius: 5px;'>Department Added!</span>";
        } else {
            $msg = "<span style='color: #ef4444;'>Error: " . $conn->error . "</span>";
        }
    } else {
        $msg = "<span style='color: #f59e0b;'>Please fill in all fields.</span>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Departments // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="admin_dashboard.php" class="nav-item">Dashboard Overview</a>
                <a href="departments.php" class="nav-item active">Departments</a>
                <a href="user_management.php" class="nav-item">User Management</a>
                <a href="financials.php" class="nav-item">Financials</a>
                <a href="student_list.php" class="nav-item">Student List</a>
                <a href="teacher_salary.php" class="nav-item">Teacher Salary</a>
                <a href="timetable.php" class="nav-item">Time Table</a>
                <a href="exams.php" class="nav-item">Exams</a>
                <a href="admin_events.php" class="nav-item">Campus Events</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <div style="margin-bottom: 30px;">
                <h1 style="margin: 0; font-size: 1.8rem;">Department Management</h1>
                <p style="color: var(--text-muted); margin-top: 5px;">Manage faculties and fee structures.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
                
                <div class="tech-card" style="height: fit-content;">
                    <h3 style="color: var(--accent-glow); margin-bottom: 20px;">Add New Department</h3>
                    
                    <?php if($msg != "") echo "<div style='margin-bottom:15px;'>$msg</div>"; ?>

                    <form method="POST">
                        <div class="input-group">
                            <label>Department Name</label>
                            <input type="text" name="dept_name" placeholder="e.g. Aeronautical Eng." required>
                        </div>

                        <div class="input-group">
                            <label>Semester Fee Amount ($)</label>
                            <input type="number" name="dept_fees" placeholder="e.g. 4500.00" step="0.01" required>
                        </div>

                        <button type="submit" name="add_dept" class="btn-tech">Create Department</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="color: white; margin-bottom: 20px;">Existing Departments</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Semester Fee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM departments";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td style='color: var(--accent-secondary); font-weight:bold;'>#{$row['id']}</td>
                                            <td>{$row['name']}</td>
                                            
                                            <td style='color: #10b981; font-family: monospace; font-size: 1rem;'>
                                                $" . number_format($row['fees'], 2) . "
                                            </td>
                                            
                                            <td>
                                                <a href='edit_department.php?id={$row['id']}' 
                                                   style='color: var(--accent-glow); text-decoration: none; font-size: 0.9rem; margin-right: 15px;'>
                                                   Edit
                                                </a>
                                                
                                                <a href='departments.php?delete={$row['id']}' 
                                                   style='color: #ef4444; text-decoration: none; font-size: 0.9rem;'
                                                   onclick='return confirm(\"Are you sure you want to delete this department?\");'>
                                                   Delete
                                                </a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; color: var(--text-muted);'>No departments found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
