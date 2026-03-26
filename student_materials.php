<?php
include 'db.php';

// Security: Only Student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// 1. Get Student's Department Info
$s_res = $conn->query("SELECT dept_id, departments.name as dept_name 
                       FROM users 
                       LEFT JOIN departments ON users.dept_id = departments.id 
                       WHERE users.id = $student_id");

$dept_id = null;
$dept_name = "Not Assigned";

if ($s_res && $s_res->num_rows > 0) {
    $s_data = $s_res->fetch_assoc();
    $dept_id = $s_data['dept_id'];
    $dept_name = $s_data['dept_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Study Materials // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="student_dashboard.php" class="nav-item">My Dashboard</a>
                <a href="student_schedule.php" class="nav-item">Class Schedule</a>
                <a href="student_results.php" class="nav-item">My Results</a>
                <a href="student_materials.php" class="nav-item active">Study Materials</a>
                <a href="student_exams.php" class="nav-item">Upcoming Exams</a>
                <a href="student_leave.php" class="nav-item">leave request</a>
                <a href="student_id_card.php" class="nav-item">ID card</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <h1 style="margin-bottom: 10px;">Study Materials</h1>
            
            <?php if (!$dept_id): ?>
                <div class="tech-card" style="border-left: 4px solid #ef4444;">
                    <h3 style="color: #ef4444; margin-top: 0;">⚠️ Account Setup Incomplete</h3>
                    <p style="color: var(--text-muted);">You are not assigned to any department yet.</p>
                    <p style="color: var(--text-muted);">Please contact the Administrator to assign you to a department so you can view study materials.</p>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted); margin-bottom: 30px;">Department: <span style="color: var(--accent-glow);"><?php echo $dept_name; ?></span></p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php
                    // 2. Fetch Materials
                    $sql = "SELECT materials.*, users.name as teacher_name 
                            FROM materials 
                            LEFT JOIN users ON materials.teacher_id = users.id
                            WHERE materials.dept_id = $dept_id 
                            ORDER BY upload_date DESC";
                    $res = $conn->query($sql);

                    if($res && $res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            $date = date("M d, Y", strtotime($row['upload_date']));
                            
                            echo "<div class='tech-card' style='transition: transform 0.2s; position: relative;'>
                                    <div style='font-size: 2rem; margin-bottom: 15px;'>📄</div>
                                    <h3 style='margin: 0 0 10px 0; color: white;'>{$row['title']}</h3>
                                    <div style='font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;'>By: {$row['teacher_name']}</div>
                                    <div style='font-size: 0.8rem; color: #64748b;'>Date: $date</div>
                                    
                                    <a href='{$row['file_path']}' target='_blank' style='
                                        display: block; 
                                        margin-top: 20px; 
                                        text-align: center; 
                                        background: rgba(59, 130, 246, 0.1); 
                                        color: var(--accent-glow); 
                                        text-decoration: none; 
                                        padding: 10px; 
                                        border-radius: 8px; 
                                        border: 1px solid var(--accent-glow);
                                        font-weight: 600;'>
                                        Download / View
                                    </a>
                                  </div>";
                        }
                    } else {
                        echo "<div class='tech-card' style='grid-column: 1/-1; text-align: center; padding: 40px;'>
                                <h3 style='color: var(--text-muted);'>No materials available yet.</h3>
                                <p style='color: #64748b;'>Check back later when your teachers upload notes.</p>
                              </div>";
                    }
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
