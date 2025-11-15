<?php
session_start();
include "config.php";

$errorAdmin = "";
$errorStudent = "";
$activeTab = "admin"; // Default tab

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
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f7fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        padding: 20px;
    }
    .login-card {
        background: white;
        width: 400px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 32px;
        box-sizing: border-box;
    }
    h2 {
        text-align: center;
        margin-bottom: 10px;
    }
    .tabs {
        display: flex;
        justify-content: space-around;
        margin-bottom: 20px;
    }
    .tabs button {
        flex: 1;
        padding: 10px;
        cursor: pointer;
        border: none;
        border-bottom: 2px solid transparent;
        background: transparent;
        font-weight: bold;
        transition: 0.3s;
    }
    .tabs button.active {
        border-color: #1e40af;
        color: #1e40af;
    }
    form {
        display: none;
        flex-direction: column;
    }
    form.active {
        display: flex;
    }
    input {
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 1rem;
    }
    button.submit-btn {
        background: #1e40af;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }
    button.submit-btn:hover {
        background: #153eac;
    }
    .error {
        color: #dc2626;
        text-align: center;
        margin-bottom: 15px;
        font-weight: 600;
    }
</style>
</head>
<body>

<div class="login-card">
    <div class="tabs">
        <button id="adminTab">Admin Login</button>
        <button id="studentTab">Student Login</button>
    </div>

    <!-- Admin Form -->
    <form id="adminForm" method="POST">
        <?php if($errorAdmin != "") echo "<div class='error'>$errorAdmin</div>"; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="admin_login" class="submit-btn">Login</button>
    </form>

    <!-- Student Form -->
    <form id="studentForm" method="POST">
        <?php if($errorStudent != "") echo "<div class='error'>$errorStudent</div>"; ?>
        <input type="text" name="student_id" placeholder="Student ID" required>
        <input type="password" name="student_password" placeholder="Password" required>
        <button type="submit" name="student_login" class="submit-btn">Login</button>
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

    // Set active tab based on PHP variable
    switchTab("<?php echo $activeTab; ?>");
</script>

</body>
</html>
