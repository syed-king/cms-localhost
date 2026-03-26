<?php
include 'db.php';

// Security: Only Teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Get Teacher's Department
$t_res = $conn->query("SELECT dept_id FROM users WHERE id=$teacher_id");
$dept_id = $t_res->fetch_assoc()['dept_id'];

// --- HANDLE APPROVE / REJECT ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $status = ($_GET['action'] == 'approve') ? 'Approved' : 'Rejected';
    
    $conn->query("UPDATE leave_requests SET status='$status' WHERE id=$request_id");
    header("Location: teacher_leave.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Requests // Super Tech</title>
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
                <a href="teacher_marks.php" class="nav-item">Enter Marks</a>
                <a href="dept_schedule.php" class="nav-item">Dept. Schedule</a>
                <a href="teacher_leave.php" class="nav-item active">student leave</a>
                <a href="teacher_id_card.php" class="nav-item">ID card</a>
                </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <h1>Student Leave Requests</h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Manage leave applications for your department.</p>

            <div class="tech-card" style="padding: 0; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: rgba(255,255,255,0.05);">
                        <tr>
                            <th style="padding: 15px; text-align: left;">Student Name</th>
                            <th style="padding: 15px; text-align: left;">Duration</th>
                            <th style="padding: 15px; text-align: left;">Reason</th>
                            <th style="padding: 15px; text-align: center;">Status / Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch requests for this department
                        $sql = "SELECT l.*, u.name as student_name 
                                FROM leave_requests l 
                                JOIN users u ON l.student_id = u.id 
                                WHERE l.dept_id = $dept_id 
                                ORDER BY FIELD(l.status, 'Pending', 'Approved', 'Rejected'), l.request_date DESC";
                        $res = $conn->query($sql);

                        if($res->num_rows > 0) {
                            while($row = $res->fetch_assoc()) {
                                $status = $row['status'];
                                
                                echo "<tr style='border-bottom: 1px solid var(--glass-border);'>
                                        <td style='padding: 15px; font-weight: 500;'>{$row['student_name']}</td>
                                        <td style='padding: 15px; font-size: 0.9rem;'>
                                            {$row['start_date']} to {$row['end_date']}
                                        </td>
                                        <td style='padding: 15px; color: var(--text-muted); font-style: italic;'>
                                            \"{$row['reason']}\"
                                        </td>
                                        <td style='padding: 15px; text-align: center;'>";
                                        
                                        if ($status == 'Pending') {
                                            echo "<a href='teacher_leave.php?action=approve&id={$row['id']}' 
                                                     class='btn-tech' style='background:#10b981; padding: 6px 12px; font-size: 0.8rem; margin-right: 5px; text-decoration:none;'>Approve</a>
                                                  <a href='teacher_leave.php?action=reject&id={$row['id']}' 
                                                     class='btn-tech' style='background:#ef4444; padding: 6px 12px; font-size: 0.8rem; text-decoration:none;'>Reject</a>";
                                        } else {
                                            $color = ($status == 'Approved') ? '#10b981' : '#ef4444';
                                            echo "<span style='color: $color; font-weight: bold;'>$status</span>";
                                        }

                                echo "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='padding:30px; text-align:center; opacity:0.6;'>No leave requests found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
