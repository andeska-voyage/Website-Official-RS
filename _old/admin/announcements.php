<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
 $pageTitle = "Pengumuman";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; $title = $_POST['title']; $content = $_POST['content']; $img = $_FILES['file']['name'] ?? '';
    if($id){
        $q="UPDATE announcements SET title=:t, content=:c"; $p=[':t'=>$title, ':c'=>$content, ':id'=>$id];
        if($img){ move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/'.$img); $q.=", file_path=:i"; $p[':i']=$img; }
        $q.=" WHERE id=:id"; $db->query($q); foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/'.$img);
        $db->query("INSERT INTO announcements (title, content, file_path) VALUES (:t, :c, :i)");
        $db->bind(':t',$title); $db->bind(':c',$content); $db->bind(':i',$img);
    } $db->execute(); header('Location: announcements');
}

if(isset($_GET['delete'])){ 
    $db->query("SELECT file_path FROM announcements WHERE id=:id"); $db->bind(':id',$_GET['delete']); $row = $db->single();
    if ($row && !empty($row['file_path']) && file_exists('../uploads/' . $row['file_path'])) unlink('../uploads/' . $row['file_path']);
    $db->query("DELETE FROM announcements WHERE id=:id"); $db->bind(':id',$_GET['delete']); $db->execute(); header('Location: announcements'); 
}

 $db->query("SELECT * FROM announcements ORDER BY created_at DESC"); $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-megaphone-fill me-2"></i>Buat Pengumuman</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            <div class="mb-3"><label>Judul</label><input type="text" name="title" id="editTitle" class="form-control" required></div>
            <div class="mb-3"><label>Isi</label><textarea name="content" id="editor" class="form-control"></textarea></div>
            <div class="row mb-3">
                <div class="col-md-6"><label>File (Opsional)</label><input type="file" name="file" class="form-control"></div>
                <div class="col-md-6 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Daftar Pengumuman</h5></div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                    <td>
                        <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></button>
                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('editor', {height:200, versionCheck:false}); function editData(d){$('#editId').val(d.id);$('#editTitle').val(d.title);CKEDITOR.instances.editor.setData(d.content);window.scrollTo(0,0);}</script>
<?php require_once 'footer_admin.php'; ?>