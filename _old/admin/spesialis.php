<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
 $pageTitle = "Spesialis";
require_once '../config/database.php'; $db = new Database();

if ($_POST) {
    $id = $_POST['kd_sps']; $name = $_POST['nm_sps'];
    $db->query("REPLACE INTO spesialis (kd_sps, nm_sps) VALUES (:id, :nm)");
    $db->bind(':id', $id); $db->bind(':nm', $name);
    $db->execute(); header('Location: spesialis');
}
if(isset($_GET['delete'])){ 
    $db->query("DELETE FROM spesialis WHERE kd_sps = :id"); 
    $db->bind(':id', $_GET['delete']); $db->execute(); header('Location: spesialis'); 
}
 $db->query("SELECT * FROM spesialis ORDER BY nm_sps"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<h2>Data Spesialisasi</h2>
<div class="card p-4">
    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="old_id" id="editOld">
        <div class="col-md-3"><input type="text" name="kd_sps" id="editId" class="form-control" placeholder="Kode (S0001)" required></div>
        <div class="col-md-5"><input type="text" name="nm_sps" id="editNm" class="form-control" placeholder="Nama Spesialis" required></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
    </form>
    <table class="table table-striped">
        <thead><tr><th>Kode</th><th>Nama Spesialis</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($rows as $r): ?>
            <tr>
                <td><?php echo $r['kd_sps']; ?></td>
                <td><?php echo $r['nm_sps']; ?></td>
                <td>
                    <button onclick="edit('<?php echo $r['kd_sps']; ?>', '<?php echo $r['nm_sps']; ?>')" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                    <a href="?delete=<?php echo $r['kd_sps']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    function edit(id, nm){ document.getElementById('editId').value=id; document.getElementById('editNm').value=nm; }
</script>
<?php require_once 'footer_admin.php'; ?>