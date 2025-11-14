<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION["admin"] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, system-ui;
            background: #f5f7fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 32px 28px 36px;
            box-sizing: border-box;
        }

        /* Heading */
        .login-card h2 {
            margin: 0 0 6px 0;
            margin-bottom: 2rem;
            font-weight: 700;
            font-size: 1.6rem;
            color: #1c1f36;
            text-align: center;
        }
        .login-card p.subtitle {
            margin: 0 0 24px 0;
            color: #7a7c90;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2d2f42;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            color: #1c1f35;
            transition: border-color 0.3s ease;
            margin-bottom: 20px;
        }
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgb(59 130 246 / 0.3);
        }

        /* Button */
        button {
            padding: 14px;
            background-color: #1e40af;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        button:hover {
            background-color: #153eac;
        }

        /* Error message */
        .error {
            color: #dc2626; /* Red */
            font-weight: 600;
            margin-bottom: 16px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 400px) {
            .login-card {
                width: 100%;
                padding: 28px 24px 32px;
            }
        }
    </style>
</head>
<body>

<div class="login-card" role="main" aria-labelledby="loginTitle">
    <h2 id="loginTitle">Admin Portal</h2>

    <?php if ($error != ""): ?>
        <div class="error" role="alert" aria-live="assertive"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label for="username">Email</label>
        <input type="text" id="username" name="username" placeholder="" required autocomplete="username" />
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="" required autocomplete="current-password" />
        <button type="submit">Login</button>
    </form>

</div>

</body>
</html>
