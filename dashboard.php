<?php
session_start();
include "config.php";

// Redirect if student not logged in
if (!isset($_SESSION["student"])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION["student"]["student_id"];

// Fetch latest student info
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id=?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Tuition calculations
$total = round(floatval($student["tuition_total"]),2);
$paid = round(floatval($student["tuition_paid"]),2);
$balance = max($total - $paid, 0);
$progress = $total>0 ? ($paid/$total)*100 : 0;
$progress = min($progress,100);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Portal</title>
    <style>
        body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#032558ff; padding:30px;}
        .header {display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;}
        .header h1 {color:#fff; font-size:1.8rem;}
        .logout-btn {padding:8px 16px; background:#dc3545; color:white; text-decoration:none; border-radius:6px; font-weight:bold;}
        .grid {display:flex; gap:30px; flex-wrap:wrap;}
        .card {flex:1; min-width:300px; background:white; padding:25px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
        .card h3 {margin-bottom:10px; color:#1e40af;}
        .card p.subtitle {margin-bottom:20px; color:#6b7280; font-size:0.9rem;}
        .info-row {display:flex; justify-content:space-between; margin-bottom:12px;}
        .info-row span.label {font-weight:bold; color:#374151;}
        .info-row span.value {color:#111827;}
        .progress-container {background:#e5e7eb; border-radius:10px; overflow:hidden; height:18px; margin-top:10px;}
        .progress-bar {height:100%; width:<?php echo $progress; ?>%; text-align:center; color:white; font-size:0.8rem; line-height:18px; font-weight:bold; border-radius:10px; transition:width 0.5s ease; background-color:<?php echo ($progress<50)?"#dc2626":($progress<100?"#f59e0b":"#16a34a"); ?>;}
        .balance-status {display:inline-block; margin-left:8px; padding:2px 8px; font-size:0.75rem; border-radius:12px; color:white; background-color:<?php echo ($balance==0)?"#16a34a":"#f59e0b"; ?>;}
        @media(max-width:700px){.grid{flex-direction:column;}}
        /* Modal Styles */
        #payModal {display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);}
        #payModal .modal-content {background:#fff; margin:10% auto; padding:20px; border-radius:12px; width:90%; max-width:500px; position:relative;}
        #payModal .close {position:absolute; top:10px; right:15px; font-size:28px; font-weight:bold; cursor:pointer; color:#aaa;}
        #payModal .close:hover {color:#000;}
        #payModal form input, #payModal form select {width:100%; padding:8px; margin-bottom:8px; border-radius:6px; border:1px solid #ccc;}
        #payModal form small {display:block; color:#6b7280; font-size:0.75rem; margin-top:-6px; margin-bottom:6px;}
        #payModal form label {font-weight:bold; font-size:0.85rem; display:block; margin-bottom:4px; color:#374151;}
    </style>
</head>
<body>

<div class="header">
    <h1>Student Portal</h1>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="grid">
    <!-- Student Profile -->
    <div class="card">
        <h3>Student Profile</h3>
        <p class="subtitle">Your personal information</p>
        <div class="info-row"><span class="label">Full Name:</span> <span class="value"><?php echo htmlspecialchars($student["full_name"]); ?></span></div>
        <div class="info-row"><span class="label">Student ID:</span> <span class="value"><?php echo htmlspecialchars($student["student_id"]); ?></span></div>
        <div class="info-row"><span class="label">Email:</span> <span class="value"><?php echo htmlspecialchars($student["email"]); ?></span></div>
        <div class="info-row"><span class="label">Program:</span> <span class="value"><?php echo htmlspecialchars($student["course"]); ?></span></div>
        <div class="info-row"><span class="label">Year:</span> <span class="value"><?php echo htmlspecialchars($student["year_level"]); ?></span></div>
        <div class="info-row"><span class="label">Semester:</span> <span class="value"><?php echo htmlspecialchars($student["semester"]); ?></span></div>
    </div>

    <!-- Tuition Summary -->
    <div class="card" id="tuitionCard">
        <h3>Tuition Summary</h3>
        <p class="subtitle">Current semester fees</p>
        <div class="info-row"><span class="label">Total Tuition:</span> <span class="value" id="totalTuition">₱<?php echo number_format($total,2); ?></span></div>
        <div class="info-row"><span class="label">Amount Paid:</span> <span class="value" style="color:#16a34a;" id="amountPaid">₱<?php echo number_format($paid,2); ?></span></div>
        <div class="info-row">
            <span class="label">Balance Due:</span> 
            <span class="value" style="color:#dc2626;" id="balanceDue">₱<?php echo number_format($balance,2); ?></span>
            <?php if($balance>0): ?>
                <button id="payBtn" onclick="openPayModal()" style="margin-left:10px; padding:4px 10px; font-size:0.8rem; background:#2563eb; color:white; border:none; border-radius:4px; cursor:pointer;">Pay</button>
            <?php else: ?>
                <span class="balance-status">Paid in Full</span>
            <?php endif; ?>
        </div>
        <div class="info-row"><span class="label">Payment Progress</span></div>
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"><?php echo round($progress); ?>%</div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePayModal()">&times;</span>
        <h3>Make a Payment</h3>
        <p>Pay your tuition balance securely</p>
        <form id="payForm">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">

            <label>Payment Amount</label>
            <input type="number" name="amount" id="amountInput" placeholder="₱0.00" min="0.01" step="0.01" required>
            <small>Enter the amount you want to pay (cannot exceed remaining balance)</small>

            <label>Payment Method</label>
            <select name="payment_method" id="paymentMethod" onchange="updatePaymentForm()" required>
                <option value="">-- Select Method --</option>
                <option value="card">Card</option>
                <option value="gcash">GCash</option>
                <option value="paymaya">PayMaya</option>
            </select>

            <div id="cardFields" style="display:none;">
                <label>Card Number</label>
                <input type="text" name="card_number" placeholder="1234 5678 9012 3456">
                <div style="display:flex; gap:10px;">
                    <input type="text" name="expiry" placeholder="MM/YY">
                    <input type="text" name="cvc" placeholder="CVC">
                </div>
                <label>Cardholder Name</label>
                <input type="text" name="card_name" placeholder="John Doe">
            </div>

            <div id="ewalletFields" style="display:none;">
                <label>Mobile Number</label>
                <input type="text" name="mobile_number" placeholder="09XXXXXXXXX">
                <label>Name</label>
                <input type="text" name="ewallet_name" placeholder="Full Name">
            </div>

            <button type="submit" style="width:100%; background:#1d4ed8; color:white; padding:10px; font-weight:bold; border:none; border-radius:6px; cursor:pointer;">
                Pay Now
            </button>
        </form>
        <p id="paymentMsg" style="margin-top:10px;"></p>
    </div>
</div>

<!-- Notification -->
<div id="paymentNotification" style="position:fixed; top:20px; right:20px; background:#16a34a; color:white; padding:12px 20px; border-radius:8px; display:none; box-shadow:0 4px 12px rgba(0,0,0,0.2); z-index:1000;">
    Payment Successful!
</div>

<script>
function openPayModal(){
    fetch('pay_tuition_fetch.php')
    .then(res => res.json())
    .then(data => {
        if(data.success){
            const amountInput = document.getElementById('amountInput');
            amountInput.max = data.balance;
            amountInput.placeholder = "₱0.00 (Max ₱" + parseFloat(data.balance).toFixed(2) + ")";
            document.getElementById('payModal').style.display='block';
        } else {
            alert(data.message);
        }
    });
}

function closePayModal(){ document.getElementById('payModal').style.display='none'; }

window.onclick = function(event){
    if(event.target==document.getElementById('payModal')) closePayModal();
}

function updatePaymentForm(){
    const method = document.getElementById('paymentMethod').value;
    const cardFields = document.getElementById('cardFields');
    const ewalletFields = document.getElementById('ewalletFields');
    if(method==='card'){
        cardFields.style.display='block';
        ewalletFields.style.display='none';
        cardFields.querySelectorAll('input').forEach(i=>i.required=true);
        ewalletFields.querySelectorAll('input').forEach(i=>i.required=false);
    } else if(method==='gcash'||method==='paymaya'){
        cardFields.style.display='none';
        ewalletFields.style.display='block';
        cardFields.querySelectorAll('input').forEach(i=>i.required=false);
        ewalletFields.querySelectorAll('input').forEach(i=>i.required=true);
    } else{
        cardFields.style.display='none';
        ewalletFields.style.display='none';
        cardFields.querySelectorAll('input').forEach(i=>i.required=false);
        ewalletFields.querySelectorAll('input').forEach(i=>i.required=false);
    }
}

// AJAX submit
document.getElementById('payForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('pay_tuition_process.php',{
        method:'POST',
        body: formData
    }).then(res=>res.json()).then(data=>{
        const msg = document.getElementById('paymentMsg');
        const notification = document.getElementById('paymentNotification');
        const payBtn = document.getElementById('payBtn');

        if(data.success){
            document.getElementById('amountPaid').textContent = '₱'+parseFloat(data.new_paid).toFixed(2);
            document.getElementById('balanceDue').textContent = '₱'+parseFloat(data.new_balance).toFixed(2);
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = data.progress+'%';
            progressBar.textContent = Math.round(data.progress)+'%';

            notification.textContent = data.message;
            notification.style.display = 'block';
            setTimeout(()=>{ notification.style.display='none'; }, 2500);

            if(parseFloat(data.new_balance) <= 0 && payBtn){
                payBtn.remove();
                const balanceSpan = document.createElement('span');
                balanceSpan.className = 'balance-status';
                balanceSpan.textContent = 'Paid in Full';
                document.getElementById('balanceDue').parentNode.appendChild(balanceSpan);
            }

            setTimeout(()=>{ closePayModal(); msg.textContent=''; },1500);
        } else{
            msg.style.color='red';
            msg.textContent = data.message;
        }
    }).catch(err=>console.error(err));
});
</script>
</body>
</html>
