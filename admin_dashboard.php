<?php
session_start();

// Prevent access if not logged in
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

include "config.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<style>
/* --- Styles same as before --- */
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #032558ff; padding: 40px 20px; display: flex; justify-content: center; min-height: 100vh; }
.container { background: #fff; width: 100%; max-width: 900px; border-radius: 16px; padding: 32px 40px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
h2 { text-align: center; font-size: 2rem; margin-bottom: 36px; color: #1c1f36; }
.actions { display: flex; justify-content: center; gap: 20px; margin-bottom: 40px; }
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
.message-bubble { padding: 8px 14px; border-radius: 12px; margin-top: 10px; color: #fff; min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); opacity: 0; transform: translateY(-20px); transition: opacity 0.5s, transform 0.5s; font-family: 'Segoe UI', sans-serif; font-weight: 600; font-size: 13px; text-align: center; cursor: pointer; }
.message-success { background: #16a34a; }
.message-error { background: #dc2626; }
</style>
</head>
<body>
<div class="container">
<h2>Admin Dashboard</h2>

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
<?php
$sql = "SELECT student_id, full_name, course, year_level, email, contact_number, semester, tuition_total
        FROM students ORDER BY student_id DESC";
$result = $conn->query($sql);

if (!$result) {
    echo "<p class='no-students'>Database error: " . $conn->error . "</p>";
} elseif ($result->num_rows > 0) {
    echo "<table>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        $tuition_display = "₱" . number_format($row["tuition_total"], 2);
        echo "<tr>
                <td>" . htmlspecialchars($row["student_id"]) . "</td>
                <td>" . htmlspecialchars($row["full_name"]) . "</td>
                <td>" . htmlspecialchars($row["course"]) . "</td>
                <td>" . htmlspecialchars($row["year_level"]) . "</td>
                <td>" . htmlspecialchars($row["email"]) . "</td>
                <td>" . htmlspecialchars($row["contact_number"]) . "</td>
                <td>" . htmlspecialchars($row["semester"]) . "</td>
                <td>" . $tuition_display . "</td>
                <td>
                    <button class='action-btn edit-btn' 
                        onclick='openEditModal(" . json_encode($row) . ")'>
                        <i class='fas fa-edit'></i> Edit
                    </button>
                    <button class='action-btn delete-btn' 
                        onclick='openDeleteModal(" . $row["student_id"] . ")'>
                        <i class='fas fa-trash-alt'></i> Delete
                    </button>
                </td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='no-students'>No students found. Add students to begin.</p>";
}
?>
</div>
</div>

<!-- Edit Modal -->
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

<script>
function openEditModal(student) {
    document.getElementById('edit_student_id').value = student.student_id;
    document.getElementById('edit_full_name').value = student.full_name;
    document.getElementById('edit_course').value = student.course;
    document.getElementById('edit_year_level').value = student.year_level;
    document.getElementById('edit_email').value = student.email;
    document.getElementById('edit_contact_number').value = student.contact_number;
    document.getElementById('edit_semester').value = student.semester;
    // Format tuition with ₱ and commas
    document.getElementById('edit_tuition_total').value = "₱" + parseFloat(student.tuition_total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('editModal').style.display = 'block';
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    // Remove ₱ and commas before sending
    let tuitionInput = document.getElementById('edit_tuition_total');
    tuitionInput.value = tuitionInput.value.replace(/[₱,]/g, '');
});

function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

function openDeleteModal(student_id) {
    document.getElementById('delete_student_id').value = student_id;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; }

window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) closeEditModal();
    if (event.target == document.getElementById('deleteModal')) closeDeleteModal();
}
</script>
</body>
</html>
