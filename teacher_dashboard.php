<?php
include 'db.php';

date_default_timezone_set('Asia/Kolkata'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['name'];

// --- LOGIC 1: CLASS SCHEDULE ---
$today = date('l'); 
$current_time = time(); 

$sql = "SELECT timetable.*, departments.name as dept_name, departments.id as dept_id 
        FROM timetable 
        LEFT JOIN departments ON timetable.dept_id = departments.id
        WHERE teacher_id = $teacher_id AND day = '$today'
        ORDER BY id ASC";

$result = $conn->query($sql);

$display_class = null;
$status_label = "✅ All Classes Done";
$status_color = "#10b981"; 

if ($result->num_rows > 0) {
    $upcoming_class = null;
    $found_current = false;

    while($row = $result->fetch_assoc()) {
        $times = explode('-', $row['time_slot']);
        if(count($times) == 2) {
            $start_time = strtotime(trim($times[0])); 
            $end_time = strtotime(trim($times[1]));

            if ($current_time >= $start_time && $current_time <= $end_time) {
                $display_class = $row;
                $status_label = "🔴 Happening Now";
                $status_color = "#ef4444"; 
                $found_current = true;
                break; 
            }
            if ($current_time < $start_time) {
                if ($upcoming_class == null || $start_time < strtotime(explode('-', $upcoming_class['time_slot'])[0])) {
                    $upcoming_class = $row;
                }
            }
        }
    }
    if (!$found_current && $upcoming_class) {
        $display_class = $upcoming_class;
        $status_label = "⏱️ Upcoming Class";
        $status_color = "#f59e0b"; 
    }
}

// --- LOGIC 2: STUDENT COUNT ---
$student_count = 0;
$target_dept_id = 0;
if ($display_class) {
    $target_dept_id = $display_class['dept_id'];
} else {
    $t_res = $conn->query("SELECT dept_id FROM users WHERE id = $teacher_id");
    if($t_res->num_rows > 0) $target_dept_id = $t_res->fetch_assoc()['dept_id'];
}
if ($target_dept_id) {
    $c_sql = "SELECT COUNT(*) as total FROM users WHERE role='student' AND dept_id = $target_dept_id";
    $c_res = $conn->query($c_sql);
    $student_count = $c_res->fetch_assoc()['total'];
}

// --- LOGIC 3: FETCH NEXT EVENT (POSTER) ---
$today_date = date('Y-m-d');
$event_sql = "SELECT * FROM events WHERE event_date >= '$today_date' ORDER BY event_date ASC LIMIT 1";
$event_res = $conn->query($event_sql);
$event = $event_res->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Portal // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .teacher-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px; margin-bottom: 30px; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 10px; }
        
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
                <a href="teacher_dashboard.php" class="nav-item active">Classroom Overview</a>
                <a href="mark_attendance.php" class="nav-item">Mark Attendance</a>
                <a href="upload_materials.php" class="nav-item">Upload Materials</a>
                <a href="teacher_marks.php" class="nav-item">Enter Marks</a>
                <a href="dept_schedule.php" class="nav-item">Dept. Schedule</a>
                <a href="teacher_leave.php" class="nav-item">student leave</a>
                <a href="teacher_id_card.php" class="nav-item">ID card</a>
            </nav>
            <div style="margin-top: auto;">
            <a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <div>
                    <h1 style="margin: 0; font-size: 1.8rem;">Teacher Portal</h1>
                    <p style="color: var(--text-muted); margin: 5px 0 0 0;">Welcome back, <?php echo $teacher_name; ?></p>
                </div>
                <div style="width: 40px; height: 40px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">T</div>
            </div>

            <div class="teacher-grid">
                <div class="tech-card" style="background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02)); position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: <?php echo $status_color; ?>; filter: blur(80px); opacity: 0.2;"></div>
                    <div style="margin-bottom: 5px; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Status</div>
                    <div class="status-badge" style="background: <?php echo $status_color; ?>20; color: <?php echo $status_color; ?>;"><?php echo $status_label; ?></div>

                    <?php if ($display_class): ?>
                        <h1 style="margin: 10px 0; font-size: 2rem; color: white;"><?php echo $display_class['subject']; ?></h1>
                        <div style="font-size: 1.1rem; color: var(--text-muted); display: flex; align-items: center; gap: 10px;">
                            <span>🕒 <?php echo $display_class['time_slot']; ?></span>
                            <span style="opacity: 0.3;">|</span>
                            <span>🏛️ <?php echo $display_class['dept_name']; ?></span>
                        </div>
                    <?php else: ?>
                        <h2 style="margin: 15px 0; font-weight: 400; color: var(--text-muted);">Relax! You have no classes scheduled for the rest of the day.</h2>
                    <?php endif; ?>
                </div>

                <div class="tech-card">
                    <div style="margin-bottom: 15px; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Class Strength</div>
                    <div style="font-size: 2.5rem; font-weight: 600; color: white; margin-bottom: 5px;"><?php echo str_pad($student_count, 2, '0', STR_PAD_LEFT); ?></div>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.9rem;"><?php echo $display_class ? "Enrolled in " . $display_class['dept_name'] : "Students in your department"; ?></p>
                    <a href="mark_attendance.php" style="display: inline-block; margin-top: 15px; color: var(--accent-glow); text-decoration: none; font-size: 0.9rem;">View Student List →</a>
                </div>
            </div>

            <h3 style="color: white; margin-bottom: 20px;">Notice Board</h3>
            
            <?php if ($event): ?>
                
                <?php
                // 1. Setup Default Background
                $bg_style = "background: linear-gradient(135deg, #1e293b, #0f172a);"; 
                $overlay_style = ""; 
                $is_image = false;

                // 2. Check if there is an attachment
                if(!empty($event['attachment'])) {
                    $ext = strtolower(pathinfo($event['attachment'], PATHINFO_EXTENSION));
                    
                    // If it is an IMAGE (jpg, png, etc) -> Use as background
                    if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $bg_style = "background: url('{$event['attachment']}') center/cover no-repeat;";
                        $overlay_style = "background: rgba(0,0,0,0.75);"; // Darken it so text is readable
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
                <div class="tech-card" style="text-align: center; padding: 40px;">
                    <div style="font-size: 3rem; margin-bottom: 10px; opacity: 0.5;">📭</div>
                    <h3 style="color: white; margin: 0;">No Upcoming Events</h3>
                    <p style="color: var(--text-muted);">The notice board is currently empty.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
