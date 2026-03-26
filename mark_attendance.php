<?php
include 'db.php';
date_default_timezone_set('Asia/Kolkata');

// Security: Only Teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$msg = "";

// 1. Get Teacher's Department
$t_sql = "SELECT dept_id, departments.name as dept_name 
          FROM users 
          LEFT JOIN departments ON users.dept_id = departments.id 
          WHERE users.id = $teacher_id";
$t_res = $conn->query($t_sql);
$t_data = $t_res->fetch_assoc();
$dept_id = $t_data['dept_id'];
$dept_name = $t_data['dept_name'];

// 2. Determine Date (Default to Today or User Selected)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// 3. Handle Form Submission (Save Attendance)
if (isset($_POST['submit_attendance'])) {
    $post_date = $_POST['attendance_date']; 
    $attendance_data = $_POST['status']; 

    foreach ($attendance_data as $stu_id => $status) {
        $check = $conn->query("SELECT id FROM attendance WHERE student_id=$stu_id AND date='$post_date'");
        
        if($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO attendance (student_id, teacher_id, dept_id, date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiss", $stu_id, $teacher_id, $dept_id, $post_date, $status);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("UPDATE attendance SET status=?, teacher_id=? WHERE student_id=? AND date=?");
            $stmt->bind_param("siis", $status, $teacher_id, $stu_id, $post_date);
            $stmt->execute();
        }
    }
    $msg = "<div class='success-msg'>✅ Attendance for <b>" . date("M d, Y", strtotime($post_date)) . "</b> saved successfully!</div>";
    $selected_date = $post_date; 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-msg {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #10b981;
            text-align: center;
        }

        /* Custom Date Input */
        input[type="date"] {
            background: rgba(255,255,255,0.1);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-family: inherit;
            outline: none;
            color-scheme: dark;
            cursor: pointer;
        }

        /* Radio Button Styling */
        .status-options { display: flex; gap: 8px; justify-content: center; }
        
        .radio-label {
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 6px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
            transition: all 0.2s;
            user-select: none;
        }
        
        input[type="radio"] { display: none; }
        input[type="radio"].present:checked + .radio-label {
            background: #10b981; color: white; border-color: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.4);
        }
        input[type="radio"].absent:checked + .radio-label {
            background: #ef4444; color: white; border-color: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="teacher_dashboard.php" class="nav-item">Classroom Overview</a>
                <a href="mark_attendance.php" class="nav-item active">Mark Attendance</a>
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
            
            <div style="display: flex; justify-content: space-between; align-items: end; margin-bottom: 20px;">
                <div>
                    <h1 style="margin: 0;">Mark Attendance</h1>
                    <p style="color: var(--text-muted); margin-top: 5px;">Department: <span style="color: var(--accent-glow);"><?php echo $dept_name; ?></span></p>
                </div>
                
                <form method="GET" id="dateForm">
                    <label style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 5px;">Select Date</label>
                    <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="document.getElementById('dateForm').submit()">
                </form>
            </div>

            <?php echo $msg; ?>

            <form method="POST">
                <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">

                <div class="tech-card" style="padding: 0; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: rgba(255,255,255,0.05);">
                            <tr>
                                <th style="padding: 15px 20px; text-align: left; color: var(--text-muted); font-weight: 500;">Student Name</th>
                                <th style="padding: 15px 20px; text-align: center; color: var(--text-muted); font-weight: 500;">Overall %</th>
                                <th style="padding: 15px 20px; text-align: center; color: var(--text-muted); font-weight: 500;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $s_sql = "SELECT id, name, email FROM users WHERE role='student' AND dept_id = $dept_id ORDER BY name ASC";
                            $s_res = $conn->query($s_sql);

                            if ($s_res->num_rows > 0) {
                                while($student = $s_res->fetch_assoc()) {
                                    $sid = $student['id'];
                                    
                                    // --- LOGIC: Calculate Percentage for this Student ---
                                    // 1. Get Total Days
                                    $tot_q = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE student_id=$sid");
                                    $total_days = $tot_q->fetch_assoc()['c'];

                                    // 2. Get Present Days
                                    $pre_q = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE student_id=$sid AND status='Present'");
                                    $present_days = $pre_q->fetch_assoc()['c'];

                                    // 3. Math
                                    $percentage = ($total_days > 0) ? round(($present_days / $total_days) * 100) : 0;
                                    
                                    // 4. Color Code
                                    $p_color = '#ef4444'; // Red
                                    if($percentage >= 75) $p_color = '#10b981'; // Green
                                    elseif($percentage >= 50) $p_color = '#f59e0b'; // Orange
                                    
                                    // ----------------------------------------------------

                                    // Check Saved Attendance for TODAY (or selected date)
                                    $status = 'Present'; 
                                    $check_sql = "SELECT status FROM attendance WHERE student_id=$sid AND date='$selected_date'";
                                    $check_res = $conn->query($check_sql);
                                    if($check_res->num_rows > 0) {
                                        $status = $check_res->fetch_assoc()['status'];
                                    }

                                    $p_checked = ($status == 'Present') ? 'checked' : '';
                                    $a_checked = ($status == 'Absent') ? 'checked' : '';

                                    echo "<tr style='border-bottom: 1px solid var(--glass-border);'>
                                            <td style='padding: 15px 20px;'>
                                                <div style='font-weight: 500; font-size: 1rem;'>{$student['name']}</div>
                                                <div style='font-size: 0.85rem; color: var(--text-muted);'>{$student['email']}</div>
                                            </td>
                                            
                                            <td style='padding: 15px 20px; text-align: center;'>
                                                <div style='display:inline-block; padding: 4px 12px; background: {$p_color}20; color: $p_color; border-radius: 15px; font-weight: 600; font-size: 0.9rem;'>
                                                    $percentage%
                                                </div>
                                                <div style='font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;'>$present_days / $total_days Days</div>
                                            </td>

                                            <td style='padding: 15px 20px;'>
                                                <div class='status-options'>
                                                    <label>
                                                        <input type='radio' name='status[$sid]' value='Present' class='present' $p_checked>
                                                        <span class='radio-label'>Present</span>
                                                    </label>
                                                    <label>
                                                        <input type='radio' name='status[$sid]' value='Absent' class='absent' $a_checked>
                                                        <span class='radio-label'>Absent</span>
                                                    </label>
                                                </div>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center; padding: 40px; color: var(--text-muted);'>No students found in your department.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($s_res->num_rows > 0): ?>
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="submit" name="submit_attendance" class="btn-tech" style="width: auto; padding: 12px 30px; background: #3b82f6;">
                            Save Attendance
                        </button>
                    </div>
                <?php endif; ?>
            </form>

        </div>
    </div>
</body>
</html>
