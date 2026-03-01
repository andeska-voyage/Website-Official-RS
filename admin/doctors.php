<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? ''; $name = $_POST['name']; $spec = $_POST['specialization']; $img = $_FILES['image']['name'] ?? '';
    if ($id) {
        $q = "UPDATE doctors SET name=:n, specialization=:s"; $p = [':n'=>$name, ':s'=>$spec, ':id'=>$id];
        if($img) { move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img); $q.=", image=:i"; $p[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='team-1.jpg'; move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img);
        $db->query("INSERT INTO doctors (name, specialization, image) VALUES (:n, :s, :i)");
        $db->bind(':n',$name); $db->bind(':s',$spec); $db->bind(':i',$img);
    } $db->execute(); header('Location: doctors');
}
if(isset($_GET['delete'])){ $db->query("DELETE FROM doctors WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: doctors'); }
 $db->query("SELECT * FROM doctors"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Data Dokter</h2>
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
            <input type="hidden" name="id" id="editId">
            <div class="col-md-3"><input type="text" name="name" id="editName" class="form-control" placeholder="Nama" required></div>
            <div class="col-md-3"><input type="text" name="specialization" id="editSpec" class="form-control" placeholder="Spesialis" required></div>
            <div class="col-md-3"><input type="file" name="image" class="form-control"></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
        </form>
        <table class="table table-hover">
            <thead><tr><th>Nama</th><th>Spesialis</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['name']; ?></td>
                    <td><?php echo $r['specialization']; ?></td>
                    <td><a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php require_once 'footer_admin.php'; ?>