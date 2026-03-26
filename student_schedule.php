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

// Current Day for highlighting
$current_day = date('l'); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Schedule // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tt-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        .day-column {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 20px;
            transition: 0.3s;
        }
        /* Highlight Today */
        .day-column.today {
            border-color: var(--accent-glow);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(255,255,255,0.01));
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
        }
        .day-header {
            font-size: 1.1rem;
            color: var(--accent-glow);
            margin-bottom: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        .class-card {
            background: rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 3px solid #64748b;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="student_dashboard.php" class="nav-item">My Dashboard</a>
                <a href="student_schedule.php" class="nav-item active">Class Schedule</a>
                <a href="student_results.php" class="nav-item">My Results</a>
                <a href="student_materials.php" class="nav-item">Study Materials</a>
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
            
            <?php if (!$dept_id): ?>
                 <div class="tech-card" style="border-left: 4px solid #ef4444;">
                    <h3 style="color: #ef4444; margin-top: 0;">⚠️ Account Setup Incomplete</h3>
                    <p style="color: var(--text-muted);">You are not assigned to any department yet.</p>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 30px;">
                    <h1 style="margin: 0;">Weekly Schedule</h1>
                    <p style="color: var(--text-muted); margin-top: 5px;">
                        Department: <span style="color: var(--accent-glow); font-weight: bold;"><?php echo $dept_name; ?></span>
                    </p>
                </div>

                <div class="tt-grid">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                    
                    foreach($days as $day) {
                        // Check if it is today
                        $is_today = ($day == $current_day) ? 'today' : '';
                        $today_badge = ($day == $current_day) ? "<span style='font-size:0.7rem; background:#3b82f6; color:white; padding:2px 6px; border-radius:4px; vertical-align:middle;'>TODAY</span>" : "";

                        echo "<div class='day-column $is_today'>
                                <div class='day-header'>
                                    <span>$day</span>
                                    $today_badge
                                </div>";
                        
                        // Fetch classes
                        $sql = "SELECT timetable.*, users.name as teacher_name 
                                FROM timetable 
                                LEFT JOIN users ON timetable.teacher_id = users.id 
                                WHERE timetable.dept_id=$dept_id AND day='$day' 
                                ORDER BY time_slot";
                        
                        $res = $conn->query($sql);
                        
                        if($res && $res->num_rows > 0) {
                            while($t = $res->fetch_assoc()) {
                                $teacher_name = $t['teacher_name'] ? $t['teacher_name'] : "TBA";
                                echo "<div class='class-card'>
                                        <div style='font-size:0.85rem; color:var(--text-muted); margin-bottom:4px;'>{$t['time_slot']}</div>
                                        <div style='font-weight:bold; color:white; font-size:1.1rem; margin-bottom:4px;'>{$t['subject']}</div>
                                        <div style='font-size:0.9rem; color:#94a3b8;'>👨‍🏫 $teacher_name</div>
                                      </div>";
                            }
                        } else {
                            echo "<div style='color:var(--text-muted); font-size:0.9rem; font-style:italic; padding:10px;'>No classes.</div>";
                        }
                        
                        echo "</div>"; 
                    }
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
