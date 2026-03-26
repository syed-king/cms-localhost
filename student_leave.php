<?php
include 'db.php';

// Security: Only Student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$msg = "";

// --- 1. HANDLE LEAVE APPLICATION ---
if (isset($_POST['apply_leave'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $conn->real_escape_string($_POST['reason']);
    
    // Get Student's Department
    $u_res = $conn->query("SELECT dept_id FROM users WHERE id=$student_id");
    $dept_id = $u_res->fetch_assoc()['dept_id'];

    $sql = "INSERT INTO leave_requests (student_id, dept_id, start_date, end_date, reason) 
            VALUES ($student_id, $dept_id, '$start_date', '$end_date', '$reason')";
    
    if ($conn->query($sql)) {
        $msg = "<div style='color:#10b981; margin-bottom:15px;'>Leave application submitted successfully!</div>";
    } else {
        $msg = "<div style='color:#ef4444; margin-bottom:15px;'>Error submitting request.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply Leave // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px;">SUPER TECH</h2>
            <nav>
                <a href="student_dashboard.php" class="nav-item">My Dashboard</a>
                <a href="student_schedule.php" class="nav-item">Class Schedule</a>
                <a href="student_results.php" class="nav-item">My Results</a>
                <a href="student_materials.php" class="nav-item">Study Materials</a>
                <a href="student_exams.php" class="nav-item">Upcoming Exams</a>
                <a href="student_leave.php" class="nav-item active">leave request</a>
                <a href="student_id_card.php" class="nav-item">ID card</a>
        	</nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <h1>Leave Application</h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Request leave from your department teacher.</p>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
                
                <div class="tech-card" style="height: fit-content;">
                    <h3 style="margin-bottom: 20px;">New Request</h3>
                    <?php echo $msg; ?>
                    <form method="POST">
                        <div class="input-group">
                            <label>From Date</label>
                            <input type="date" name="start_date" required>
                        </div>
                        <div class="input-group">
                            <label>To Date</label>
                            <input type="date" name="end_date" required>
                        </div>
                        <div class="input-group">
                            <label>Reason</label>
                            <textarea name="reason" rows="4" required placeholder="e.g., Sick leave, Family function..." 
                                      style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; outline: none;"></textarea>
                        </div>
                        <button type="submit" name="apply_leave" class="btn-tech">Submit Request</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="margin-bottom: 20px;">My Request History</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: rgba(255,255,255,0.05);">
                            <tr>
                                <th style="padding: 12px; text-align: left;">Dates</th>
                                <th style="padding: 12px; text-align: left;">Reason</th>
                                <th style="padding: 12px; text-align: left;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM leave_requests WHERE student_id=$student_id ORDER BY id DESC";
                            $res = $conn->query($sql);
                            
                            if($res->num_rows > 0) {
                                while($row = $res->fetch_assoc()) {
                                    // Status Colors
                                    $statusColor = '#f59e0b'; // Orange (Pending)
                                    if($row['status'] == 'Approved') $statusColor = '#10b981'; // Green
                                    if($row['status'] == 'Rejected') $statusColor = '#ef4444'; // Red

                                    echo "<tr style='border-bottom: 1px solid var(--glass-border);'>
                                            <td style='padding: 12px; font-size: 0.9rem;'>
                                                {$row['start_date']} <span style='color:var(--text-muted);'>to</span><br>{$row['end_date']}
                                            </td>
                                            <td style='padding: 12px; color: var(--text-muted);'>{$row['reason']}</td>
                                            <td style='padding: 12px; font-weight: bold; color: $statusColor;'>{$row['status']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='padding:20px; text-align:center; opacity:0.6;'>No leave history found.</td></tr>";
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
