<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_POST) {
    $id = $_POST['id'] ?? ''; $title = $_POST['title']; $content = $_POST['content']; $cat = $_POST['category_id']; $img = $_FILES['image']['name'] ?? '';
    if($id){
        $q="UPDATE posts SET title=:t, content=:c, category_id=:cat"; $p=[':t'=>$title, ':c'=>$content, ':cat'=>$cat, ':id'=>$id];
        if($img){ move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img); $q.=", image=:i"; $p[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='blog-1.jpg'; move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img);
        $db->query("INSERT INTO posts (title, content, category_id, image) VALUES (:t, :c, :cat, :i)");
        $db->bind(':t',$title); $db->bind(':c',$content); $db->bind(':cat',$cat); $db->bind(':i',$img);
    } $db->execute(); header('Location: posts');
}
if(isset($_GET['delete'])){ $db->query("DELETE FROM posts WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: posts'); }
 $db->query("SELECT p.*, c.name as cname FROM posts p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC"); $rows = $db->resultSet();
 $db->query("SELECT * FROM categories"); $cats = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Berita / Artikel</h2>
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="id" id="editId">
            <div class="row g-3">
                <div class="col-md-6"><input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul" required></div>
                <div class="col-md-3"><select name="category_id" id="editCat" class="form-select" required><option value="">Pilih Kategori</option><?php foreach($cats as $c) echo '<option value="'.$c['id'].'">'.$c['name'].'</option>'; ?></select></div>
                <div class="col-md-3"><input type="file" name="image" class="form-control"></div>
                <div class="col-12"><textarea name="content" id="editor" class="form-control"></textarea></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo $r['cname']; ?></td>
                    <td><a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script> tinymce.init({ selector: '#editor', plugins: 'link image code lists', menubar: true, height: 300 }); </script>
<?php require_once 'footer_admin.php'; ?>