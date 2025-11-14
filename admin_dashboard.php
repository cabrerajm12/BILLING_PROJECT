<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

include "config.php"; // Assuming this includes your database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <style>
        /* Reset */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, system-ui;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            color: #333;
        }
        .container {
            background: white;
            width: 100%;
            max-width: 900px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            padding: 32px 40px;
            box-sizing: border-box;
        }

        h2 {
            margin-top: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #1c1f36;
            text-align: center;
            margin-bottom: 36px;
        }

        .actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        a.button {
            background: #2563eb; /* Blue */
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            user-select: none;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        a.button:hover,
        a.button:focus-visible {
            background: #1d4ed8;
            box-shadow: 0 6px 15px rgba(29, 78, 216, 0.4);
            outline: none;
        }
        a.logout {
            background: #dc2626; /* Red */
        }
        a.logout:hover,
        a.logout:focus-visible {
            background: #b91c1c;
            box-shadow: 0 6px 15px rgba(185, 28, 28, 0.4);
        }

        h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1c1f36;
        }
        /* Font Awesome icon sizing */
        h3 i {
            font-size: 1.3rem;
            color: #2563eb;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background: white;
        }
        thead tr {
            background: #2563eb;
            color: white;
            font-weight: 700;
        }
        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e3e6ee;
        }
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        tbody tr:hover {
            background: #e0e7ff;
            cursor: default;
        }

        .no-students {
            text-align: center;
            color: #6b7280; /* Gray-500 */
            font-style: italic;
            margin-top: 24px;
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 24px 20px;
            }
            a.button {
                width: 100%;
                justify-content: center;
            }
            th, td {
                padding: 12px 14px;
                font-size: 14px;
            }
            h2 {
                font-size: 1.75rem;
            }
            h3 {
                font-size: 1.1rem;
            }
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
</head>
<body>

<div class="container" role="main" aria-label="Admin Dashboard">
    <h2> Admin Dashboard</h2>

    <div class="actions">
        <a href="admin_add_student.php" class="button" aria-label="Add New Student">
            <i class="fas fa-user-plus" aria-hidden="true"></i> Add New Student
        </a>
        <a href="admin_logout.php" class="button logout" aria-label="Logout Admin">
            <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
        </a>
    </div>

    <h3><i class="fas fa-users" aria-hidden="true"></i> Student List</h3>
    <div class="table-container" tabindex="0">
        <?php
        $sql = "SELECT student_id, fullname, course, year_level, email, contact FROM students ORDER BY student_id DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo '<table aria-describedby="studentListDescription">';
            echo '<thead><tr>
                    <th scope="col">Student ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Course</th>
                    <th scope="col">Year Level</th>
                    <th scope="col">Email</th>
                    <th scope="col">Contact</th>
                  </tr></thead><tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row["student_id"]) . '</td>
                        <td>' . htmlspecialchars($row["fullname"]) . '</td>
                        <td>' . htmlspecialchars($row["course"]) . '</td>
                        <td>' . htmlspecialchars($row["year_level"]) . '</td>
                        <td>' . htmlspecialchars($row["email"]) . '</td>
                        <td>' . htmlspecialchars($row["contact"]) . '</td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p id="studentListDescription" class="no-students">No students found. Please add some students.</p>';
        }
        ?>
    </div>
</div>

</body>
</html>
