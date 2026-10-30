<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
 $pageTitle = "Layanan";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; $title = $_POST['title']; $description = $_POST['description']; $icon = $_POST['icon'];
    if($id){
        $db->query("UPDATE services SET title=:t, description=:d, icon=:i WHERE id=:id");
        $db->bind(':id', $id);
    } else {
        $db->query("INSERT INTO services (title, description, icon) VALUES (:t, :d, :i)");
    }
    $db->bind(':t', $title); $db->bind(':d', $description); $db->bind(':i', $icon);
    $db->execute(); header('Location: services');
}
if(isset($_GET['delete'])){ $db->query("DELETE FROM services WHERE id=:id"); $db->bind(':id', $_GET['delete']); $db->execute(); header('Location: services'); }
 $db->query("SELECT * FROM services ORDER BY id ASC"); $rows = $db->resultSet();
 $icon_list = ['fas fa-hospital'=>'Hospital', 'fas fa-user-md'=>'Dokter', 'fas fa-procedures'=>'Rawat Inap', 'fas fa-ambulance'=>'IGD', 'fas fa-flask'=>'Lab', 'fas fa-heartbeat'=>'Jantung', 'fas fa-baby'=>'Bayi', 'fas fa-syringe'=>'Imunisasi', 'fas fa-stethoscope'=>'Pemeriksaan', 'fas fa-pills'=>'Farmasi'];
require_once 'nav_admin.php';
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-gear-fill me-2"></i>Kelola Layanan</h5></div>
    <div class="card-body p-4">
        <form method="POST" class="row g-3 mb-4">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            <div class="col-md-4"><label>Nama Layanan</label><input type="text" name="title" id="editTitle" class="form-control" required></div>
            <div class="col-md-4"><label>Icon</label>
                <select name="icon" id="editIcon" class="form-select" required>
                    <?php foreach($icon_list as $class => $name): ?><option value="<?php echo $class; ?>"><?php echo $name; ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4"><label>Deskripsi</label><input type="text" name="description" id="editDesc" class="form-control" required></div>
            <div class="col-12"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button></div>
        </form>
        <hr>
        <table class="table table-hover align-middle">
            <thead class="bg-light"><tr><th>Icon</th><th>Nama Layanan</th><th>Deskripsi</th><th width="100">Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><i class="<?php echo $r['icon']; ?> fa-2x text-primary"></i></td>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo $r['description']; ?></td>
                    <td>
                        <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>function editData(d){$('#editId').val(d.id);$('#editTitle').val(d.title);$('#editDesc').val(d.description);$('#editIcon').val(d.icon);}</script>
<?php require_once 'footer_admin.php'; ?>