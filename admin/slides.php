<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Hero Slider";
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
    $subtitle = $_POST['subtitle'];
    $btn_text = $_POST['btn_text'];
    $btn_link = $_POST['btn_link'];
    $ordering = $_POST['ordering'] ?? 99; // Tangkap input urutan
    $img = $_FILES['image']['name'] ?? '';

    if($id){
        $q = "UPDATE slides SET title=:t, subtitle=:s, btn_text=:bt, btn_link=:bl, ordering=:o"; 
        $p = [':t'=>$title, ':s'=>$subtitle, ':bt'=>$btn_text, ':bl'=>$btn_link, ':o'=>$ordering, ':id'=>$id];
        if($img){ 
            $db->query("SELECT image FROM slides WHERE id=:id"); $db->bind(':id', $id); $old = $db->single();
            if($old && $old['image'] && file_exists('../img/' . $old['image'])) unlink('../img/' . $old['image']);
            move_uploaded_file($_FILES['image']['tmp_name'], '../img/' . $img); 
            $q.=", image=:i"; $p[':i']=$img; 
        }
        $q.=" WHERE id=:id"; 
        $db->query($q); 
        foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='hero-img.jpg';
        move_uploaded_file($_FILES['image']['tmp_name'], '../img/' . $img);
        $db->query("INSERT INTO slides (title, subtitle, btn_text, btn_link, image, ordering) VALUES (:t, :s, :bt, :bl, :i, :o)");
        $db->bind(':t',$title); $db->bind(':s',$subtitle); $db->bind(':bt',$btn_text); $db->bind(':bl',$btn_link); $db->bind(':i',$img); $db->bind(':o',$ordering);
    } 
    $db->execute(); 
    header('Location: slides');
}

// Proses Hapus
if(isset($_GET['delete'])){ 
    $db->query("SELECT image FROM slides WHERE id=:id"); $db->bind(':id', $_GET['delete']); $row = $db->single();
    if ($row && $row['image'] != 'hero-img.jpg' && file_exists('../img/' . $row['image'])) unlink('../img/' . $row['image']);
    $db->query("DELETE FROM slides WHERE id=:id"); $db->bind(':id', $_GET['delete']); $db->execute(); header('Location: slides'); 
}

// Ambil Data (Urutkan berdasarkan kolom 'ordering')
 $db->query("SELECT * FROM slides ORDER BY ordering ASC"); 
 $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-images me-2"></i>Kelola Hero Slider</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="col-md-3">
                <label>Judul (Opsional)</label>
                <input type="text" name="title" id="editTitle" class="form-control" placeholder="Kosongkan jika gambar ada teks">
            </div>
            <div class="col-md-3">
                <label>Sub Judul (Opsional)</label>
                <input type="text" name="subtitle" id="editSubtitle" class="form-control" placeholder="Deskripsi singkat">
            </div>
            <div class="col-md-1">
                <label>Urutan</label>
                <input type="number" name="ordering" id="editOrder" class="form-control" value="99" required title="Isi angka, kecil = depan">
            </div>
            <div class="col-md-2">
                <label>Tombol (Teks)</label>
                <input type="text" name="btn_text" id="editBtnText" class="form-control" placeholder="Pelajari">
            </div>
            <div class="col-md-3">
                <label>Link Tombol</label>
                <input type="text" name="btn_link" id="editBtnLink" class="form-control" placeholder="#about">
            </div>
            <div class="col-md-6">
                <label>Gambar (Wajib)</label>
                <input type="file" name="image" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped">
    <thead><tr><th>No</th><th>Gambar</th><th>Judul</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php $no=1; foreach($rows as $r): ?>
        <tr>
            <td><span class="badge bg-primary"><?php echo $r['ordering']; ?></span></td>
            <td><img src="../img/<?php echo $r['image']; ?>" style="width:100px; height:50px; object-fit:cover;"></td>
            <td><?php echo $r['title']; ?></td>
            <td>
                <button onclick="editData('<?php echo $r['id']; ?>', '<?php echo htmlspecialchars($r['title']); ?>', '<?php echo htmlspecialchars($r['subtitle']); ?>', '<?php echo $r['btn_text']; ?>', '<?php echo $r['btn_link']; ?>', '<?php echo $r['ordering']; ?>')" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
function editData(id, t, s, bt, bl, o){
    document.getElementById('editId').value=id;
    document.getElementById('editTitle').value=t;
    document.getElementById('editSubtitle').value=s;
    document.getElementById('editBtnText').value=bt;
    document.getElementById('editBtnLink').value=bl;
    document.getElementById('editOrder').value=o;
}
</script>
<?php require_once 'footer_admin.php'; ?>