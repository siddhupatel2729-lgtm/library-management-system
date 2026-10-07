<?php
require_once "config/db.php";
$page_title="Books"; $page_heading="Books"; $page_subtitle="Add, search, update and manage library books."; $active="books";
$action=$_GET['action']??'list';
if(isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    $stmt=$conn->prepare("DELETE FROM books WHERE book_id=?"); $stmt->bind_param("i",$id);
    if($stmt->execute()){$_SESSION['success']="Book deleted successfully.";}else{$_SESSION['error']="Unable to delete book.";}
    header("Location: books.php"); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['book_id']??0); $title=trim($_POST['title']); $author=trim($_POST['author']); $isbn=trim($_POST['isbn']); $category=trim($_POST['category']);
    if($id){
        $stmt=$conn->prepare("UPDATE books SET title=?,author=?,isbn=?,category=? WHERE book_id=?"); $stmt->bind_param("ssssi",$title,$author,$isbn,$category,$id);
        if($stmt->execute()) $_SESSION['success']="Book updated successfully."; else $_SESSION['error']="Update failed. ISBN may already exist.";
    }else{
        $stmt=$conn->prepare("INSERT INTO books(title,author,isbn,category) VALUES(?,?,?,?)"); $stmt->bind_param("ssss",$title,$author,$isbn,$category);
        if($stmt->execute()) $_SESSION['success']="Book added successfully."; else $_SESSION['error']="Could not add book. ISBN may already exist.";
    }
    header("Location: books.php"); exit;
}
$edit=null;
if($action==='edit' && isset($_GET['id'])){ $stmt=$conn->prepare("SELECT * FROM books WHERE book_id=?");$stmt->bind_param("i",$_GET['id']);$stmt->execute();$edit=$stmt->get_result()->fetch_assoc(); }
$q=trim($_GET['q']??'');
$stmt=$conn->prepare("SELECT * FROM books WHERE title LIKE CONCAT('%',?,'%') OR author LIKE CONCAT('%',?,'%') OR isbn LIKE CONCAT('%',?,'%') OR category LIKE CONCAT('%',?,'%') ORDER BY book_id DESC");
$stmt->bind_param("ssss",$q,$q,$q,$q);$stmt->execute();$list=$stmt->get_result();
include "includes/header.php";
?>
<?php if($action==='add'||$action==='edit'): ?>
<div class="card"><div class="card-head"><h2><?= $edit?'Edit Book':'Add New Book' ?></h2><a class="btn btn-secondary" href="books.php">← Back</a></div>
<form method="post"><input type="hidden" name="book_id" value="<?= $edit['book_id']??0 ?>"><div class="form-grid">
<div class="form-group"><label>Title *</label><input required name="title" value="<?= htmlspecialchars($edit['title']??'') ?>"></div>
<div class="form-group"><label>Author *</label><input required name="author" value="<?= htmlspecialchars($edit['author']??'') ?>"></div>
<div class="form-group"><label>ISBN *</label><input required name="isbn" value="<?= htmlspecialchars($edit['isbn']??'') ?>"></div>
<div class="form-group"><label>Category *</label><input required name="category" value="<?= htmlspecialchars($edit['category']??'') ?>"></div>
</div><div class="form-actions"><button class="btn btn-primary">💾 Save Book</button><a class="btn btn-secondary" href="books.php">Cancel</a></div></form></div>
<?php else: ?>
<div class="card"><div class="card-head"><h2>All Books <span class="muted">(<?= $list->num_rows ?>)</span></h2><a class="btn btn-primary" href="books.php?action=add">+ Add Book</a></div>
<form class="search-row" method="get"><input name="q" placeholder="Search title, author, ISBN or category..." value="<?= htmlspecialchars($q) ?>"><button class="btn btn-secondary">🔍 Search</button><?php if($q): ?><a class="btn btn-secondary" href="books.php">Clear</a><?php endif; ?></form>
<div class="table-wrap"><table><tr><th>ID</th><th>Title</th><th>Author</th><th>ISBN</th><th>Category</th><th>Actions</th></tr>
<?php if($list->num_rows): while($b=$list->fetch_assoc()): ?><tr><td><?= $b['book_id'] ?></td><td><b><?= htmlspecialchars($b['title']) ?></b></td><td><?= htmlspecialchars($b['author']) ?></td><td><?= htmlspecialchars($b['isbn']) ?></td><td><?= htmlspecialchars($b['category']) ?></td><td><div class="actions"><a class="btn btn-secondary" href="books.php?action=edit&id=<?= $b['book_id'] ?>">Edit</a><a data-confirm="Delete this book?" class="btn btn-danger" href="books.php?delete=<?= $b['book_id'] ?>">Delete</a></div></td></tr><?php endwhile; else: ?><tr><td colspan="6" class="empty">No books found.</td></tr><?php endif; ?></table></div></div>
<?php endif; include "includes/footer.php"; ?>