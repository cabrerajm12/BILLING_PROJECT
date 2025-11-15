<?php
session_start();
include "config.php";
header('Content-Type: application/json');

$student_id = $_POST['student_id'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);

if(!$student_id || $amount<=0){
    echo json_encode(['success'=>false,'message'=>'Invalid payment data']);
    exit;
}

$stmt = $conn->prepare("SELECT tuition_total, tuition_paid FROM students WHERE student_id=?");
$stmt->bind_param("s",$student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$total = round(floatval($student['tuition_total']),2);
$paid = round(floatval($student['tuition_paid']),2);
$balance = max($total-$paid,0);

if($amount>$balance){
    echo json_encode(['success'=>false,'message'=>"Payment cannot exceed remaining balance of ₱".number_format($balance,2)]);
    exit;
}

// Update payment
$new_paid = round($paid+$amount,2);
$update = $conn->prepare("UPDATE students SET tuition_paid=? WHERE student_id=?");
$update->bind_param("ds",$new_paid,$student_id);
$update->execute();

$new_balance = max($total-$new_paid,0);
$progress = $total>0 ? ($new_paid/$total)*100 : 0;
$progress = min($progress,100);

echo json_encode([
    'success'=>true,
    'message'=>"Payment of ₱".number_format($amount,2)." successful!",
    'new_paid'=>$new_paid,
    'new_balance'=>$new_balance,
    'progress'=>$progress
]);
exit;
