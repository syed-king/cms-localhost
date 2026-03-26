<?php
include 'db.php';

// Security: Only Teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// 1. Get Teacher's Department Info
$d_res = $conn->query("SELECT dept_id, departments.name as dept_name 
                       FROM users 
                       LEFT JOIN departments ON users.dept_id = departments.id 
                       WHERE users.id = $teacher_id");
$d_data = $d_res->fetch_assoc();
$dept_id = $d_data['dept_id'];
$dept_name = $d_data['dept_name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dept. Schedule // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Timetable Grid Styles */
        .tt-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        .day-column {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 20px;
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
        }
        .class-card {
            background: rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 3px solid #64748b; /* Default Gray Border */
            transition: transform 0.2s;
        }
        .class-card:hover {
            transform: translateX(5px);
            background: rgba(255,255,255,0.05);
        }
        
        /* Highlight for YOUR classes */
        .my-class {
            background: linear-gradient(90deg, rgba(236, 72, 153, 0.1), transparent);
            border-left: 3px solid #ec4899; /* Pink Border */
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="teacher_dashboard.php" class="nav-item">Classroom Overview</a>
                <a href="mark_attendance.php" class="nav-item">Mark Attendance</a>
                <a href="upload_materials.php" class="nav-item">Upload Materials</a>
                <a href="teacher_marks.php" class="nav-item">Enter Marks</a>
                <a href="dept_schedule.php" class="nav-item active">Dept. Schedule</a>
                <a href="teacher_leave.php" class="nav-item">student leave</a>
                <a href="teacher_id_card.php" class="nav-item">ID card</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <div style="margin-bottom: 30px;">
                <h1 style="margin: 0;">Department Schedule</h1>
                <p style="color: var(--text-muted); margin-top: 5px;">
                    Viewing timetable for: <span style="color: var(--accent-glow); font-weight: bold;"><?php echo $dept_name; ?></span>
                </p>
            </div>

            <div class="tt-grid">
                <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                
                foreach($days as $day) {
                    echo "<div class='day-column'>
                            <div class='day-header'>$day</div>";
                    
                    // Fetch classes for this day & department
                    $sql = "SELECT timetable.*, users.name as teacher_name 
                            FROM timetable 
                            LEFT JOIN users ON timetable.teacher_id = users.id 
                            WHERE timetable.dept_id=$dept_id AND day='$day' 
                            ORDER BY time_slot";
                    
                    $res = $conn->query($sql);
                    
                    if($res->num_rows > 0) {
                        while($t = $res->fetch_assoc()) {
                            // Check if this is the logged-in teacher's class
                            $is_mine = ($t['teacher_id'] == $teacher_id);
                            $card_class = $is_mine ? "class-card my-class" : "class-card";
                            $teacher_display = $is_mine ? "<b>You</b>" : $t['teacher_name'];

                            echo "<div class='$card_class'>
                                    <div style='font-size:0.85rem; color:var(--text-muted); margin-bottom:4px;'>{$t['time_slot']}</div>
                                    <div style='font-weight:bold; color:white; font-size:1.1rem; margin-bottom:4px;'>{$t['subject']}</div>
                                    <div style='font-size:0.9rem; color:#94a3b8;'>👨‍🏫 $teacher_display</div>
                                  </div>";
                        }
                    } else {
                        echo "<div style='color:var(--text-muted); font-size:0.9rem; font-style:italic; padding:10px;'>No classes.</div>";
                    }
                    
                    echo "</div>"; // End day-column
                }
                ?>
            </div>

        </div>
    </div>
</body>
</html>
