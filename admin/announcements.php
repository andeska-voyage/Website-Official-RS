<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_POST) {
    $id = $_POST['id'] ?? ''; $title = $_POST['title']; $content = $_POST['content']; $img = $_FILES['file']['name'] ?? '';
    if($id){
        $q="UPDATE announcements SET title=:t, content=:c"; $p=[':t'=>$title, ':c'=>$content, ':id'=>$id];
        if($img){ move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/'.$img); $q.=", file_path=:i"; $p[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/'.$img);
        $db->query("INSERT INTO announcements (title, content, file_path) VALUES (:t, :c, :i)");
        $db->bind(':t',$title); $db->bind(':c',$content); $db->bind(':i',$img);
    } $db->execute(); header('Location: announcements');
}
if(isset($_GET['delete'])){ $db->query("DELETE FROM announcements WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: announcements'); }
 $db->query("SELECT * FROM announcements ORDER BY created_at DESC"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Pengumuman</h2>
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="id" id="editId">
            <div class="mb-3"><input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul" required></div>
            <div class="mb-3"><textarea name="content" id="editor" class="form-control"></textarea></div>
            <div class="mb-3"><input type="file" name="file" class="form-control"></div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
        <table class="table table-bordered">
            <thead><tr><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                    <td><a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script> tinymce.init({ selector: '#editor', menubar: false, height: 200 }); </script>
<?php require_once 'footer_admin.php'; ?>