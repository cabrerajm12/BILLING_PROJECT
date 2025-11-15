<?php
session_start();

// Redirect to login if student is not logged in
if (!isset($_SESSION["student"])) {
    header("Location: login.php");
    exit();
}

$student = $_SESSION["student"];

// Tuition calculations
$total = isset($student["tuition_total"]) ? $student["tuition_total"] : 0;
$paid = isset($student["tuition_paid"]) ? $student["tuition_paid"] : 0;
$balance = max($total - $paid, 0);
$progress = $total > 0 ? ($paid / $total) * 100 : 0;
$progress = min($progress, 100);

// Payment status
$paymentStatus = ($balance == 0) ? "Paid in Full" : "Pending";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Portal</title>
    <style>
        /* Reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
              background: #032558ff;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #ffffffff;
            font-size: 1.8rem;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .grid {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .card {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin-bottom: 10px;
            color: #1e40af;
        }

        .card p.subtitle {
            margin-bottom: 20px;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .info-row span.label {
            font-weight: bold;
            color: #374151;
        }

        .info-row span.value {
            color: #111827;
        }

        /* Tuition progress bar */
        .progress-container {
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            height: 18px;
            margin-top: 10px;
        }

        .progress-bar {
            height: 100%;
            width: <?php echo $progress; ?>%;
            text-align: center;
            color: white;
            font-size: 0.8rem;
            line-height: 18px;
            font-weight: bold;
            border-radius: 10px;
            transition: width 0.5s ease;
            background-color: <?php 
                if($progress < 50) echo "#dc2626"; // red
                elseif($progress < 100) echo "#f59e0b"; // yellow
                else echo "#16a34a"; // green
            ?>;
        }

        .balance-status {
            display: inline-block;
            margin-left: 8px;
            padding: 2px 8px;
            font-size: 0.75rem;
            border-radius: 12px;
            color: white;
            background-color: <?php echo ($balance == 0) ? "#16a34a" : "#f59e0b"; ?>;
        }

        @media(max-width: 700px) {
            .grid {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Student Portal</h1>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="grid">
    <!-- Student Profile Card -->
    <div class="card">
        <h3>Student Profile</h3>
        <p class="subtitle">Your personal information</p>
        <div class="info-row"><span class="label">Full Name:</span> <span class="value"><?php echo isset($student["full_name"]) ? htmlspecialchars($student["full_name"]) : "N/A"; ?></span></div>
        <div class="info-row"><span class="label">Student ID:</span> <span class="value"><?php echo isset($student["student_id"]) ? htmlspecialchars($student["student_id"]) : "N/A"; ?></span></div>
        <div class="info-row"><span class="label">Email:</span> <span class="value"><?php echo isset($student["email"]) ? htmlspecialchars($student["email"]) : "N/A"; ?></span></div>
        <div class="info-row"><span class="label">Program:</span> <span class="value"><?php echo isset($student["course"]) ? htmlspecialchars($student["course"]) : "N/A"; ?></span></div>
        <div class="info-row"><span class="label">Year:</span> <span class="value"><?php echo isset($student["year_level"]) ? htmlspecialchars($student["year_level"]) : "N/A"; ?></span></div>
        <div class="info-row"><span class="label">Semester:</span> <span class="value"><?php echo isset($student["semester"]) ? htmlspecialchars($student["semester"]) : "N/A"; ?></span></div>
    </div>

    <!-- Tuition Summary Card -->
    <div class="card">
        <h3>Tuition Summary</h3>
        <p class="subtitle">Current semester fees</p>
        <div class="info-row"><span class="label">Total Tuition:</span> <span class="value">₱<?php echo number_format($total,2); ?></span></div>
        <div class="info-row"><span class="label">Amount Paid:</span> <span class="value" style="color:#16a34a;">₱<?php echo number_format($paid,2); ?></span></div>
        <div class="info-row">
            <span class="label">Balance Due:</span> 
            <span class="value" style="color:#dc2626;">₱<?php echo number_format($balance,2); ?></span>
            <?php if($balance == 0): ?>
                <span class="balance-status">Paid in Full</span>
            <?php endif; ?>
        </div>

        <div class="info-row"><span class="label">Payment Progress</span></div>
        <div class="progress-container">
            <div class="progress-bar"><?php echo round($progress); ?>%</div>
        </div>
    </div>
</div>

</body>
</html>
