<?php
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST["student_id"];
    $name = $_POST["name"];
    $course = $_POST["course"];
    $year_level = $_POST["year_level"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $semester = $_POST["semester"];

    // Remove non-numeric characters for storing in DB
    $tuition_total = floatval(str_replace(['₱', ','], '', $_POST["tuition_total"]));
    $tuition_paid = 0.00; // start at 0

    $password = $_POST["password"];

    // Prepared statement to insert student safely
    $stmt = $conn->prepare("INSERT INTO students (student_id, password, full_name, course, year_level, email, contact_number, semester, tuition_total, tuition_paid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssdd", $student_id, $password, $name, $course, $year_level, $email, $contact, $semester, $tuition_total, $tuition_paid);

    if($stmt->execute()){
        $message = "Student successfully added!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student (Admin)</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
/* Your original CSS */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: white;
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.container {
    width: 90%;
    max-width: 800px;
    padding: 30px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    margin: 15px;
    position: relative;
    overflow: hidden;
}
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 28px;
    font-weight: 500;
    font-size: 24px;
}
.message {
    text-align: center;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 10px;
    font-weight: 400;
}
.message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.form-group {
    position: relative;
}
.form-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    font-size: 18px;
}
input, select {
    width: 100%;
    padding: 15px 15px 15px 45px;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}
input:focus, select:focus {
    border-color: #ccccccff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
    padding-right: 45px;
}
.full-width {
    grid-column: 1 / -1;
}
button {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #01156bff 0%, #1f026dff 100%);
    border: none;
    color: white;
    font-size: 16px;
    font-weight: 500;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 7px;
}
button:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3); }
.back-btn {
    background: #6b7280;
    margin-top: 10px;
    grid-column: 1 / -1;
}
.back-btn:hover { background: #4b5563; }

@media (max-width: 768px) {
    form { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-user-plus"></i> Add Student</h2>

    <?php if ($message != "") { 
        $class = strpos($message, "successfully") !== false ? "success" : "error";
        echo "<p class='message $class'>$message</p>"; 
    } ?>

    <form method="POST">
        <div class="form-group">
            <i class="fas fa-id-card"></i>
            <input type="text" name="student_id" placeholder="Student ID" required>
        </div>
        <div class="form-group">
            <i class="fas fa-user"></i>
            <input type="text" name="name" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <i class="fas fa-graduation-cap"></i>
            <select name="course" required>
                <option value="">Select Course</option>
                <option value="BSIT">BSIT</option>
                <option value="BSBA">BSBA</option>
                <option value="BSED">BSED</option>
            </select>
        </div>
        <div class="form-group">
            <i class="fas fa-calendar-alt"></i>
            <select name="year_level" required>
                <option value="">Select Year Level</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
            </select>
        </div>
        <div class="form-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <i class="fas fa-phone"></i>
            <input type="text" name="contact" placeholder="Contact Number" required>
        </div>
        <div class="form-group">
            <i class="fas fa-calendar-alt"></i>
            <select name="semester" required>
                <option value="">Select Semester</option>
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
            </select>
        </div>
        <div class="form-group">
            <i class="fas fa-money-bill-wave"></i>
            <input type="text" name="tuition_total" id="tuition_total" placeholder="Tuition" required>
        </div>
        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password for Portal Login" required>
        </div>
        <button type="submit" class="full-width"><i class="fas fa-plus"></i> Add Student</button>
        <button type="button" class="back-btn" onclick="window.location.href='admin_dashboard.php'">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </button>
    </form>
</div>

<script>
// Fixed currency formatting with backspace & decimals
const tuitionInput = document.getElementById('tuition_total');

tuitionInput.addEventListener('input', function() {
    let value = tuitionInput.value.replace(/[₱,]/g, ''); // remove symbols & commas

    if(value === "") {
        tuitionInput.value = "";
        return;
    }

    // Split integer and decimal
    let parts = value.split('.');
    let intPart = parts[0];
    let decPart = parts[1] ? parts[1].slice(0,2) : '';

    // Format integer part
    intPart = Number(intPart).toLocaleString('en-PH');

    tuitionInput.value = decPart ? `₱${intPart}.${decPart}` : `₱${intPart}`;
});
</script>

</body>
</html>
