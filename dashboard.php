<?php
session_start();

if (!isset($_SESSION["student"])) {
    header("Location: login.php");
    exit();
}

$student = $_SESSION["student"];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            padding: 40px;
        }
        .container {
            width: 600px;
            background: white;
            padding: 25px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .info-box {
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f8f8;
            border-left: 4px solid #007bff;
        }
        .label {
            font-weight: bold;
        }
        .logout-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #dc3545;
            text-align: center;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo $student["name"]; ?>!</h2>

    <div class="info-box">
        <span class="label">Student ID:</span> <?php echo $student["student_id"]; ?>
    </div>

    <div class="info-box">
        <span class="label">Course:</span> <?php echo $student["course"]; ?><br>
        <span class="label">Year Level:</span> <?php echo $student["year_level"]; ?>
    </div>

    <div class="info-box">
        <span class="label">Email:</span> <?php echo $student["email"]; ?><br>
        <span class="label">Contact:</span> <?php echo $student["contact"]; ?>
    </div>

    <!-- Tuition Information -->
    <div class="info-box">
        <span class="label">Total Tuition Fee:</span> 
        <?php echo isset($student["tuition_total"]) ? $student["tuition_total"] : "₱0.00"; ?><br>

        <span class="label">Amount Paid:</span> 
        <?php echo isset($student["tuition_paid"]) ? $student["tuition_paid"] : "₱0.00"; ?><br>

        <span class="label">Remaining Balance:</span> 
        <?php 
            if (isset($student["tuition_total"]) && isset($student["tuition_paid"])) {
                echo "₱" . ($student["tuition_total"] - $student["tuition_paid"]);
            } else {
                echo "₱0.00";
            }
        ?>
    </div>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
