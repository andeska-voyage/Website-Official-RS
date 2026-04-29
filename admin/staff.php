<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Karyawan";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; $name = $_POST['name']; $pos = $_POST['position']; $cat = $_POST['category']; $img = $_FILES['image']['name'] ?? '';
    if($id){
        $q = "UPDATE staff SET name=:n, position=:p, category=:c"; $pars = [':n'=>$name, ':p'=>$pos, ':c'=>$cat, ':id'=>$id];
        if($img) { move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img); $q.=", image=:i"; $pars[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($pars as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='team-1.jpg'; move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img);
        $db->query("INSERT INTO staff (name, position, category, image) VALUES (:n, :p, :c, :i)");
        $db->bind(':n',$name); $db->bind(':p',$pos); $db->bind(':c',$cat); $db->bind(':i',$img);
    } $db->execute(); header('Location: staff');
}
if(isset($_GET['delete'])){ 
    $db->query("DELETE FROM staff WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: staff'); 
}
 $db->query("SELECT * FROM staff ORDER BY category, name ASC"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-people-fill me-2"></i>Kelola Karyawan</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" id="formStaff">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="row g-3">
                <div class="col-md-2"><label>Nama</label><input type="text" name="name" id="editName" class="form-control" required></div>
                <div class="col-md-2"><label>Jabatan</label><input type="text" name="position" id="editPos" class="form-control" placeholder="Cth: Perawat" required></div>
                <div class="col-md-2">
                    <label>Kategori</label>
                    <select name="category" id="editCat" class="form-select">
                        <option value="Direksi">Direksi</option>
                        <option value="Medis">Medis</option>
                        <option value="Administrasi">Administrasi</option>
                    </select>
                </div>
                <div class="col-md-2"><label>Foto</label><input type="file" name="image" class="form-control"></div>
                <div class="col-md-2 mt-md-4"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Simpan</button></div>
            </div>
        </form>

        <hr>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $r): ?>
                    <tr>
                        <td width="80">
                            <img src="../img/<?php echo $r['image']; ?>" class="rounded-circle shadow-sm" style="width:50px; height:50px; object-fit:cover;">
                        </td>
                        <td><strong><?php echo $r['name']; ?></strong></td>
                        <td><?php echo $r['position']; ?></td>
                        <td><span class="badge bg-secondary"><?php echo $r['category']; ?></span></td>
                        <td>
                            <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></button>
                            <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function editData(data) {
        $('#editId').val(data.id);
        $('#editName').val(data.name);
        $('#editPos').val(data.position);
        $('#editCat').val(data.category);
    }
</script>
<?php require_once 'footer_admin.php'; ?>