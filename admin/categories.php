<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Kategori";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; $name = $_POST['name']; 
    $slug = strtolower(str_replace(' ', '-', $name));
    
    if($id) { 
        $db->query("UPDATE categories SET name=:n, slug=:s WHERE id=:id"); 
        $db->bind(':id', $id); 
    } else { 
        $db->query("INSERT INTO categories (name, slug) VALUES (:n, :s)"); 
    }
    $db->bind(':n', $name); $db->bind(':s', $slug);
    $db->execute(); header('Location: categories');
}
if(isset($_GET['delete'])){ 
    $db->query("DELETE FROM categories WHERE id = :id"); $db->bind(':id', $_GET['delete']); $db->execute(); header('Location: categories'); 
}
 $db->query("SELECT * FROM categories"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<h2>Data Kategori</h2>
<div class="card p-4 mb-4">
    <form method="POST" class="row g-3">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <input type="hidden" name="id" id="editId">
        <div class="col-md-3"><input type="text" name="name" id="editName" class="form-control" placeholder="Nama Kategori" required></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
    </form>
</div>
<table class="table table-striped">
    <thead><tr><th>Nama</th><th>Slug</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?php echo $r['name']; ?></td>
            <td><code><?php echo $r['slug']; ?></code></td>
            <td>
                <button onclick="edit('<?php echo $r['id']; ?>', '<?php echo $r['name']; ?>')" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>function edit(id, nm){ document.getElementById('editId').value=id; document.getElementById('editName').value=nm; }</script>
<?php require_once 'footer_admin.php'; ?>