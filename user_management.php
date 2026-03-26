<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// --- 1. HANDLE ADD USER ---
if (isset($_POST['add_user'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $_POST['password']; 
    $role = $_POST['role'];
    $phone = $conn->real_escape_string($_POST['phone']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $raw_dept_id = $_POST['dept_id'];

    if ($role == 'admin') {
        $dept_val = "NULL"; 
    } else {
        $dept_val = "'$raw_dept_id'";
    }

    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $msg = "<div style='background:rgba(239, 68, 68, 0.2); color:#ef4444; padding:10px; border-radius:8px; margin-bottom:15px;'>Error: Email already exists!</div>";
    } else {
        $sql = "INSERT INTO users (name, email, password, role, dept_id, phone, designation) 
                VALUES ('$name', '$email', '$pass', '$role', $dept_val, '$phone', '$designation')";
        if ($conn->query($sql)) {
            $msg = "<div style='background:rgba(16, 185, 129, 0.2); color:#10b981; padding:10px; border-radius:8px; margin-bottom:15px;'>User Added Successfully!</div>";
        } else {
            $msg = "<div style='color:red;'>Error: " . $conn->error . "</div>";
        }
    }
}

// --- 2. HANDLE DELETE USER ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id=$id");
    }
    header("Location: user_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management // Super Tech</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px; font-weight: 600;">SUPER TECH</h2>
            <nav>
                <a href="admin_dashboard.php" class="nav-item">Dashboard Overview</a>
                <a href="departments.php" class="nav-item">Departments</a>
                <a href="user_management.php" class="nav-item active">User Management</a>
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
            <h1 style="margin-bottom: 10px;">User Management</h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Search and manage teachers, students, and admins.</p>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
                
                <div class="tech-card" style="height: fit-content;">
                    <h3 style="color: var(--accent-glow); margin-bottom: 20px;">Register New User</h3>
                    <?php if(isset($msg)) echo $msg; ?>
                    
                    <form method="POST">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="name" required placeholder="John Doe">
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" required placeholder="john@supertech.com">
                        </div>
                        
                        <div class="input-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" required placeholder="+91 98765 43210">
                        </div>

                        <div class="input-group">
                            <label>Designation (For Teachers)</label>
                            <input type="text" name="designation" placeholder="e.g. Student / HOD" value="Student">
                        </div>

                        <div class="input-group">
                            <label>Password</label>
                            <input type="password" name="password" required placeholder="••••">
                        </div>
                        <div class="input-group">
                            <label>Role</label>
                            <select name="role" style="width: 100%; padding: 14px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; outline: none;">
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Department (Ignore for Admins)</label>
                            <select name="dept_id" style="width: 100%; padding: 14px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; outline: none;">
                                <?php
                                $d_res = $conn->query("SELECT * FROM departments");
                                while($d = $d_res->fetch_assoc()) {
                                    echo "<option value='{$d['id']}'>{$d['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="add_user" class="btn-tech">Create Account</button>
                    </form>
                </div>

                <div class="tech-card">
                    <h3 style="color: white; margin-bottom: 20px;">System Users</h3>
                    <input type="text" id="searchInput" placeholder="Search by Name, Role, or Department..." style="width: 100%; padding: 12px; margin-bottom: 20px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: white; outline: none; border-color: #3b82f6;">

                    <table style="width: 100%; border-collapse: separate; border-spacing: 0 5px;">
                        <thead>
                            <tr style="color: var(--text-muted); font-size: 0.85rem; text-align: left;">
                                <th style="padding: 10px;">Name / Phone</th>
                                <th style="padding: 10px;">Email</th> <th style="padding: 10px;">Role / Desig.</th>
                                <th style="padding: 10px;">Dept</th>
                                <th style="padding: 10px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <?php
                            $sql = "SELECT users.*, departments.name as dept_name 
                                    FROM users 
                                    LEFT JOIN departments ON users.dept_id = departments.id 
                                    ORDER BY users.id DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $roleColor = '#3b82f6'; 
                                    if($row['role'] == 'teacher') $roleColor = '#10b981'; 
                                    if($row['role'] == 'admin') $roleColor = '#ef4444'; 
                                    
                                    $dept_display = $row['dept_name'] ? $row['dept_name'] : "<span style='opacity:0.3;'>-</span>";
                                    $phone_display = $row['phone'] ? $row['phone'] : "No Phone";
                                    $desig_display = $row['designation'] ? $row['designation'] : "-";

                                    echo "<tr style='background: rgba(255,255,255,0.02);'>
                                            <td style='padding: 15px; border-radius: 8px 0 0 8px;'>
                                                <div style='font-weight: 500;'>{$row['name']}</div>
                                                <div style='font-size: 0.8rem; color: var(--text-muted);'>{$phone_display}</div>
                                            </td>
                                            
                                            <td style='padding: 15px; font-size: 0.9rem; color: #fff;'>
                                                {$row['email']}
                                            </td>

                                            <td style='padding: 15px;'>
                                                <span style='color: $roleColor; text-transform: capitalize;'>{$row['role']}</span><br>
                                                <span style='font-size: 0.8rem; opacity:0.7;'>$desig_display</span>
                                            </td>
                                            <td style='padding: 15px;'>$dept_display</td>
                                            
                                            <td style='padding: 15px; text-align: right; border-radius: 0 8px 8px 0;'>
                                                <a href='edit_user.php?id={$row['id']}' style='color: #f59e0b; text-decoration: none; font-size: 0.85rem; margin-right: 15px; font-weight: 500;'>Edit</a>
                                                <a href='user_management.php?delete={$row['id']}' style='color: #ef4444; text-decoration: none; font-size: 0.85rem;' onclick='return confirm(\"Delete this user?\")'>Delete</a>
                                            </td>
                                          </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#userTable tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
