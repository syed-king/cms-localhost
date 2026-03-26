<?php
include 'db.php';
date_default_timezone_set('Asia/Kolkata');

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
    <title>Upcoming Exams // Super Tech</title>
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
                <a href="student_materials.php" class="nav-item">Study Materials</a>
                <a href="student_exams.php" class="nav-item active">Upcoming Exams</a>
                <a href="student_leave.php" class="nav-item">leave request</a>
                <a href="student_id_card.php" class="nav-item">ID card</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            
            <?php if (!$dept_id): ?>
                 <div class="tech-card" style="border-left: 4px solid #ef4444;">
                    <h3 style="color: #ef4444; margin-top: 0;">⚠️ Account Setup Incomplete</h3>
                    <p style="color: var(--text-muted);">You are not assigned to any department yet.</p>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 30px;">
                    <h1 style="margin: 0;">Examination Schedule</h1>
                    <p style="color: var(--text-muted); margin-top: 5px;">
                        Department: <span style="color: var(--accent-glow); font-weight: bold;"><?php echo $dept_name; ?></span>
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php
                    $today = date('Y-m-d');
                    
                    // Fetch UPCOMING exams (Date >= Today)
                    $sql = "SELECT * FROM exams 
                            WHERE dept_id = $dept_id AND exam_date >= '$today' 
                            ORDER BY exam_date ASC";
                    
                    $res = $conn->query($sql);

                    if($res && $res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            $date_display = date("l, F d, Y", strtotime($row['exam_date']));
                            $days_left = (strtotime($row['exam_date']) - strtotime($today)) / (60 * 60 * 24);
                            
                            // Visual badge for how close the exam is
                            $badge = "";
                            if($days_left == 0) $badge = "<span style='background:#ef4444; color:white; padding:2px 8px; border-radius:4px; font-size:0.75rem;'>TODAY</span>";
                            elseif($days_left == 1) $badge = "<span style='background:#f59e0b; color:white; padding:2px 8px; border-radius:4px; font-size:0.75rem;'>TOMORROW</span>";
                            else $badge = "<span style='background:rgba(59, 130, 246, 0.2); color:#3b82f6; padding:2px 8px; border-radius:4px; font-size:0.75rem;'>In $days_left days</span>";

                            echo "<div class='tech-card' style='position:relative; border-left: 4px solid #06b6d4; transition: transform 0.2s;'>
                                    <div style='display:flex; justify-content:space-between; align-items:start; margin-bottom:15px;'>
                                        <div style='font-size: 2rem;'>📝</div>
                                        $badge
                                    </div>
                                    
                                    <h3 style='margin: 0 0 5px 0; color: white; font-size: 1.2rem;'>{$row['exam_name']}</h3>
                                    
                                    <div style='margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--glass-border);'>
                                        <div style='display:flex; align-items:center; gap:10px; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;'>
                                            <span>📅</span> $date_display
                                        </div>
                                        <div style='display:flex; align-items:center; gap:10px; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;'>
                                            <span>⏰</span> {$row['time_slot']}
                                        </div>
                                        <div style='display:flex; align-items:center; gap:10px; color:var(--text-muted); font-size:0.9rem;'>
                                            <span>📍</span> Room: <span style='color:white;'>{$row['room_no']}</span>
                                        </div>
                                    </div>
                                  </div>";
                        }
                    } else {
                        echo "<div class='tech-card' style='grid-column: 1/-1; text-align: center; padding: 40px;'>
                                <div style='font-size: 3rem; margin-bottom: 10px; opacity: 0.5;'>🎉</div>
                                <h3 style='color: white; margin: 0;'>No Upcoming Exams</h3>
                                <p style='color: var(--text-muted);'>You are all clear for now!</p>
                              </div>";
                    }
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
