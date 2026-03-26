<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Logic: Check if Department is selected
$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : null;
$dept_name = "";

if($dept_id) {
    $d_sql = "SELECT name FROM departments WHERE id=$dept_id";
    $d_res = $conn->query($d_sql);
    if($d_res->num_rows > 0) {
        $dept_name = $d_res->fetch_assoc()['name'];
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$del_id");
    header("Location: student_list.php?dept_id=$dept_id"); // Stay on same page
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student List // Super Tech</title>
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
                <a href="student_list.php" class="nav-item active">Student List</a>
                <a href="teacher_salary.php" class="nav-item">Teacher Salary</a>
                <a href="timetable.php" class="nav-item">Time Table</a>
                <a href="exams.php" class="nav-item">Exams</a>
                <a href="admin_events.php" class="nav-item">Campus Events</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            
            <?php if (!$dept_id): ?>
                <h1 style="margin-bottom: 10px;">Student Directories</h1>
                <p style="color: var(--text-muted); margin-bottom: 30px;">Select a department to view student records and financial dues.</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    <?php
                    $sql = "SELECT departments.*, COUNT(users.id) as s_count 
                            FROM departments 
                            LEFT JOIN users ON departments.id = users.dept_id AND users.role='student' 
                            GROUP BY departments.id";
                    $result = $conn->query($sql);

                    while($row = $result->fetch_assoc()) {
                        echo "<div class='tech-card' style='text-align: center; padding: 30px;'>
                                <div style='font-size: 2rem; margin-bottom: 10px;'>🎓</div>
                                <h3 style='margin: 0 0 5px 0;'>{$row['name']}</h3>
                                <p style='color: var(--text-muted); font-size: 0.9rem;'>{$row['s_count']} Students</p>
                                <a href='student_list.php?dept_id={$row['id']}' class='btn-tech' style='display:inline-block; margin-top:15px; text-decoration:none;'>View Students</a>
                              </div>";
                    }
                    ?>
                </div>

            <?php else: ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <h1 style="color: var(--accent-glow); margin: 0;"><?php echo $dept_name; ?></h1>
                        <p style="color: var(--text-muted); margin: 5px 0 0 0;">Student Financial & Academic Directory</p>
                    </div>
                    <a href="student_list.php" style="color: var(--text-muted); text-decoration: none; border: 1px solid var(--glass-border); padding: 8px 15px; border-radius: 6px;">← Back to Departments</a>
                </div>

                <div class="tech-card" style="padding: 15px; margin-bottom: 20px;">
                    <input type="text" id="searchInput" placeholder="Search Student Name, Email or Fees..." style="width: 100%; padding: 10px; background: transparent; border: none; color: white; outline: none;">
                </div>

                <div class="tech-card" style="padding: 0; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: rgba(255,255,255,0.05);">
                            <tr>
                                <th style="padding: 15px; text-align: left; color: var(--text-muted);">Student Name</th>
                                <th style="padding: 15px; text-align: left; color: var(--text-muted);">Email</th>
                                <th style="padding: 15px; text-align: left; color: var(--text-muted);">Fees Due ($)</th>
                                <th style="padding: 15px; text-align: right; color: var(--text-muted);">Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentTable">
                            <?php
                            $s_sql = "SELECT * FROM users WHERE dept_id = $dept_id AND role = 'student' ORDER BY name ASC";
                            $s_res = $conn->query($s_sql);

                            if ($s_res->num_rows > 0) {
                                while($stu = $s_res->fetch_assoc()) {
                                    
                                    // Color code fees
                                    $fee_color = ($stu['fees_due'] > 0) ? "#ef4444" : "#10b981"; // Red if owing, Green if 0

                                    echo "<tr style='border-bottom: 1px solid var(--glass-border);'>
                                            <td style='padding: 15px; font-weight: 500;'>{$stu['name']}</td>
                                            <td style='padding: 15px; color: var(--text-muted);'>{$stu['email']}</td>
                                            
                                            <td style='padding: 15px; font-weight: bold; color: $fee_color;'>
                                                $ " . number_format($stu['fees_due'], 2) . "
                                            </td>

                                            <td style='padding: 15px; text-align: right;'>
                                                <a href='edit_student_fees.php?id={$stu['id']}' style='color: #f59e0b; text-decoration: none; margin-right: 15px; font-weight: 500;'>Edit Fee</a>
                                                <a href='student_list.php?dept_id=$dept_id&delete={$stu['id']}' style='color: #ef4444; text-decoration: none;' onclick='return confirm(\"Delete this student?\")'>Delete</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='padding:30px; text-align:center; color:var(--text-muted);'>No students found in this department.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        // Check if searchInput exists (it only exists on the second view)
        const searchInput = document.getElementById('searchInput');
        if(searchInput){
            searchInput.addEventListener('keyup', function() {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll('#studentTable tr');

                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    if (text.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
