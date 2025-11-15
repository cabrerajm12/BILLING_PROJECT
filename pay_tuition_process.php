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

// Get latest paid/total
$stmt = $conn->prepare("SELECT tuition_total, tuition_paid FROM students WHERE student_id=?");
$stmt->bind_param("s",$student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$total = round(floatval($student['tuition_total']),2);
$paid = round(floatval($student['tuition_paid']),2);
$balance = max($total-$paid,0);

// Prevent overpayment
if($amount>$balance){
    echo json_encode(['success'=>false,'message'=>"Payment cannot exceed remaining balance of ₱".number_format($balance,2)]);
    exit;
}

// Update payment
$new_paid = round($paid+$amount,2);
$update = $conn->prepare("UPDATE students SET tuition_paid=? WHERE student_id=?");
$update->bind_param("ds",$new_paid,$student_id);
$update->execute();

// Calculate new balance/progress
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

if ($payment_success) {
    // Deduct from tuition_total and add to tuition_paid
    $sql = "UPDATE students 
            SET tuition_paid = tuition_paid + ?, tuition_total = tuition_total - ?
            WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dds", $amount, $amount, $student_id);
    if ($stmt->execute()) {

        // Make sure tuition_total doesn't go negative
        $sql_fix = "UPDATE students SET tuition_total = 0 WHERE student_id = ? AND tuition_total < 0";
        $stmt_fix = $conn->prepare($sql_fix);
        $stmt_fix->bind_param("s", $student_id);
        $stmt_fix->execute();

        // Log payment
        $sql_log = "INSERT INTO payments (student_id, amount, method, details, payment_date)
                    VALUES (?, ?, ?, ?, NOW())";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->bind_param("sdss", $student_id, $amount, $method, $payment_details);
        $stmt_log->execute();

        // Check if fully paid
        $sql_balance = "SELECT tuition_total FROM students WHERE student_id = ?";
        $stmt_balance = $conn->prepare($sql_balance);
        $stmt_balance->bind_param("s", $student_id);
        $stmt_balance->execute();
        $result_balance = $stmt_balance->get_result()->fetch_assoc();
        $balance = $result_balance['tuition_total'];

        if ($balance <= 0) {
            // Mark as Paid
            $sql_mark = "UPDATE students SET status = 'Paid' WHERE student_id = ?";
            $stmt_mark = $conn->prepare($sql_mark);
            $stmt_mark->bind_param("s", $student_id);
            $stmt_mark->execute();
        }

        // Return response
        echo json_encode([
            "success" => true,
            "message" => "Payment of ₱".number_format($amount,2)." successful!",
            "new_paid" => $paid + $amount,
            "new_balance" => max($balance, 0),
            "progress" => min((($paid + $amount)/$total)*100, 100)
        ]);
        exit();

    } else {
        echo json_encode(["success"=>false,"message"=>"Failed to update tuition."]);
        exit();
    }
}
// After updating tuition_paid and tuition_total
$new_balance = $total - $paid_after_payment; // make sure it's never negative
if($new_balance <= 0){
    $update_status_sql = "UPDATE students SET status='Paid', tuition_total=0 WHERE student_id=?";
    $stmt = $conn->prepare($update_status_sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
}
?>
