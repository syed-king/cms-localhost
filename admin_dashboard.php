<?php
include 'db.php';
if ($_SESSION['role'] != 'admin') header("Location: index.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="admin_dashboard.php" class="nav-item active">Dashboard Overview</a>
                <a href="departments.php" class="nav-item">Departments</a>
                <a href="user_management.php" class="nav-item">User Management</a>
                <a href="financials.php" class="nav-item">Financials</a>
                <a href="student_list.php" class="nav-item">Student List</a>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <div>
                    <h1 style="margin: 0; font-size: 1.8rem;">Dashboard</h1>
                    <p style="color: var(--text-muted); margin: 5px 0 0 0;">Welcome back, <?php echo $_SESSION['name']; ?></p>
                </div>
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--accent-glow), var(--accent-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    A
                </div>
            </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
                
                <a href="student_list.php" style="text-decoration: none; color: inherit;">
                    <div class="tech-card" style="transition: transform 0.2s; cursor: pointer;">
                        <h3>Total Students</h3>
                        <div class="value">
                            <?php
                            $s_res = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'");
                            echo $s_res->fetch_assoc()['c'];
                            ?>
                        </div>
                    </div>
                </a>

                <a href="teacher_salary.php" style="text-decoration: none; color: inherit;">
                    <div class="tech-card" style="transition: transform 0.2s; cursor: pointer;">
                        <h3>Active Teachers</h3>
                        <div class="value">
                            <?php
                            $t_res = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='teacher'");
                            echo $t_res->fetch_assoc()['c'];
                            ?>
                        </div>
                    </div>
                </a>

                <a href="departments.php" style="text-decoration: none; color: inherit;">
                    <div class="tech-card" style="transition: transform 0.2s; cursor: pointer;">
                        <h3>Departments</h3>
                        <div class="value">
                            <?php
                            $d_res = $conn->query("SELECT COUNT(*) as c FROM departments");
                            echo $d_res->fetch_assoc()['c'];
                            ?>
                        </div>
                    </div>
                </a>

            </div>

            <div class="tech-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 1.1rem; color: white;">Recent User Activity</h3>
                    <button style="background: none; border: 1px solid var(--glass-border); color: var(--accent-glow); padding: 5px 15px; border-radius: 8px; cursor: pointer;">View All</button>
                </div>
                <table>
                    <tbody>
                        <?php
                        // Fetch the 5 most recently created users
                        $sql = "SELECT * FROM users ORDER BY id DESC LIMIT 5";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                // Color Code the Roles
                                $roleColor = 'white'; 
                                if($row['role'] == 'admin') $roleColor = '#ef4444';   // Red
                                if($row['role'] == 'teacher') $roleColor = '#f59e0b'; // Orange/Gold
                                if($row['role'] == 'student') $roleColor = '#3b82f6'; // Blue

                                echo "<tr>
                                        <td>
                                            <div style='font-weight: 500;'>{$row['name']}</div>
                                            <div style='font-size: 0.8rem; color: var(--text-muted);'>{$row['email']}</div>
                                        </td>
                                        <td style='color: $roleColor; text-transform: capitalize; font-weight: 600;'>
                                            {$row['role']}
                                        </td>
                                        <td>
                                            <span style='color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;'>
                                                Active
                                            </span>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align: center; color: var(--text-muted); padding: 20px;'>No activity found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
