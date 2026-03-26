<?php
include 'db.php';
date_default_timezone_set('Asia/Kolkata');

// Security: Only Teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['name'];

// 1. Get Teacher's Department ID automatically
$d_res = $conn->query("SELECT dept_id FROM users WHERE id=$teacher_id");
$dept_id = $d_res->fetch_assoc()['dept_id'];

// 2. Handle File Upload
if (isset($_POST['upload_file'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $upload_date = date('Y-m-d');
    
    // File Logic
    if (isset($_FILES['material']) && $_FILES['material']['error'] == 0) {
        $upload_dir = 'uploads/';
        // Unique Name: timestamp_filename
        $file_name = time() . "_" . basename($_FILES['material']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Move file
        if (move_uploaded_file($_FILES['material']['tmp_name'], $target_file)) {
            // Save to DB
            $sql = "INSERT INTO materials (dept_id, teacher_id, title, file_path, upload_date) 
                    VALUES ($dept_id, $teacher_id, '$title', '$target_file', '$upload_date')";
            $conn->query($sql);
            header("Location: upload_materials.php");
            exit();
        } else {
            $error = "Failed to move uploaded file.";
        }
    }
}

// 3. Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Verify this material belongs to this teacher (Security)
    $check = $conn->query("SELECT * FROM materials WHERE id=$id AND teacher_id=$teacher_id");
    if($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        if(file_exists($row['file_path'])) { unlink($row['file_path']); } // Delete actual file
        $conn->query("DELETE FROM materials WHERE id=$id");
    }
    header("Location: upload_materials.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Materials // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="teacher_dashboard.php" class="nav-item">Classroom Overview</a>
                <a href="mark_attendance.php" class="nav-item">Mark Attendance</a>
                <a href="upload_materials.php" class="nav-item active">Upload Materials</a>
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
            <h1 style="margin-bottom: 30px;">Study Materials</h1>
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
                
                <div class="tech-card" style="height: fit-content;">
                    <h3 style="color: var(--accent-glow); margin-bottom: 20px;">Upload New File</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="input-group">
                            <label>Title / Subject</label>
                            <input type="text" name="title" placeholder="e.g. Data Structures Unit 1" required>
                        </div>
                        <div class="input-group">
                            <label>Select File (PDF, Doc, Image)</label>
                            <input type="file" name="material" style="padding: 10px; background: rgba(255,255,255,0.05); width: 100%;" required>
                        </div>
                        <button type="submit" name="upload_file" class="btn-tech" style="background: #8b5cf6;">Upload Material</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="color: white; margin-bottom: 20px;">My Uploads</h3>
                    <?php
                    $sql = "SELECT * FROM materials WHERE teacher_id=$teacher_id ORDER BY id DESC";
                    $res = $conn->query($sql);
                    
                    if($res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            $date = date("M d, Y", strtotime($row['upload_date']));
                            echo "<div style='display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.05); padding:15px; margin-bottom:10px; border-radius:10px;'>
                                    <div>
                                        <div style='font-weight:600; font-size:1rem;'>{$row['title']}</div>
                                        <div style='font-size:0.8rem; color:var(--text-muted);'>Uploaded on $date</div>
                                    </div>
                                    <div style='display:flex; gap:10px;'>
                                        <a href='{$row['file_path']}' target='_blank' style='color:#3b82f6; text-decoration:none; font-size:0.9rem;'>View</a>
                                        <a href='upload_materials.php?delete={$row['id']}' style='color:#ef4444; text-decoration:none; font-size:0.9rem;' onclick='return confirm(\"Delete this file?\")'>Delete</a>
                                    </div>
                                  </div>";
                        }
                    } else {
                        echo "<p style='color:var(--text-muted);'>You haven't uploaded any materials yet.</p>";
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
