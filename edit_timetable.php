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
$day_val = "";
$time_val = "";
$sub_val = "";
$teacher_val = ""; // New variable for teacher

// If Editing, Fetch Existing Data
if($edit_id) {
    $q = $conn->query("SELECT * FROM timetable WHERE id=$edit_id");
    if($q->num_rows > 0) {
        $data = $q->fetch_assoc();
        $day_val = $data['day'];
        $time_val = $data['time_slot'];
        $sub_val = $data['subject'];
        $teacher_val = $data['teacher_id']; // Fetch saved teacher
    }
}

// Handle Form Submit
if(isset($_POST['save_schedule'])) {
    $day = $_POST['day'];
    $time = $_POST['time_slot'];
    $subject = $_POST['subject'];
    $teacher_id = $_POST['teacher_id']; // Get selected teacher
    
    if($edit_id) {
        // Update existing
        $sql = "UPDATE timetable SET day='$day', time_slot='$time', subject='$subject', teacher_id='$teacher_id' WHERE id=$edit_id";
    } else {
        // Insert new
        $sql = "INSERT INTO timetable (dept_id, day, time_slot, subject, teacher_id) VALUES ('$dept_id', '$day', '$time', '$subject', '$teacher_id')";
    }
    
    if($conn->query($sql) === TRUE) {
        header("Location: timetable.php?dept_id=$dept_id");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Timetable // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Custom Select Style to match inputs */
        select.custom-select {
            width: 100%;
            padding: 14px 16px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: white;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            outline: none;
            cursor: pointer;
        }
        select.custom-select option {
            background: #0f172a; /* Dark background for dropdown options */
            color: white;
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
        </div>
        
        <div class="main-area">
            <div style="max-width: 500px; margin: 50px auto;">
                <h2 style="color: white; margin-bottom: 20px;">
                    <?php echo $edit_id ? "Edit Schedule" : "Add New Class"; ?>
                </h2>
                
                <div class="tech-card">
                    <form method="POST">
                        <div class="input-group">
                            <label>Select Day</label>
                            <select name="day" class="custom-select" required>
                                <option value="Monday" <?php if($day_val=='Monday') echo 'selected'; ?>>Monday</option>
                                <option value="Tuesday" <?php if($day_val=='Tuesday') echo 'selected'; ?>>Tuesday</option>
                                <option value="Wednesday" <?php if($day_val=='Wednesday') echo 'selected'; ?>>Wednesday</option>
                                <option value="Thursday" <?php if($day_val=='Thursday') echo 'selected'; ?>>Thursday</option>
                                <option value="Friday" <?php if($day_val=='Friday') echo 'selected'; ?>>Friday</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Time Slot (e.g. 09:00 - 10:00)</label>
                            <input type="text" name="time_slot" value="<?php echo $time_val; ?>" placeholder="09:00 AM - 10:00 AM" required>
                        </div>

                        <div class="input-group">
                            <label>Subject / Activity</label>
                            <input type="text" name="subject" value="<?php echo $sub_val; ?>" placeholder="e.g. Data Structures" required>
                        </div>

                        <div class="input-group">
                            <label>Assign Teacher</label>
                            <select name="teacher_id" class="custom-select" required>
                                <option value="" disabled selected>Select a Teacher</option>
                                <?php
                                // Fetch only Teachers from the database
                                $t_sql = "SELECT id, name FROM users WHERE role='teacher'";
                                $t_res = $conn->query($t_sql);
                                
                                if($t_res->num_rows > 0) {
                                    while($t = $t_res->fetch_assoc()) {
                                        $selected = ($t['id'] == $teacher_val) ? "selected" : "";
                                        echo "<option value='{$t['id']}' $selected>{$t['name']}</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No teachers found</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" name="save_schedule" class="btn-tech" style="background: #ec4899;">Save Class</button>
                            
                            <a href="timetable.php?dept_id=<?php echo $dept_id; ?>" style="
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