<?php
session_start();
include "config.php";
header('Content-Type: application/json');

$student_id = $_SESSION['student']['student_id'] ?? '';

if (!$student_id) {
    echo json_encode(['success'=>false,'message'=>'Student not logged in']);
    exit;
}

$stmt = $conn->prepare("SELECT tuition_total, tuition_paid FROM students WHERE student_id=?");
$stmt->bind_param("s",$student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$total = round(floatval($student['tuition_total']),2);
$paid = round(floatval($student['tuition_paid']),2);
$balance = max($total - $paid, 0);

echo json_encode([
    'success' => true,
    'balance' => $balance,
    'total' => $total,
    'paid' => $paid
]);
exit;
