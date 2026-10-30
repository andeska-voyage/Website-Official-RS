<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_POST) {
    $id = $_POST['id'] ?? ''; $name = $_POST['name']; $slug = strtolower(str_replace(' ', '-', $name));
    if($id) { $db->query("UPDATE categories SET name=:n, slug=:s WHERE id=:id"); $db->bind(':id',$id); }
    else { $db->query("INSERT INTO categories (name, slug) VALUES (:n, :s)"); }
    $db->bind(':n',$name); $db->bind(':s',$slug); $db->execute(); header('Location: categories');
}
if(isset($_GET['delete'])){ $db->query("DELETE FROM categories WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: categories'); }
 $db->query("SELECT * FROM categories"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

    <h2>Kategori Berita</h2>
    <div class="card p-4">
        <form method="POST" class="row g-3 mb-4">
            <input type="hidden" name="id" id="editId">
            <div class="col-md-4"><input type="text" name="name" id="editName" class="form-control" placeholder="Nama Kategori" required></div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
        </form>
        <table class="table table-striped">
            <thead><tr><th>Nama</th><th>Slug</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['name']; ?></td>
                    <td><code><?php echo $r['slug']; ?></code></td>
                    <td><a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php require_once 'footer_admin.php'; ?>