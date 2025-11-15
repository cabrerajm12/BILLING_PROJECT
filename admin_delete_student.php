<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id=?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Student deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting student: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
