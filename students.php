<?php
require_once "config/db.php";
$page_title="Students";$page_heading="Students";$page_subtitle="Register and manage student contact details.";$active="students";
$action=$_GET['action']??'list';
if(isset($_GET['delete'])){ $id=(int)$_GET['delete'];$stmt=$conn->prepare("DELETE FROM students WHERE student_id=?");$stmt->bind_param("i",$id);if($stmt->execute())$_SESSION['success']="Student deleted.";else $_SESSION['error']="Unable to delete student.";header("Location: students.php");exit; }
if($_SERVER['REQUEST_METHOD']==='POST'){ $id=(int)($_POST['student_id']??0);$name=trim($_POST['name']);$email=trim($_POST['email']);$phone=trim($_POST['phone']);
if($id){$stmt=$conn->prepare("UPDATE students SET name=?,email=?,phone=? WHERE student_id=?");$stmt->bind_param("sssi",$name,$email,$phone,$id);$_SESSION['success']=$stmt->execute()?"Student updated successfully.":"Update failed.";}
else{$stmt=$conn->prepare("INSERT INTO students(name,email,phone) VALUES(?,?,?)");$stmt->bind_param("sss",$name,$email,$phone);$_SESSION['success']=$stmt->execute()?"Student registered successfully.":"Registration failed.";}header("Location: students.php");exit;}
$edit=null;if($action==='edit'&&isset($_GET['id'])){$stmt=$conn->prepare("SELECT * FROM students WHERE student_id=?");$stmt->bind_param("i",$_GET['id']);$stmt->execute();$edit=$stmt->get_result()->fetch_assoc();}
$q=trim($_GET['q']??'');$stmt=$conn->prepare("SELECT * FROM students WHERE name LIKE CONCAT('%',?,'%') OR email LIKE CONCAT('%',?,'%') OR phone LIKE CONCAT('%',?,'%') ORDER BY student_id DESC");$stmt->bind_param("sss",$q,$q,$q);$stmt->execute();$list=$stmt->get_result();
include "includes/header.php"; ?>
<?php if($action==='add'||$action==='edit'): ?><div class="card"><div class="card-head"><h2><?= $edit?'Edit Student':'Register New Student' ?></h2><a class="btn btn-secondary" href="students.php">← Back</a></div>
<form method="post"><input type="hidden" name="student_id" value="<?= $edit['student_id']??0 ?>"><div class="form-grid">
<div class="form-group"><label>Student Name *</label><input required name="name" value="<?= htmlspecialchars($edit['name']??'') ?>"></div>
<div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($edit['email']??'') ?>"></div>
<div class="form-group"><label>Phone</label><input name="phone" value="<?= htmlspecialchars($edit['phone']??'') ?>"></div>
</div><div class="form-actions"><button class="btn btn-primary">💾 Save Student</button><a class="btn btn-secondary" href="students.php">Cancel</a></div></form></div>
<?php else: ?><div class="card"><div class="card-head"><h2>Registered Students <span class="muted">(<?= $list->num_rows ?>)</span></h2><a class="btn btn-primary" href="students.php?action=add">+ Register Student</a></div>
<form class="search-row" method="get"><input name="q" placeholder="Search student name, email or phone..." value="<?= htmlspecialchars($q) ?>"><button class="btn btn-secondary">🔍 Search</button></form>
<div class="table-wrap"><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
<?php if($list->num_rows):while($s=$list->fetch_assoc()): ?><tr><td><?= $s['student_id'] ?></td><td><b><?= htmlspecialchars($s['name']) ?></b></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['phone']) ?></td><td><div class="actions"><a class="btn btn-secondary" href="students.php?action=edit&id=<?= $s['student_id'] ?>">Edit</a><a data-confirm="Delete this student? Related issue records will also be deleted." class="btn btn-danger" href="students.php?delete=<?= $s['student_id'] ?>">Delete</a></div></td></tr><?php endwhile;else: ?><tr><td colspan="5" class="empty">No students found.</td></tr><?php endif; ?></table></div></div><?php endif; include "includes/footer.php"; ?>