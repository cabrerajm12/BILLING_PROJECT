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
    $tuition_total = $_POST['tuition_total'];

    // Validate semester
    if ($semester !== "1st Semester" && $semester !== "2nd Semester") {
        header("Location: admin_dashboard.php?msg=Invalid semester value ❌");
        exit();
    }

    // Ensure tuition is numeric
    $tuition_total = floatval(str_replace(',', '', $tuition_total));

    // Update student
    $stmt = $conn->prepare("UPDATE students SET full_name=?, course=?, year_level=?, email=?, contact_number=?, semester=?, tuition_total=? WHERE student_id=?");
    $stmt->bind_param("sssssdii", $full_name, $course, $year_level, $email, $contact_number, $semester, $tuition_total, $student_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Student updated successfully ✅");
    } else {
        header("Location: admin_dashboard.php?msg=Error updating student ❌");
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
