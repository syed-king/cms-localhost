<?php
include 'db.php';

// Security: Only Teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$msg = "";

// Get Teacher's Department
$t_res = $conn->query("SELECT dept_id FROM users WHERE id=$teacher_id");
$dept_id = $t_res->fetch_assoc()['dept_id'];

// --- HANDLE ADD MARKS ---
if (isset($_POST['add_mark'])) {
    $student_id = $_POST['student_id'];
    $exam = $conn->real_escape_string($_POST['exam_name']);
    $subject = $conn->real_escape_string($_POST['subject_name']);
    $marks = $_POST['marks'];

    $sql = "INSERT INTO exam_results (student_id, dept_id, exam_name, subject_name, marks_obtained) 
            VALUES ($student_id, $dept_id, '$exam', '$subject', '$marks')";
    
    if ($conn->query($sql)) {
        $msg = "<div style='color:#10b981; margin-bottom:15px;'>Mark added successfully!</div>";
    } else {
        $msg = "<div style='color:#ef4444; margin-bottom:15px;'>Error adding mark.</div>";
    }
}

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM exam_results WHERE id=$del_id");
    header("Location: teacher_marks.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enter Marks // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px;">SUPER TECH</h2>
            <nav>
                <a href="teacher_dashboard.php" class="nav-item">Classroom Overview</a>
                <a href="mark_attendance.php" class="nav-item">Mark Attendance</a>
                <a href="upload_materials.php" class="nav-item">Upload Materials</a>
                <a href="teacher_marks.php" class="nav-item active">Enter Marks</a>
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
            <h1>Student Grading</h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Enter exam marks for your students.</p>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
                
                <div class="tech-card" style="height: fit-content;">
                    <h3 style="margin-bottom: 20px; color: #f59e0b;">Add New Mark</h3>
                    <?php echo $msg; ?>
                    <form method="POST">
                        <div class="input-group">
                            <label>Select Student</label>
                            <select name="student_id" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                                <?php
                                $s_sql = "SELECT id, name FROM users WHERE dept_id=$dept_id AND role='student'";
                                $s_res = $conn->query($s_sql);
                                while($s = $s_res->fetch_assoc()) {
                                    echo "<option value='{$s['id']}'>{$s['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Exam Name</label>
                            <input type="text" name="exam_name" placeholder="e.g. Cycle Test 1" required>
                        </div>
                        <div class="input-group">
                            <label>Subject</label>
                            <input type="text" name="subject_name" placeholder="e.g. Python Programming" required>
                        </div>
                        <div class="input-group">
                            <label>Marks Obtained (Out of 100)</label>
                            <input type="number" name="marks" max="100" required placeholder="85">
                        </div>
                        <button type="submit" name="add_mark" class="btn-tech">Save Mark</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="margin-bottom: 20px;">Recent Entries</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: rgba(255,255,255,0.05);">
                            <tr>
                                <th style="padding: 12px; text-align: left;">Student</th>
                                <th style="padding: 12px; text-align: left;">Exam / Subject</th>
                                <th style="padding: 12px; text-align: center;">Marks</th>
                                <th style="padding: 12px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $h_sql = "SELECT r.*, u.name as s_name 
                                      FROM exam_results r 
                                      JOIN users u ON r.student_id = u.id 
                                      WHERE r.dept_id=$dept_id 
                                      ORDER BY r.id DESC LIMIT 10";
                            $h_res = $conn->query($h_sql);

                            if($h_res->num_rows > 0) {
                                while($row = $h_res->fetch_assoc()) {
                                    echo "<tr style='border-bottom: 1px solid var(--glass-border);'>
                                            <td style='padding: 12px;'>{$row['s_name']}</td>
                                            <td style='padding: 12px;'>
                                                <div style='font-size:0.9rem; color:var(--text-muted);'>{$row['exam_name']}</div>
                                                <div>{$row['subject_name']}</div>
                                            </td>
                                            <td style='padding: 12px; text-align: center; font-weight: bold; color: #10b981;'>
                                                {$row['marks_obtained']}
                                            </td>
                                            <td style='padding: 12px; text-align: right;'>
                                                <a href='teacher_marks.php?delete={$row['id']}' style='color: #ef4444; text-decoration: none;'>Delete</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='padding:20px; text-align:center; opacity:0.6;'>No marks entered yet.</td></tr>";
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
