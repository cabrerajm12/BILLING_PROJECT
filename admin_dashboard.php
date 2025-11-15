<?php
session_start();

// Prevent access if not logged in
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

include "config.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            width: 100%;
            max-width: 900px;
            border-radius: 16px;
            padding: 32px 40px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 36px;
            color: #1c1f36;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }
        a.button {
            background: #2563eb;
            color: #fff;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
        }
        a.button:hover { background: #1d4ed8; }
        a.logout { background: #dc2626; }
        a.logout:hover { background: #b91c1c; }

        .table-container { overflow-x: auto; margin-top: 10px; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }
        thead { background: #2563eb; color: #fff; }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:hover { background: #e0e7ff; }

        .no-students {
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
<div class="container">
    
    <h2>Admin Dashboard</h2>

    <div class="actions">
        <a href="admin_add_student.php" class="button">
            <i class="fas fa-user-plus"></i> Add New Student
        </a>
        <a href="admin_logout.php" class="button logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <h3><i class="fas fa-users"></i> Student List</h3>

    <div class="table-container">
        <?php
        
        $sql = "SELECT student_id, full_name, course, year_level, email, contact_number 
                FROM students 
                ORDER BY student_id DESC";

        $result = $conn->query($sql);

        if (!$result) {
            echo "<p class='no-students'>Database error: " . $conn->error . "</p>";
        } elseif ($result->num_rows > 0) {

            echo "<table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["student_id"]) . "</td>
                        <td>" . htmlspecialchars($row["full_name"]) . "</td>
                        <td>" . htmlspecialchars($row["course"]) . "</td>
                        <td>" . htmlspecialchars($row["year_level"]) . "</td>
                        <td>" . htmlspecialchars($row["email"]) . "</td>
                        <td>" . htmlspecialchars($row["contact_number"]) . "</td>
                      </tr>";
            }

            echo "</tbody></table>";

        } else {
            echo "<p class='no-students'>No students found. Add students to begin.</p>";
        }
        ?>
    </div>

</div>
</body>
</html>
