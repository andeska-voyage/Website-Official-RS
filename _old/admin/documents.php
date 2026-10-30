<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_POST) {
    $id = $_POST['id'] ?? ''; $title = $_POST['title']; $cat = $_POST['category']; $img = $_FILES['doc']['name'] ?? '';
    if($id){
        $q="UPDATE documents SET title=:t, category=:c"; $p=[':t'=>$title, ':c'=>$cat, ':id'=>$id];
        if($img){ move_uploaded_file($_FILES['doc']['tmp_name'], '../uploads/'.$img); $q.=", file_path=:i"; $p[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        move_uploaded_file($_FILES['doc']['tmp_name'], '../uploads/'.$img);
        $db->query("INSERT INTO documents (title, category, file_path) VALUES (:t, :c, :i)");
        $db->bind(':t',$title); $db->bind(':c',$cat); $db->bind(':i',$img);
    } $db->execute(); header('Location: documents');
}
if(isset($_GET['delete'])){ $db->query("DELETE FROM documents WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: documents'); }
 $db->query("SELECT * FROM documents ORDER BY created_at DESC"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Download Dokumen</h2>
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
            <input type="hidden" name="id" id="editId">
            <div class="col-md-3"><input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul" required></div>
            <div class="col-md-3">
                <select name="category" id="editCat" class="form-select">
                    <option>Akreditasi</option><option>SOP</option><option>SK</option><option>Lainnya</option>
                </select>
            </div>
            <div class="col-md-3"><input type="file" name="doc" class="form-control"></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Upload</button></div>
        </form>
        <table class="table table-striped">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo $r['category']; ?></td>
                    <td><a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php require_once 'footer_admin.php'; ?>