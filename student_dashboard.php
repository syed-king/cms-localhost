<?php
include 'db.php';

// Fix Timezone
date_default_timezone_set('Asia/Kolkata'); 

// Security: Only Student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['name'];

// --- LOGIC 1: CALCULATE REAL ATTENDANCE ---
$total_days = 0;
$present_days = 0;
$percentage = 0;
$att_color = "#94a3b8"; // Default Gray
$att_msg = "No data yet";

// Count TOTAL days attendance was taken for this student
$t_sql = "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id";
$t_res = $conn->query($t_sql);
if($t_res) { $total_days = $t_res->fetch_assoc()['count']; }

// Count PRESENT days
$p_sql = "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id AND status = 'Present'";
$p_res = $conn->query($p_sql);
if($p_res) { $present_days = $p_res->fetch_assoc()['count']; }

// Calculate %
if ($total_days > 0) {
    $percentage = round(($present_days / $total_days) * 100);
    $att_msg = "Present: $present_days / $total_days Days";
    
    // Set Color based on percentage
    if($percentage >= 75) {
        $att_color = "#10b981"; // Green (Good)
    } elseif ($percentage >= 50) {
        $att_color = "#f59e0b"; // Orange (Warning)
    } else {
        $att_color = "#ef4444"; // Red (Bad)
    }
}

// --- LOGIC 2: FETCH NEXT EVENT (POSTER) ---
$today_date = date('Y-m-d');
$event_sql = "SELECT * FROM events WHERE event_date >= '$today_date' ORDER BY event_date ASC LIMIT 1";
$event_res = $conn->query($event_sql);
$event = $event_res->fetch_assoc();

// --- LOGIC 3: FETCH MY DEPARTMENT ---
$dept_name = "General";
$u_sql = "SELECT departments.name 
          FROM users 
          LEFT JOIN departments ON users.dept_id = departments.id 
          WHERE users.id = $student_id";
$u_res = $conn->query($u_sql);
if($u_res->num_rows > 0) {
    $dept_name = $u_res->fetch_assoc()['name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Portal // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .student-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px; }
        
        /* Event Poster Style */
        .event-poster {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            position: relative;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
            margin-top: 30px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .event-poster:hover {
            transform: translateY(-7px);
            border-color: var(--accent-glow);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.6), 0 0 20px rgba(59, 130, 246, 0.5);
        }
        .event-bg-glow {
            position: absolute; width: 100%; height: 100%; top: 0; left: 0;
            background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.15), transparent 70%);
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="student_dashboard.php" class="nav-item active">My Dashboard</a>
                <a href="student_schedule.php" class="nav-item">Class Schedule</a>
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
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <div>
                    <h1 style="margin: 0; font-size: 1.8rem;">Student Portal</h1>
                    <p style="color: var(--text-muted); margin: 5px 0 0 0;">Welcome, <?php echo $student_name; ?></p>
                </div>
                <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">S</div>
            </div>

            <div class="student-grid">
                
                <div class="tech-card">
                    <div style="margin-bottom: 10px; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">My Attendance</div>
                    
                    <div style="font-size: 2.5rem; font-weight: 600; color: <?php echo $att_color; ?>;">
                        <?php echo $percentage; ?>%
                    </div>
                    
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.9rem;">
                        <?php echo $att_msg; ?>
                    </p>
                </div>
                
                <div class="tech-card">
                    <div style="margin-bottom: 10px; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">Department</div>
                    <div style="font-size: 1.5rem; font-weight: 600; color: white;"><?php echo $dept_name; ?></div>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.9rem;">Diploma of Technology</p>
                </div>
            </div>

            <h3 style="color: white; margin-bottom: 0;">Campus Notice Board</h3>

            <?php if ($event): ?>
                
                <?php
                // Image Logic for Poster
                $bg_style = "background: linear-gradient(135deg, #1e293b, #0f172a);"; 
                $overlay_style = ""; 
                $is_image = false;

                if(!empty($event['attachment'])) {
                    $ext = strtolower(pathinfo($event['attachment'], PATHINFO_EXTENSION));
                    if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $bg_style = "background: url('{$event['attachment']}') center/cover no-repeat;";
                        $overlay_style = "background: rgba(0,0,0,0.75);"; 
                        $is_image = true;
                    }
                }
                ?>

                <div class="event-poster" style="<?php echo $bg_style; ?>">
                    <div class="event-bg-glow" style="<?php echo $overlay_style; ?>"></div>
                    
                    <div style="z-index: 1;">
                        <span style="background: var(--accent-secondary); color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase;">
                            <?php echo $event['type']; ?>
                        </span>
                        
                        <h1 style="font-size: 2.5rem; margin: 20px 0 10px 0; background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                            <?php echo $event['title']; ?>
                        </h1>
                        
                        <div style="font-size: 1.2rem; color: var(--accent-glow); margin-bottom: 20px; font-weight: 600;">
                            📅 <?php echo date("l, F d, Y", strtotime($event['event_date'])); ?>
                        </div>
                        
                        <p style="color: #cbd5e1; font-size: 1.1rem; max-width: 600px; line-height: 1.6; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
                            <?php echo $event['description']; ?>
                        </p>

                        <?php if(!empty($event['attachment']) && !$is_image): ?>
                             <a href="<?php echo $event['attachment']; ?>" target="_blank" style="display:inline-block; margin-top:20px; background:white; color:black; padding:10px 20px; border-radius:30px; text-decoration:none; font-weight:bold; transition:0.3s;">
                                📥 Download Attachment
                             </a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <div class="tech-card" style="text-align: center; padding: 40px; margin-top: 20px;">
                    <div style="font-size: 3rem; margin-bottom: 10px; opacity: 0.5;">📭</div>
                    <h3 style="color: white; margin: 0;">No Upcoming Events</h3>
                    <p style="color: var(--text-muted);">Enjoy your day!</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
