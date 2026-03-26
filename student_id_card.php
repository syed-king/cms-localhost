<?php
include 'db.php';

// Security: Only Student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch Student Details
$sql = "SELECT users.*, departments.name as dept_name 
        FROM users 
        LEFT JOIN departments ON users.dept_id = departments.id 
        WHERE users.id = $student_id";
$res = $conn->query($sql);
$student = $res->fetch_assoc();

// --- PROFILE PIC LOGIC ---
// If database has a pic, use it. Otherwise, generate a letter avatar.
$profile_src = !empty($student['profile_pic']) ? $student['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($student['name'])."&background=random&size=128";
?>

<!DOCTYPE html>
<html>
<head>
    <title>My ID Card // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ID CARD STYLING */
        .id-card-container {
            width: 350px;
            height: 520px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            padding: 30px;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .hole-punch {
            width: 60px;
            height: 10px;
            background: rgba(0,0,0,0.5);
            border-radius: 10px;
            margin: 0 auto 20px auto;
        }

        .college-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #3b82f6;
            object-fit: cover; /* Ensures real photos don't stretch */
            margin: 20px auto;
            background: #fff;
            padding: 2px;
        }

        .student-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            margin: 10px 0 5px 0;
        }

        .role-badge {
            background: #10b981;
            color: black;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }

        .details-grid {
            text-align: left;
            margin-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .label { color: var(--text-muted); }
        .value { color: white; font-weight: 500; }

        .barcode {
            margin-top: 30px;
            height: 40px;
            width: 80%;
            background: white;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.8;
        }

        @media print {
            body * { visibility: hidden; }
            .id-card-container, .id-card-container * { visibility: visible; }
            .id-card-container {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                border: 2px solid black;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar no-print">
            <h2 style="margin-bottom: 40px;">SUPER TECH</h2>
            <nav>
                <a href="student_dashboard.php" class="nav-item">My Dashboard</a>
                <a href="student_schedule.php" class="nav-item">Class Schedule</a>
                <a href="student_results.php" class="nav-item">My Results</a>
                <a href="student_materials.php" class="nav-item">Study Materials</a>
                <a href="student_exams.php" class="nav-item">Upcoming Exams</a>
                <a href="student_leave.php" class="nav-item">leave request</a>
                <a href="student_id_card.php" class="nav-item active"">ID card</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <div class="no-print" style="margin-bottom: 30px;">
                <h1>Digital ID Card</h1>
                <p style="color: var(--text-muted);">View and download your official college identity card.</p>
            </div>

            <div class="id-card-container">
                <div class="hole-punch"></div>
                <div class="college-name">Super Tech College</div>
                <div style="font-size: 0.7rem; color: #aaa; letter-spacing: 2px;">EST. 2024</div>

                <img src="<?php echo $profile_src; ?>" class="profile-img">

                <div class="student-name"><?php echo $student['name']; ?></div>
                <div class="role-badge">STUDENT</div>

                <div class="details-grid">
                    <div class="detail-row">
                        <span class="label">ID Number</span>
                        <span class="value">ST-<?php echo str_pad($student['id'], 4, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Department</span>
                        <span class="value"><?php echo $student['dept_name']; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Valid Until</span>
                        <span class="value">Dec 2026</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Phone No.</span>
                        <span class="value"><?php echo isset($student['phone']) ? $student['phone'] : '-'; ?></span>
                    </div>
                </div>

                <div class="barcode" style="background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/QR_code_for_mobile_English_Wikipedia.svg/1200px-QR_code_for_mobile_English_Wikipedia.svg.png'); background-size: cover; width: 60px; height: 60px; margin-top: 20px;"></div>
            </div>

            <div style="text-align: center; margin-top: 40px;" class="no-print">
                <button onclick="window.print()" class="btn-tech" style="padding: 15px 40px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto; gap: 10px;">
                    <span>🖨️</span> Print / Save as PDF
                </button>
            </div>

        </div>
    </div>
</body>
</html>
