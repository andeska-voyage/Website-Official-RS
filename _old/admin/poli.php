<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
 $pageTitle = "Poliklinik";
require_once '../config/database.php'; $db = new Database();

if ($_POST) {
    $id = $_POST['kd_poli']; $name = $_POST['nm_poli'];
    if($_POST['old_id'] != $id) { 
        // Jika Kode Poli berubah, update semua dokter & jadwal yang pakai kode lama
        $db->query("UPDATE dokter SET kd_poli = :new WHERE kd_poli = :old");
        $db->bind(':new', $id); $db->bind(':old', $_POST['old_id']); $db->execute();
        
        $db->query("UPDATE jadwal SET kd_poli = :new WHERE kd_poli = :old");
        $db->bind(':new', $id); $db->bind(':old', $_POST['old_id']); $db->execute();
    }

    $db->query("REPLACE INTO poliklinik (kd_poli, nm_poli) VALUES (:id, :nm)");
    $db->bind(':id', $id); $db->bind(':nm', $name);
    $db->execute();
    header('Location: poli');
}
if(isset($_GET['delete'])){ 
    $db->query("DELETE FROM poliklinik WHERE kd_poli = :id"); 
    $db->bind(':id', $_GET['delete']); 
    $db->execute(); 
    header('Location: poli'); 
}
 $db->query("SELECT * FROM poliklinik"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Data Poliklinik</h2>
    <div class="card p-4">
        <form method="POST" class="row g-3 mb-4">
            <input type="hidden" name="old_id" id="editOld">
            <div class="col-md-3"><input type="text" name="kd_poli" id="editId" class="form-control" placeholder="Kode (U0001)" required></div>
            <div class="col-md-5"><input type="text" name="nm_poli" id="editNm" class="form-control" placeholder="Nama Poliklinik" required></div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
        </form>
        <table class="table table-striped">
            <thead><tr><th>Kode</th><th>Nama Poliklinik</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['kd_poli']; ?></td>
                    <td><?php echo $r['nm_poli']; ?></td>
                    <td>
                        <button onclick="edit('<?php echo $r['kd_poli']; ?>', '<?php echo $r['nm_poli']; ?>')" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                        <a href="?delete=<?php echo $r['kd_poli']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        function edit(id, nm){ document.getElementById('editOld').value=id; document.getElementById('editId').value=id; document.getElementById('editNm').value=nm; }
    </script>
<?php require_once 'footer_admin.php'; ?>