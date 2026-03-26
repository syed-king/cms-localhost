<?php
include 'db.php';

// Security: Only Student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Results // Super Tech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .grade-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .grade-pass { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .grade-fail { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 style="margin-bottom: 40px;">SUPER TECH</h2>
            <nav>
                <a href="student_dashboard.php" class="nav-item">My Dashboard</a>
                <a href="student_schedule.php" class="nav-item">Class Schedule</a>
                <a href="student_results.php" class="nav-item active">My Results</a>
                <a href="student_materials.php" class="nav-item">Study Materials</a>
                <a href="student_exams.php" class="nav-item">Upcoming Exams</a>
                <a href="student_leave.php" class="nav-item">leave request</a>
                <a href="student_id_card.php" class="nav-item">ID card</a>
            </nav>
            <div style="margin-top: auto;">
            	<a href="settings.php" class="nav-item">Settings</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </div>
        </div>
        
        <div class="main-area">
            <h1>Academic Performance</h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Your Exam Results and Grade Reports.</p>

            <?php
            // 1. Get Distinct Exams for this student
            $exam_sql = "SELECT DISTINCT exam_name FROM exam_results WHERE student_id=$student_id ORDER BY id DESC";
            $exam_res = $conn->query($exam_sql);

            if ($exam_res->num_rows > 0) {
                while($exam_row = $exam_res->fetch_assoc()) {
                    $exam_name = $exam_row['exam_name'];
                    
                    echo "<div class='tech-card' style='margin-bottom: 30px;'>
                            <div style='display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;'>
                                <h3 style='margin:0; color:#3b82f6;'>$exam_name</h3>
                                <span style='font-size:0.9rem; color:var(--text-muted);'>Report Card</span>
                            </div>
                            
                            <table style='width: 100%; border-collapse: collapse;'>
                                <thead style='border-bottom: 1px solid rgba(255,255,255,0.1);'>
                                    <tr>
                                        <th style='padding: 10px; text-align: left; color: var(--text-muted);'>Subject</th>
                                        <th style='padding: 10px; text-align: center; color: var(--text-muted);'>Max Marks</th>
                                        <th style='padding: 10px; text-align: center; color: var(--text-muted);'>Obtained</th>
                                        <th style='padding: 10px; text-align: right; color: var(--text-muted);'>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>";

                    // 2. Get Subjects for this specific exam
                    $sub_sql = "SELECT * FROM exam_results WHERE student_id=$student_id AND exam_name='$exam_name'";
                    $sub_res = $conn->query($sub_sql);
                    
                    $total_marks = 0;
                    $total_max = 0;

                    while($sub = $sub_res->fetch_assoc()) {
                        $marks = $sub['marks_obtained'];
                        $max = $sub['max_marks'];
                        
                        $total_marks += $marks;
                        $total_max += $max;

                        // Calculate Grade Logic
                        if ($marks < 50) { $grade = "FAIL"; $class="grade-fail"; }
                        elseif ($marks >= 90) { $grade = "O (Outstanding)"; $class="grade-pass"; }
                        elseif ($marks >= 80) { $grade = "A+ (Excellent)"; $class="grade-pass"; }
                        elseif ($marks >= 70) { $grade = "A (Very Good)"; $class="grade-pass"; }
                        elseif ($marks >= 60) { $grade = "B (Good)"; $class="grade-pass"; }
                        else { $grade = "C (Pass)"; $class="grade-pass"; }

                        echo "<tr style='border-bottom: 1px solid rgba(255,255,255,0.05);'>
                                <td style='padding: 15px;'>{$sub['subject_name']}</td>
                                <td style='padding: 15px; text-align: center; opacity:0.6;'>$max</td>
                                <td style='padding: 15px; text-align: center; font-weight:bold;'>$marks</td>
                                <td style='padding: 15px; text-align: right;'>
                                    <span class='grade-badge $class'>$grade</span>
                                </td>
                              </tr>";
                    }

                    // Calculate Percentage
                    $percentage = ($total_max > 0) ? round(($total_marks / $total_max) * 100, 2) : 0;
                    
                    echo "</tbody>
                          <tfoot>
                            <tr style='background: rgba(255,255,255,0.05);'>
                                <td style='padding: 15px; font-weight:bold;'>TOTAL</td>
                                <td style='padding: 15px; text-align: center;'>$total_max</td>
                                <td style='padding: 15px; text-align: center; font-weight:bold; color:#f59e0b;'>$total_marks</td>
                                <td style='padding: 15px; text-align: right; font-weight:bold;'>$percentage%</td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>";
                }
            } else {
                echo "<div class='tech-card' style='text-align:center; padding:50px;'>
                        <div style='font-size:3rem; margin-bottom:10px;'>📄</div>
                        <h3 style='color:var(--text-muted);'>No results declared yet.</h3>
                      </div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
