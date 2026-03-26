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

// Handle Delete Teacher
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$del_id");
    header("Location: teacher_salary.php?dept_id=$dept_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Salary // Super Tech</title>
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
                <a href="teacher_salary.php" class="nav-item active">Teacher Salary</a>
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
                <h1 style="margin-bottom: 10px;">Teacher Salaries</h1>
                <p style="color: var(--text-muted); margin-bottom: 30px;">Select a department to manage payroll.</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    <?php
                    $sql = "SELECT departments.*, COUNT(users.id) as t_count 
                            FROM departments 
                            LEFT JOIN users ON departments.id = users.dept_id AND users.role='teacher' 
                            GROUP BY departments.id";
                    $result = $conn->query($sql);

                    while($row = $result->fetch_assoc()) {
                        echo "<div class='tech-card' style='text-align: center; padding: 30px;'>
                                <div style='font-size: 2rem; margin-bottom: 10px; color: #f59e0b;'>$</div>
                                <h3 style='margin: 0 0 5px 0;'>{$row['name']}</h3>
                                <p style='color: var(--text-muted); font-size: 0.9rem;'>{$row['t_count']} Teachers</p>
                                <a href='teacher_salary.php?dept_id={$row['id']}' class='btn-tech' style='display:inline-block; margin-top:15px; text-decoration:none;'>Manage Salaries</a>
                              </div>";
                    }
                    ?>
                </div>

            <?php else: ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <h1 style="color: #f59e0b; margin: 0;"><?php echo $dept_name; ?></h1>
                        <p style="color: var(--text-muted); margin: 5px 0 0 0;">Payroll Management</p>
                    </div>
                    <a href="teacher_salary.php" style="color: var(--text-muted); text-decoration: none; border: 1px solid var(--glass-border); padding: 8px 15px; border-radius: 6px;">← Back to Departments</a>
                </div>

                <div class="tech-card" style="padding: 15px; margin-bottom: 20px; border: 1px solid #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="teacherSearch" placeholder="Search Teacher Name or Email..." 
                               style="flex: 1; padding: 12px; background: transparent; border: none; color: white; outline: none; font-size: 1rem;">
                        <button style="background: #f59e0b; border: none; padding: 0 25px; border-radius: 6px; color: black; font-weight: bold;">Search</button>
                    </div>
                </div>

                <div class="tech-card" style="padding: 0; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: rgba(255,255,255,0.05);">
                            <tr>
                                <th style="padding: 20px; text-align: left; color: var(--text-muted);">Teacher Name</th>
                                <th style="padding: 20px; text-align: left; color: var(--text-muted);">Email</th>
                                <th style="padding: 20px; text-align: left; color: var(--text-muted);">Current Salary</th>
                                <th style="padding: 20px; text-align: right; color: var(--text-muted);">Action</th>
                            </tr>
                        </thead>
                        
                        <tbody id="teacherTable">
                            <?php
                            $t_sql = "SELECT * FROM users WHERE dept_id = $dept_id AND role = 'teacher' ORDER BY name ASC";
                            $t_res = $conn->query($t_sql);

                            if ($t_res->num_rows > 0) {
                                while($t = $t_res->fetch_assoc()) {
                                    $salary_display = isset($t['salary']) ? number_format($t['salary'], 2) : "0.00";

                                    echo "<tr style='border-bottom: 1px solid var(--glass-border);'>
                                            <td style='padding: 20px; font-weight: 600; font-size: 1.1rem;'>{$t['name']}</td>
                                            <td style='padding: 20px; color: var(--text-muted);'>{$t['email']}</td>
                                            
                                            <td style='padding: 20px; font-weight: bold; color: #10b981; font-size: 1.1rem;'>
                                                $ $salary_display
                                            </td>

                                            <td style='padding: 20px; text-align: right;'>
                                                <a href='edit_salary.php?id={$t['id']}' style='border: 1px solid #f59e0b; color: #f59e0b; padding: 8px 15px; border-radius: 6px; text-decoration: none; margin-right: 10px; font-weight: 500;'>Edit Salary</a>
                                                <a href='teacher_salary.php?dept_id=$dept_id&delete={$t['id']}' style='border: 1px solid #ef4444; color: #ef4444; padding: 8px 15px; border-radius: 6px; text-decoration: none;' onclick='return confirm(\"Delete this teacher?\")'>Delete</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='padding:40px; text-align:center; color:var(--text-muted);'>No teachers found in this department.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('teacherSearch');
            const tableBody = document.getElementById('teacherTable');

            if (searchInput && tableBody) {
                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();
                    const rows = tableBody.getElementsByTagName('tr');

                    for (let i = 0; i < rows.length; i++) {
                        const nameCol = rows[i].getElementsByTagName('td')[0];
                        const emailCol = rows[i].getElementsByTagName('td')[1];

                        if (nameCol || emailCol) {
                            const nameText = nameCol.textContent || nameCol.innerText;
                            const emailText = emailCol.textContent || emailCol.innerText;

                            if (nameText.toLowerCase().indexOf(filter) > -1 || 
                                emailText.toLowerCase().indexOf(filter) > -1) {
                                rows[i].style.display = "";
                            } else {
                                rows[i].style.display = "none";
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
