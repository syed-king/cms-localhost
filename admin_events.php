<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Handle Add Event
if (isset($_POST['add_event'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $date = $_POST['event_date'];
    $desc = $conn->real_escape_string($_POST['description']);
    $type = $_POST['type'];
    $attachment_path = NULL;

    // --- FILE UPLOAD LOGIC ---
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = 'uploads/';
        // Create unique name: event_TIMESTAMP_filename
        $file_name = "event_" . time() . "_" . basename($_FILES['attachment']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Move file to folder
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attachment_path = $target_file;
        }
    }

    $sql = "INSERT INTO events (title, event_date, description, type, attachment) 
            VALUES ('$title', '$date', '$desc', '$type', " . ($attachment_path ? "'$attachment_path'" : "NULL") . ")";
            
    if($conn->query($sql)){
        header("Location: admin_events.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Optional: Delete the actual file from folder
    $q = $conn->query("SELECT attachment FROM events WHERE id=$id");
    $file = $q->fetch_assoc()['attachment'];
    if($file && file_exists($file)) { unlink($file); }

    $conn->query("DELETE FROM events WHERE id=$id");
    header("Location: admin_events.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events // Super Tech</title>
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
                <a href="exams.php" class="nav-item">Exams</a>
                <a href="admin_events.php" class="nav-item active">Campus Events</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <h1 style="margin-bottom: 30px;">Campus Events & Notices</h1>
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
                
                <div class="tech-card" style="height: fit-content;">
                    <h3 style="color: var(--accent-glow); margin-bottom: 20px;">Post New Event</h3>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="input-group">
                            <label>Event Title</label>
                            <input type="text" name="title" placeholder="e.g. Annual Tech Symposium" required>
                        </div>
                        <div class="input-group">
                            <label>Event Date</label>
                            <input type="date" name="event_date" style="color-scheme: dark;" required>
                        </div>
                        <div class="input-group">
                            <label>Type</label>
                            <select name="type" style="width: 100%; padding: 14px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; outline: none;">
                                <option value="General">General Notice</option>
                                <option value="Meeting">Staff Meeting</option>
                                <option value="Holiday">Holiday</option>
                                <option value="Workshop">Workshop</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label>Attachment (Image or PDF)</label>
                            <input type="file" name="attachment" style="padding: 10px; background: rgba(255,255,255,0.05);">
                        </div>

                        <div class="input-group">
                            <label>Description / Details</label>
                            <textarea name="description" rows="4" style="width: 100%; padding: 14px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; outline: none; font-family: inherit;" placeholder="Enter event details..."></textarea>
                        </div>
                        <button type="submit" name="add_event" class="btn-tech">Post Event</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="color: white; margin-bottom: 20px;">Upcoming Events</h3>
                    <?php
                    $sql = "SELECT * FROM events ORDER BY event_date ASC";
                    $result = $conn->query($sql);
                    
                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $date_display = date("M d, Y", strtotime($row['event_date']));
                            
                            // Check if attachment exists
                            $attach_btn = "";
                            if($row['attachment']) {
                                $attach_btn = "<a href='{$row['attachment']}' target='_blank' style='display:inline-block; margin-top:10px; font-size:0.85rem; color:#3b82f6; text-decoration:none; border:1px solid #3b82f6; padding:4px 10px; border-radius:5px;'>📎 View Attachment</a>";
                            }

                            echo "<div style='background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; margin-bottom: 15px; border-left: 4px solid var(--accent-glow); position: relative;'>
                                    <div style='display: flex; justify-content: space-between; align-items: start;'>
                                        <div>
                                            <span style='background: var(--accent-glow); color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; text-transform: uppercase;'>{$row['type']}</span>
                                            <h3 style='margin: 10px 0 5px 0; color: white;'>{$row['title']}</h3>
                                            <div style='color: var(--text-muted); font-size: 0.9rem;'>📅 $date_display</div>
                                            <p style='color: #cbd5e1; margin-top: 10px; font-size: 0.95rem;'>{$row['description']}</p>
                                            $attach_btn
                                        </div>
                                        <a href='admin_events.php?delete={$row['id']}' style='color: #ef4444; text-decoration: none; font-size: 1.2rem; padding: 5px;' onclick='return confirm(\"Delete this event?\")'>×</a>
                                    </div>
                                  </div>";
                        }
                    } else {
                        echo "<p style='color: var(--text-muted);'>No events scheduled.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
