<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Check if a Department is selected
$selected_dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : null;
$dept_name = "";

if ($selected_dept_id) {
    $d_query = $conn->query("SELECT name FROM departments WHERE id = $selected_dept_id");
    if ($d_query->num_rows > 0) {
        $dept_name = $d_query->fetch_assoc()['name'];
    } else {
        header("Location: exams.php");
        exit();
    }
}

// Delete Logic
if(isset($_GET['delete_id'])) {
    $eid = $_GET['delete_id'];
    $did = $_GET['dept_id'];
    $conn->query("DELETE FROM exams WHERE id=$eid");
    header("Location: exams.php?dept_id=$did");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Schedule // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Card Styles */
        .dept-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
            border: 1px solid var(--glass-border);
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: block;
            color: white;
        }
        .dept-card:hover {
            transform: translateY(-5px);
            border-color: #06b6d4; /* Cyan for Exams */
            box-shadow: 0 10px 30px -10px rgba(6, 182, 212, 0.3);
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(255,255,255,0.01));
        }
        .dept-icon { font-size: 2rem; margin-bottom: 15px; color: #06b6d4; }
        
        .exam-count {
            background: rgba(0,0,0,0.3);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="admin_dashboard.php" class="nav-item">Dashboard Overview</a>
                <a href="departments.php" class="nav-item">Departments</a>
                <a href="user_management.php" class="nav-item">User Management</a>
                <a href="financials.php" class="nav-item">Financials</a>
                <a href="student_list.php" class="nav-item">Student List</a>
                <a href="teacher_salary.php" class="nav-item">Teacher Salary</a>
                <a href="timetable.php" class="nav-item">Time Table</a>
                <a href="exams.php" class="nav-item active">Exams</a>
                <a href="admin_events.php" class="nav-item">Campus Events</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            
            <?php if (!$selected_dept_id): ?>
                
                <div style="margin-bottom: 30px;">
                    <h1 style="margin: 0; font-size: 1.8rem;">Exam Schedules</h1>
                    <p style="color: var(--text-muted); margin-top: 5px;">Select a department to manage examinations.</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    <?php
                    // Get departments + count of EXAMS
                    $sql = "SELECT d.id, d.name, COUNT(e.id) as count 
                            FROM departments d 
                            LEFT JOIN exams e ON d.id = e.dept_id
                            GROUP BY d.id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<a href='exams.php?dept_id={$row['id']}' class='dept-card'>
                                    <div class='dept-icon'>📝</div>
                                    <h3 style='margin: 0; font-size: 1.2rem;'>{$row['name']}</h3>
                                    <div class='exam-count'>{$row['count']} Exams</div>
                                  </a>";
                        }
                    } else {
                        echo "<p style='color:var(--text-muted);'>No departments found.</p>";
                    }
                    ?>
                </div>

            <?php else: ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <h1 style="margin: 0; font-size: 1.8rem; color: #06b6d4;"><?php echo $dept_name; ?></h1>
                        <p style="color: var(--text-muted); margin-top: 5px;">Upcoming Examinations</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                         <a href="edit_exam.php?dept_id=<?php echo $selected_dept_id; ?>" class="btn-tech" style="text-decoration: none; padding: 10px 20px; background: #06b6d4;">
                            + Schedule Exam
                        </a>
                        <a href="exams.php" style="padding: 10px 20px; border: 1px solid var(--glass-border); border-radius: 10px; color: white; text-decoration: none; font-size: 0.9rem; display:flex; align-items:center;">
                            Back
                        </a>
                    </div>
                </div>

                <div class="tech-card">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Exam Name / Subject</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch exams sorted by DATE
                            $e_sql = "SELECT * FROM exams WHERE dept_id=$selected_dept_id ORDER BY exam_date ASC";
                            $e_res = $conn->query($e_sql);

                            if ($e_res->num_rows > 0) {
                                while($e = $e_res->fetch_assoc()) {
                                    // Format Date nicely (e.g., Jan 15, 2026)
                                    $nice_date = date("M d, Y", strtotime($e['exam_date']));
                                    
                                    echo "<tr>
                                            <td style='font-family:monospace; color:#06b6d4; font-size:1rem;'>$nice_date</td>
                                            <td><div style='font-weight:bold;'>{$e['exam_name']}</div></td>
                                            <td style='color:var(--text-muted);'>{$e['time_slot']}</td>
                                            <td><span style='background:rgba(255,255,255,0.1); padding:3px 8px; border-radius:4px; font-size:0.85rem;'>{$e['room_no']}</span></td>
                                            <td>
                                                <div style='display:flex; gap:10px;'>
                                                    <a href='edit_exam.php?edit_id={$e['id']}&dept_id=$selected_dept_id' style='color:#f59e0b; text-decoration:none;'>Edit</a>
                                                    <a href='exams.php?delete_id={$e['id']}&dept_id=$selected_dept_id' style='color:#ef4444; text-decoration:none;' onclick='return confirm(\"Delete this exam?\")'>Delete</a>
                                                </div>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; padding: 40px; color: var(--text-muted);'>No exams scheduled yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>
</body>
</html>
