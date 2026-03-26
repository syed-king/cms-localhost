<?php
include 'db.php';

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// --- 1. CALCULATE TEACHER SALARIES ---
// Sum of 'salary' column for all teachers
$salary_sql = "SELECT SUM(salary) as total_salary FROM users WHERE role='teacher'";
$salary_res = $conn->query($salary_sql);
$salary_data = $salary_res->fetch_assoc();
$total_salary = $salary_data['total_salary'] ? $salary_data['total_salary'] : 0;


// --- 2. CALCULATE EXPECTED FEES ---
// This is tricky: We need to multiply (Student Count in Dept X) * (Fee for Dept X)
// We use a JOIN query to do this in one go.
$fees_sql = "SELECT SUM(d.fees) as total_expected_fees 
             FROM users u 
             JOIN departments d ON u.dept_id = d.id 
             WHERE u.role = 'student'";
$fees_res = $conn->query($fees_sql);
$fees_data = $fees_res->fetch_assoc();
$total_fees = $fees_data['total_expected_fees'] ? $fees_data['total_expected_fees'] : 0;


// --- 3. PENDING DUES (Logic Placeholder) ---
// Since we don't have a 'payments' table yet, we will assume 
// Total Fees = Pending Dues for now (or you can set it to 0 if you prefer).
$pending_dues = $total_fees; 
$collected = 0; // If you had a payments table, this would be (Total - Pending)

?>

<!DOCTYPE html>
<html>
<head>
    <title>Financials // Super Tech</title>
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
                <a href="financials.php" class="nav-item active">Financials</a>
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
            <h1>Financial Overview</h1>
            <p style="color: var(--text-muted);">Real-time financial analysis.</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
                
                <div class="tech-card">
                    <h3 style="margin:0 0 10px 0; color:var(--text-muted); font-size:0.9rem;">Total Expected Fees</h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #10b981;">
                        $ <?php echo number_format($total_fees, 2); ?>
                    </div>
                    <div style="font-size: 0.8rem; opacity: 0.5;">From all active students</div>
                </div>

                <div class="tech-card">
                    <h3 style="margin:0 0 10px 0; color:var(--text-muted); font-size:0.9rem;">Pending Dues</h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #ef4444;">
                        $ <?php echo number_format($pending_dues, 2); ?>
                    </div>
                    <div style="font-size: 0.8rem; opacity: 0.5;">Uncollected Revenue</div>
                </div>

                <div class="tech-card">
                    <h3 style="margin:0 0 10px 0; color:var(--text-muted); font-size:0.9rem;">Monthly Salary Expense</h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #f59e0b;">
                        $ <?php echo number_format($total_salary, 2); ?>
                    </div>
                    <div style="font-size: 0.8rem; opacity: 0.5;">For all teachers</div>
                </div>

            </div>

            <div class="tech-card" style="margin-top: 30px;">
                <h3 style="margin-bottom: 20px;">Financial Health</h3>
                <p>
                    <strong>Net Profit (Projected):</strong> 
                    <span style="color: #10b981;">$ <?php echo number_format($total_fees - $total_salary, 2); ?></span>
                </p>
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    * This calculation assumes all students pay their fees and salaries are paid once a month.
                </p>
            </div>

        </div>
    </div>
</body>
</html>
