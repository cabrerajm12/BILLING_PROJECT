<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

include "config.php";

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Student deleted successfully ✅");
    } else {
        header("Location: admin_dashboard.php?msg=Error deleting student ❌");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: admin_dashboard.php");
}
?>
