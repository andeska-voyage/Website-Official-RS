<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'];
    $count = $_POST['count'];
    $icon = $_POST['icon'];
    if ($id) {
        $db->query("UPDATE achievements SET title=:t, count=:c, icon=:i WHERE id=:id");
        $db->bind(':id', $id);
    } else {
        $db->query("INSERT INTO achievements (title, count, icon) VALUES (:t, :c, :i)");
    }
    $db->bind(':t', $title); $db->bind(':c', $count); $db->bind(':i', $icon);
    $db->execute();
    header('Location: achievements');
}
if (isset($_GET['delete'])) {
    $db->query("DELETE FROM achievements WHERE id = :id"); $db->bind(':id', $_GET['delete']); $db->execute(); header('Location: achievements');
}
 $db->query("SELECT * FROM achievements");
 $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Pencapaian (Angka Statistik)</h2>
    <div class="card p-4">
        <form method="POST" class="row g-3 mb-4">
            <input type="hidden" name="id" id="editId">
            <div class="col-md-4"><input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul (cth: Pasien)" required></div>
            <div class="col-md-2"><input type="number" name="count" id="editCount" class="form-control" placeholder="Angka" required></div>
            <div class="col-md-3"><input type="text" name="icon" id="editIcon" class="form-control" placeholder="Icon (fas fa-star)" required></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
        </form>
        <table class="table table-striped">
            <thead><tr><th>Judul</th><th>Angka</th><th>Icon</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo number_format($r['count']); ?></td>
                    <td><i class="<?php echo $r['icon']; ?>"></i> <?php echo $r['icon']; ?></td>
                    <td>
                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php require_once 'footer_admin.php'; ?>