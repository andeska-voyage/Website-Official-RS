<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Capaian RS";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; 
 $db = new Database();

// Proses Simpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; 
    $title = $_POST['title']; 
    $icon = $_POST['icon'];
    $value = $_POST['value']; // Diubah dari count

    if($id){
        $db->query("UPDATE achievements SET title=:t, icon=:i, value=:v WHERE id=:id");
        $db->bind(':t',$title); $db->bind(':i',$icon); $db->bind(':v',$value); $db->bind(':id',$id);
    } else {
        $db->query("INSERT INTO achievements (title, icon, value) VALUES (:t, :i, :v)");
        $db->bind(':t',$title); $db->bind(':i',$icon); $db->bind(':v',$value);
    }
    $db->execute(); 
    header('Location: achievements');
}

// Proses Hapus
if(isset($_GET['delete'])){ 
    $db->query("DELETE FROM achievements WHERE id=:id"); $db->bind(':id', $_GET['delete']); $db->execute(); header('Location: achievements'); 
}

 $db->query("SELECT * FROM achievements ORDER BY id ASC"); 
 $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-trophy me-2"></i>Kelola Capaian</h5></div>
    <div class="card-body p-4">
        <form method="POST" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="col-md-3">
                <label>Judul</label>
                <input type="text" name="title" id="editTitle" class="form-control" placeholder="Pasien Puas" required>
            </div>
            <div class="col-md-3">
                <label>Nilai / Teks</label>
                <!-- DIUBAH: Nama jadi 'value' dan type='text' -->
                <input type="text" name="value" id="editValue" class="form-control" placeholder="10.000+ atau Terakreditasi" required>
            </div>
            <div class="col-md-3">
                <label>Icon (Font Awesome)</label>
                <input type="text" name="icon" id="editIcon" class="form-control" placeholder="fas fa-user" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped">
    <thead><tr><th>Judul</th><th>Nilai</th><th>Icon</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?php echo $r['title']; ?></td>
            <!-- Menampilkan langsung, bukan number_format -->
            <td><strong><?php echo $r['value']; ?></strong></td>
            <td><i class="<?php echo $r['icon']; ?>"></i></td>
            <td>
                <button onclick="editData('<?php echo $r['id']; ?>', '<?php echo htmlspecialchars($r['title']); ?>', '<?php echo htmlspecialchars($r['value']); ?>', '<?php echo $r['icon']; ?>')" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
function editData(id, t, v, i){
    document.getElementById('editId').value=id;
    document.getElementById('editTitle').value=t;
    document.getElementById('editValue').value=v; // Target Value
    document.getElementById('editIcon').value=i;
}
</script>
<?php require_once 'footer_admin.php'; ?>