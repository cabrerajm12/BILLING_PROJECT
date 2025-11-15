<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

include "config.php";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $full_name = $_POST['full_name'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $semester = $_POST['semester'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE students SET full_name=?, course=?, year_level=?, email=?, contact_number=?, semester=? WHERE student_id=?");
    $stmt->bind_param("ssssssi", $full_name, $course, $year_level, $email, $contact_number, $semester, $student_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Student updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating student: " . $stmt->error;
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
