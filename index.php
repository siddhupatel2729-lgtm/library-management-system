<?php
require_once "config/db.php";
$page_title="Dashboard"; $page_heading="Dashboard"; $page_subtitle="Overview of your college library."; $active="dashboard";
$books = $conn->query("SELECT COUNT(*) c FROM books")->fetch_assoc()['c'];
$students = $conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c'];
$issued = $conn->query("SELECT COUNT(*) c FROM issue_return WHERE status='Issued'")->fetch_assoc()['c'];
$recent = $conn->query("SELECT ir.*, b.title, s.name FROM issue_return ir JOIN books b ON b.book_id=ir.book_id JOIN students s ON s.student_id=ir.student_id ORDER BY ir.issue_id DESC LIMIT 6");
include "includes/header.php";
?>
<div class="welcome"><h2>Welcome to LibraCore 👋</h2><p>Manage books, students and issue/return records from one place.</p></div>
<div class="stats">
<div class="stat"><div><div class="label">Total Books</div><div class="num"><?= $books ?></div></div><div class="stat-icon">📚</div></div>
<div class="stat"><div><div class="label">Total Students</div><div class="num"><?= $students ?></div></div><div class="stat-icon">🎓</div></div>
<div class="stat"><div><div class="label">Currently Issued</div><div class="num"><?= $issued ?></div></div><div class="stat-icon">🔄</div></div>
</div>
<div class="two-col">
<div class="card"><div class="card-head"><h2>Recent Issue / Return Records</h2></div>
<div class="table-wrap"><table><tr><th>Book</th><th>Student</th><th>Status</th></tr>
<?php if($recent->num_rows): while($r=$recent->fetch_assoc()): ?>
<tr><td><?= htmlspecialchars($r['title']) ?></td><td><?= htmlspecialchars($r['name']) ?></td><td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td></tr>
<?php endwhile; else: ?><tr><td colspan="3" class="empty">No records yet.</td></tr><?php endif; ?></table></div></div>
<div class="card"><div class="card-head"><h2>Quick Actions</h2></div><div class="quick-links">
<a class="quick" href="books.php?action=add"><b>➕ Add Book</b><span>Add a new library book</span></a>
<a class="quick" href="students.php?action=add"><b>🎓 Register Student</b><span>Create student record</span></a>
<a class="quick" href="issue_return.php?action=issue"><b>🔄 Issue Book</b><span>Issue a book to student</span></a>
</div></div></div>
<?php include "includes/footer.php"; ?>