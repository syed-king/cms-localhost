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
        header("Location: timetable.php");
        exit();
    }
}

// Delete Logic
if(isset($_GET['delete_id'])) {
    $tid = $_GET['delete_id'];
    $did = $_GET['dept_id'];
    $conn->query("DELETE FROM timetable WHERE id=$tid");
    header("Location: timetable.php?dept_id=$did");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Time Table // Super Tech</title>
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
            border-color: #ec4899; /* Pink for Time Table */
            box-shadow: 0 10px 30px -10px rgba(236, 72, 153, 0.3);
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.1), rgba(255,255,255,0.01));
        }
        .dept-icon { font-size: 2rem; margin-bottom: 15px; color: #ec4899; }
        
        /* Timetable Grid Styles */
        .tt-grid {
            display: grid;
            gap: 15px;
        }
        .day-row {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 20px;
        }
        .day-header {
            font-size: 1.2rem;
            color: #ec4899;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        .class-card {
            background: rgba(0,0,0,0.3);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 3px solid var(--accent-glow);
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
                <a href="timetable.php" class="nav-item active">Time Table</a>
                <a href="exams.php" class="nav-item">Exams</a>
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
                    <h1 style="margin: 0; font-size: 1.8rem;">Department Timetables</h1>
                    <p style="color: var(--text-muted); margin-top: 5px;">Select a department to view or edit schedule.</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    <?php
                    $sql = "SELECT * FROM departments";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<a href='timetable.php?dept_id={$row['id']}' class='dept-card'>
                                    <div class='dept-icon'>📅</div>
                                    <h3 style='margin: 0; font-size: 1.2rem;'>{$row['name']}</h3>
                                    <div style='color:var(--text-muted); margin-top:5px; font-size:0.9rem;'>View Schedule →</div>
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
                        <h1 style="margin: 0; font-size: 1.8rem; color: #ec4899;"><?php echo $dept_name; ?></h1>
                        <p style="color: var(--text-muted); margin-top: 5px;">Weekly Schedule</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                         <a href="edit_timetable.php?dept_id=<?php echo $selected_dept_id; ?>" class="btn-tech" style="text-decoration: none; padding: 10px 20px; background: #ec4899;">
                            + Add New Class
                        </a>
                        <a href="timetable.php" style="padding: 10px 20px; border: 1px solid var(--glass-border); border-radius: 10px; color: white; text-decoration: none; font-size: 0.9rem; display:flex; align-items:center;">
                            Back
                        </a>
                    </div>
                </div>

                <div class="tt-grid">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                    
                    foreach($days as $day) {
                        echo "<div class='day-row'>
                                <div class='day-header'>
                                    $day
                                </div>";
                        
                        // NEW SQL: Joins the users table to get the teacher's name
                        $sql = "SELECT timetable.*, users.name as teacher_name 
                                FROM timetable 
                                LEFT JOIN users ON timetable.teacher_id = users.id 
                                WHERE timetable.dept_id=$selected_dept_id AND day='$day' 
                                ORDER BY time_slot";
                        
                        $res = $conn->query($sql);
                        
                        if($res->num_rows > 0) {
                            while($t = $res->fetch_assoc()) {
                                // Default name if no teacher assigned
                                $teacher_display = $t['teacher_name'] ? $t['teacher_name'] : "No Teacher Assigned";

                                echo "<div class='class-card'>
                                        <div>
                                            <div style='font-weight:bold; color:white;'>{$t['subject']}</div>
                                            <div style='font-size:0.8rem; color:#ec4899; margin-bottom:2px;'>$teacher_display</div>
                                            <div style='font-size:0.85rem; color:var(--text-muted);'>{$t['time_slot']}</div>
                                        </div>
                                        <div style='display:flex; gap:10px;'>
                                            <a href='edit_timetable.php?edit_id={$t['id']}&dept_id=$selected_dept_id' style='color:#f59e0b; text-decoration:none; font-size:0.85rem;'>Edit</a>
                                            <a href='timetable.php?delete_id={$t['id']}&dept_id=$selected_dept_id' style='color:#ef4444; text-decoration:none; font-size:0.85rem;' onclick='return confirm(\"Remove this class?\")'>Remove</a>
                                        </div>
                                      </div>";
                            }
                        } else {
                            echo "<div style='color:var(--text-muted); font-size:0.9rem; font-style:italic;'>No classes scheduled.</div>";
                        }
                        
                        echo "</div>"; // End day-row
                    }
                    ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</body>
</html>
