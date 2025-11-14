<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST["student_id"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM students WHERE student_id='$student_id' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Store student info in session
        $_SESSION["student"] = $result->fetch_assoc();
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Student ID or Password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Portal Login</title>
    <style>
        body {
            background: #e3e3e3;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            width: 350px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Student Login</h2>

    <?php if ($error != "") { echo "<p class='error'>$error</p>"; } ?>

    <form method="POST">
        <input type="text" name="student_id" placeholder="Student ID" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
