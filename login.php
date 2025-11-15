<?php
session_start();
include "config.php";

$errorAdmin = "";
$errorStudent = "";
$activeTab = "student"; // Default tab

// Admin login
if (isset($_POST['admin_login'])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION["admin"] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $errorAdmin = "Invalid username or password!";
        $activeTab = "admin";
    }
}

// Student login
if (isset($_POST['student_login'])) {
    $student_id = $_POST["student_id"];
    $password = $_POST["student_password"];

    $sql = "SELECT * FROM students WHERE student_id='$student_id' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION["student"] = $result->fetch_assoc();
        header("Location: dashboard.php");
        exit();
    } else {
        $errorStudent = "Invalid Student ID or Password!";
        $activeTab = "student";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #032558ff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-card {
        background: #fff;
        width: 360px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 30px 24px;
        text-align: center;
    }

    .login-card h2 {
        margin: 0 0 8px;
        font-weight: 700;
        color: #111827;
    }

    .login-card p {
        margin: 0 0 20px;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .tabs {
        display: flex;
        margin-bottom: 20px;
        border-radius: 8px;
        overflow: hidden;
        background: #f3f4f6;
    }

    .tabs button {
        flex: 1;
        padding: 10px 0;
        border: none;
        background: transparent;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s;
        color: #6b7280;
    }

    .tabs button.active {
        background: #1e40af;
        color: #fff;
    }

    form {
        display: none;
        flex-direction: column;
        gap: 12px;
        text-align: left;
    }

    form.active {
        display: flex;
    }

    input {
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        width: 90%;
        font-size: 0.95rem;
    }

    button.submit-btn {
        background: #1e40af;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
        font-size: 0.95rem;
    }

    button.submit-btn:hover {
        background: #1d4ed8;
    }

    .error {
        color: #dc2626;
        font-size: 0.85rem;
        text-align: center;
        font-weight: 600;
    }

</style>
</head>
<body>

<div class="login-card">
    <h2>Student Portal</h2>
    <p>Access your student information</p>

    <div class="tabs">
        <button id="studentTab">Login</button>
        <button id="adminTab">Admin</button>
    </div>

    <!-- Student Form -->
    <form id="studentForm" method="POST">
        <?php if($errorStudent != "") echo "<div class='error'>$errorStudent</div>"; ?>
        <input type="text" name="student_id" placeholder="Student ID" required>
        <input type="password" name="student_password" placeholder="Password" required>
        <button type="submit" name="student_login" class="submit-btn">Login</button>
    </form>

    <!-- Admin Form -->
    <form id="adminForm" method="POST">
        <?php if($errorAdmin != "") echo "<div class='error'>$errorAdmin</div>"; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="admin_login" class="submit-btn">Login</button>
    </form>
</div>

<script>
    const adminTab = document.getElementById('adminTab');
    const studentTab = document.getElementById('studentTab');
    const adminForm = document.getElementById('adminForm');
    const studentForm = document.getElementById('studentForm');

    function switchTab(tab) {
        if(tab === 'admin'){
            adminTab.classList.add('active');
            studentTab.classList.remove('active');
            adminForm.classList.add('active');
            studentForm.classList.remove('active');
        } else {
            studentTab.classList.add('active');
            adminTab.classList.remove('active');
            studentForm.classList.add('active');
            adminForm.classList.remove('active');
        }
    }

    adminTab.addEventListener('click', () => switchTab('admin'));
    studentTab.addEventListener('click', () => switchTab('student'));

    // Set default active tab
    switchTab("<?php echo $activeTab; ?>");
</script>

</body>
</html>
