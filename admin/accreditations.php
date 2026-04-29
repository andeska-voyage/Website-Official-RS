<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Akreditasi";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; $title = $_POST['title']; $img = $_FILES['image']['name'] ?? '';
    if($id){
        $q="UPDATE accreditations SET title=:t"; $p=[':t'=>$title, ':id'=>$id];
        if($img){ move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img); $q.=", image=:i"; $p[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='default.png'; move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img);
        $db->query("INSERT INTO accreditations (title, image) VALUES (:t, :i)"); $db->bind(':t',$title); $db->bind(':i',$img);
    } $db->execute(); header('Location: accreditations');
}
if(isset($_GET['delete'])){ 
    $db->query("SELECT image FROM accreditations WHERE id=:id"); $db->bind(':id',$_GET['delete']); $row=$db->single();
    if($row && file_exists('../img/'.$row['image'])) unlink('../img/'.$row['image']);
    $db->query("DELETE FROM accreditations WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: accreditations'); 
}
 $db->query("SELECT * FROM accreditations"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header"><h5 class="text-primary">Tambah Akreditasi</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            <div class="col-md-4"><input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul" required></div>
            <div class="col-md-4"><input type="file" name="image" class="form-control"></div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
        </form>
    </div>
</div>
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
<script>function editData(d){$('#editId').val(d.id);$('#editTitle').val(d.title);}</script>
<?php require_once 'footer_admin.php'; ?>