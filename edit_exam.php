<?php
include 'db.php';

// Security
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : '';
$edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';

// Data holders
$name_val = "";
$date_val = "";
$time_val = "";
$room_val = "";

// If Editing, Fetch Existing Data
if($edit_id) {
    $q = $conn->query("SELECT * FROM exams WHERE id=$edit_id");
    if($q->num_rows > 0) {
        $data = $q->fetch_assoc();
        $name_val = $data['exam_name'];
        $date_val = $data['exam_date'];
        $time_val = $data['time_slot'];
        $room_val = $data['room_no'];
    }
}

// Handle Form Submit
if(isset($_POST['save_exam'])) {
    $name = $_POST['exam_name'];
    $date = $_POST['exam_date'];
    $time = $_POST['time_slot'];
    $room = $_POST['room_no'];
    
    if($edit_id) {
        // Update
        $sql = "UPDATE exams SET exam_name='$name', exam_date='$date', time_slot='$time', room_no='$room' WHERE id=$edit_id";
    } else {
        // Insert
        $sql = "INSERT INTO exams (dept_id, exam_name, exam_date, time_slot, room_no) VALUES ('$dept_id', '$name', '$date', '$time', '$room')";
    }
    
    if($conn->query($sql) === TRUE) {
        header("Location: exams.php?dept_id=$dept_id");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exam // Super Tech</title>
    <link rel="stylesheet" href="style.css">
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
        </div>
        
        <div class="main-area">
            <div style="max-width: 500px; margin: 50px auto;">
                <h2 style="color: white; margin-bottom: 20px;">
                    <?php echo $edit_id ? "Edit Exam" : "Schedule New Exam"; ?>
                </h2>
                
                <div class="tech-card">
                    <form method="POST">
                        <div class="input-group">
                            <label>Exam Subject / Name</label>
                            <input type="text" name="exam_name" value="<?php echo $name_val; ?>" placeholder="e.g. Advanced Calculus Mid-Term" required>
                        </div>

                        <div class="input-group">
                            <label>Date</label>
                            <input type="date" name="exam_date" value="<?php echo $date_val; ?>" style="color-scheme: dark;" required>
                        </div>

                        <div class="input-group">
                            <label>Time Slot</label>
                            <input type="text" name="time_slot" value="<?php echo $time_val; ?>" placeholder="e.g. 10:00 AM - 01:00 PM" required>
                        </div>

                        <div class="input-group">
                            <label>Room Number</label>
                            <input type="text" name="room_no" value="<?php echo $room_val; ?>" placeholder="e.g. Hall 4B">
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" name="save_exam" class="btn-tech" style="background: #06b6d4;">Save Schedule</button>
                            
                            <a href="exams.php?dept_id=<?php echo $dept_id; ?>" style="
                                display: block; width: 100%; padding: 14px; text-align: center; color: var(--text-muted); text-decoration: none; border: 1px solid var(--glass-border); border-radius: 12px; margin-top: 10px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>