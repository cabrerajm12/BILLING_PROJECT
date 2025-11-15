<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
include "config.php";

// Handle messages from update/delete
$msg = "";
if(isset($_GET['msg'])) {
    $msg = htmlspecialchars($_GET['msg']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<style>
/* ----------- CSS Same as Before ----------- */
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #032558ff; padding: 40px 20px; display: flex; justify-content: center; min-height: 100vh; }
.container { background: #fff; width: 100%; max-width: 900px; border-radius: 16px; padding: 32px 40px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
h2 { text-align: center; font-size: 2rem; margin-bottom: 36px; color: #1c1f36; }
.actions { display: flex; justify-content: center; gap: 20px; margin-bottom: 20px; }
a.button { background: #2563eb; color: #fff; padding: 14px 28px; border-radius: 12px; font-weight: 700; text-decoration: none; transition: 0.3s; }
a.button:hover { background: #1d4ed8; }
a.logout { background: #dc2626; }
a.logout:hover { background: #b91c1c; }

.table-container { overflow-x: auto; margin-top: 10px; }
table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.05); font-size: 13px; }
thead { background: #2563eb; color: #fff; }
th, td { padding: 7px; border-bottom: 1px solid #eee; text-align: left; }
tbody tr:nth-child(even) { background: #f9fafb; }
tbody tr:hover { background: #e0e7ff; }
.action-btn { margin-right: 5px; padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer; color: #fff; }
.edit-btn { background: #16a34a; }
.delete-btn { background: #dc2626; }
.no-students { text-align: center; color: #6b7280; font-style: italic; }
.status-paid { color: #16a34a; font-weight: bold; }
.status-unpaid { color: #dc2626; font-weight: bold; }

/* Modal Styles */
.modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
.modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 12px; width: 90%; max-width: 500px; position: relative; }
.close { position: absolute; top: 10px; right: 15px; color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
.close:hover { color: #000; }
.modal input, .modal select { width: 100%; padding: 8px; margin-bottom: 12px; border-radius: 6px; border: 1px solid #ccc; }
.modal button { padding: 10px 18px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
.save-btn { background: #2563eb; color: #fff; }
.cancel-btn { background: #6b7280; color: #fff; margin-left: 10px; }

/* Floating Messages */
#message-container { position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; flex-direction: column; align-items: flex-end; }
.message-bubble { padding: 8px 14px; border-radius: 12px; margin-top: 10px; color: #fff; min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); opacity: 1; font-family: 'Segoe UI', sans-serif; font-weight: 600; font-size: 13px; text-align: center; cursor: pointer; }
.message-success { background: #16a34a; }
.message-error { background: #dc2626; }
</style>
</head>
<body>
<div class="container">
<h2>Admin Dashboard</h2>

<?php if($msg != ""): ?>
    <div id="message-container">
        <div class="message-bubble <?php echo strpos($msg,'success')!==false?'message-success':'message-error'; ?>">
            <?php echo $msg; ?>
        </div>
    </div>
<?php endif; ?>

<div class="actions">
    <a href="admin_add_student.php" class="button">
        <i class="fas fa-user-plus"></i> Add New Student
    </a>
    <a href="admin_logout.php" class="button logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<h3><i class="fas fa-users"></i> Student List</h3>
<div class="table-container">
<table>
<thead>
<tr>
    <th>Student ID</th>
    <th>Name</th>
    <th>Course</th>
    <th>Year</th>
    <th>Email</th>
    <th>Contact No.</th>
    <th>Semester</th>
    <th>Tuition</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody id="studentTableBody">
<?php
$sql = "SELECT student_id, full_name, course, year_level, email, contact_number, semester, tuition_total, tuition_paid
        FROM students ORDER BY student_id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tuition_display = "₱" . number_format($row["tuition_total"], 2);
        $balance = floatval($row["tuition_total"]) - floatval($row["tuition_paid"]);
        $status_class = ($balance <= 0) ? "status-paid" : "status-unpaid";
        $status_text = ($balance <= 0) ? "Paid in Full" : "Unpaid";

        echo "<tr data-student-id='".$row["student_id"]."'>
                <td>" . htmlspecialchars($row["student_id"]) . "</td>
                <td>" . htmlspecialchars($row["full_name"]) . "</td>
                <td>" . htmlspecialchars($row["course"]) . "</td>
                <td>" . htmlspecialchars($row["year_level"]) . "</td>
                <td>" . htmlspecialchars($row["email"]) . "</td>
                <td>" . htmlspecialchars($row["contact_number"]) . "</td>
                <td>" . htmlspecialchars($row["semester"]) . "</td>
                <td>" . $tuition_display . "</td>
                <td class='status $status_class'>" . $status_text . "</td>
                <td>
                    <button class='action-btn edit-btn' 
                        onclick='openEditModal(" . json_encode($row) . ")'>
                        <i class='fas fa-edit'></i> Edit
                    </button>
                    <button class='action-btn delete-btn' 
                        onclick='openDeleteModal(" . $row["student_id"] . ", \"" . htmlspecialchars(addslashes($row["full_name"])) . "\")'>
                        <i class='fas fa-trash-alt'></i> Delete
                    </button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='10' class='no-students'>No students found. Add students to begin.</td></tr>";
}
?>
</tbody>
</table>
</div>
</div>

<!-- Modals for Edit/Delete (same as before) -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Student</h3>
        <form id="editForm" method="POST" action="admin_update_student.php">
            <input type="hidden" name="student_id" id="edit_student_id" />
            <input type="text" name="full_name" id="edit_full_name" placeholder="Full Name" required />
            <input type="text" name="course" id="edit_course" placeholder="Course" required />
            <input type="text" name="year_level" id="edit_year_level" placeholder="Year Level" required />
            <input type="email" name="email" id="edit_email" placeholder="Email" required />
            <input type="text" name="contact_number" id="edit_contact_number" placeholder="Contact Number" required />
            <select name="semester" id="edit_semester" required>
                <option value="">Select Semester</option>
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
            </select>
            <input type="text" name="tuition_total" id="edit_tuition_total" placeholder="Tuition" required />
            <button type="submit" class="save-btn">Save Changes</button>
            <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h3>Confirm Deletion</h3>
        <p id="deleteMessage">Are you sure you want to delete this student?</p>
        <div style="text-align: right;">
            <button id="confirmDeleteBtn" class="save-btn">Yes, Delete</button>
            <button class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
// Edit/Delete functions same as before
function openEditModal(student) { /*...*/ }
function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
let deleteStudentId = null;
function openDeleteModal(id,name){ deleteStudentId=id; document.getElementById('deleteMessage').innerText=`Are you sure you want to delete "${name}"?`; document.getElementById('deleteModal').style.display='block'; }
document.getElementById('confirmDeleteBtn').addEventListener('click', function(){ if(deleteStudentId!==null) window.location.href="admin_delete_student.php?id="+deleteStudentId; });
function closeDeleteModal(){ document.getElementById('deleteModal').style.display='none'; }

// ------------------ Live Status Update ------------------
function fetchStatusUpdates(){
    fetch('admin_student_status_fetch.php')
    .then(res=>res.json())
    .then(data=>{
        data.forEach(student=>{
            const row = document.querySelector(`tr[data-student-id='${student.student_id}']`);
            if(row){
                const statusCell = row.querySelector('.status');
                if(student.balance <= 0){
                    statusCell.textContent = "Paid in Full";
                    statusCell.classList.add('status-paid');
                    statusCell.classList.remove('status-unpaid');
                } else {
                    statusCell.textContent = "Unpaid";
                    statusCell.classList.add('status-unpaid');
                    statusCell.classList.remove('status-paid');
                }
            }
        });
    }).catch(err=>console.error(err));
}

// Fetch status updates every 5 seconds
setInterval(fetchStatusUpdates,5000);
</script>
</body>
</html>
