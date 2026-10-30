<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_POST) {
    $id = $_POST['id'] ?? ''; $title = $_POST['title']; $img = $_FILES['image']['name'] ?? '';
    if($id){
        $q="UPDATE accreditations SET title=:t"; $p=[':t'=>$title, ':id'=>$id];
        if($img){ move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img); $q.=", image=:i"; $p[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='default.png'; move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img);
        $db->query("INSERT INTO accreditations (title, image) VALUES (:t, :i)");
        $db->bind(':t',$title); $db->bind(':i',$img);
    } $db->execute(); header('Location: accreditations');
}
if(isset($_GET['delete'])){ $db->query("DELETE FROM accreditations WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: accreditations'); }
 $db->query("SELECT * FROM accreditations"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Akreditasi & Sertifikat</h2>
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
            <input type="hidden" name="id" id="editId">
            <div class="col-md-4"><input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul" required></div>
            <div class="col-md-4"><input type="file" name="image" class="form-control"></div>
            <div class="col-md-4"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
        </form>
        <div class="row">
            <?php foreach($rows as $r): ?>
            <div class="col-md-3 text-center mb-3">
                <div class="card p-3 h-100">
                    <img src="../img/<?php echo $r['image']; ?>" class="img-fluid mb-2" style="height:150px; object-fit:cover;">
                    <h6><?php echo $r['title']; ?></h6>
                    <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php require_once 'footer_admin.php'; ?>