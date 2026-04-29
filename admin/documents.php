<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Download";
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
    $cat = $_POST['category']; 
    $input_type = $_POST['input_type']; // 'file' atau 'link'
    
    $file_to_save = '';
    $link_to_save = '';

    // Logika berdasarkan tipe input
    if ($input_type == 'file') {
        // Hapus link jika sebelumnya ada link
        $link_to_save = ''; 
        
        // Cek apakah ada upload file baru
        if(!empty($_FILES['doc']['name'])){
            // Jika edit, hapus file lama
            if($id){
                $db->query("SELECT file_path FROM documents WHERE id=:id"); $db->bind(':id', $id);
                $old = $db->single();
                if($old && $old['file_path'] && file_exists('../uploads/' . $old['file_path'])) unlink('../uploads/' . $old['file_path']);
            }
            $file_to_save = time() . '_' . basename($_FILES['doc']['name']);
            move_uploaded_file($_FILES['doc']['tmp_name'], '../uploads/' . $file_to_save);
        } else {
            // Jika tidak upload baru, pertahankan file lama (hanya saat edit)
            if($id) {
                $db->query("SELECT file_path FROM documents WHERE id=:id"); $db->bind(':id', $id);
                $old = $db->single();
                $file_to_save = $old['file_path'] ?? '';
            }
        }
    } else {
        // Tipe Link
        $link_to_save = $_POST['link_url'];
        // Kosongkan file karena pakai link
        if($id) {
            $db->query("SELECT file_path FROM documents WHERE id=:id"); $db->bind(':id', $id);
            $old = $db->single();
            if($old && $old['file_path'] && file_exists('../uploads/' . $old['file_path'])) unlink('../uploads/' . $old['file_path']);
        }
        $file_to_save = '';
    }

    // Query DB
    if($id){
        $db->query("UPDATE documents SET title=:t, category=:c, file_path=:f, link_url=:l WHERE id=:id");
        $db->bind(':id', $id);
    } else {
        $db->query("INSERT INTO documents (title, category, file_path, link_url) VALUES (:t, :c, :f, :l)");
    }
    $db->bind(':t', $title); 
    $db->bind(':c', $cat); 
    $db->bind(':f', $file_to_save);
    $db->bind(':l', $link_to_save);
    
    $db->execute(); 
    header('Location: documents');
}

// Proses Hapus
if(isset($_GET['delete'])){ 
    $db->query("SELECT file_path FROM documents WHERE id=:id"); $db->bind(':id', $_GET['delete']); $row = $db->single();
    if ($row && $row['file_path'] && file_exists('../uploads/' . $row['file_path'])) unlink('../uploads/' . $row['file_path']);
    $db->query("DELETE FROM documents WHERE id=:id"); $db->bind(':id', $_GET['delete']); $db->execute(); header('Location: documents'); 
}

 $db->query("SELECT * FROM documents ORDER BY created_at DESC"); 
 $rows = $db->resultSet();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header"><h5 class="text-primary">Kelola Dokumen & Link</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" class="row g-3" id="formDoc">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="col-md-3">
                <input type="text" name="title" id="editTitle" class="form-control" placeholder="Judul Dokumen" required>
            </div>
            <div class="col-md-2">
                <select name="category" id="editCat" class="form-select">
                    <option>Akreditasi</option><option>SOP</option><option>SK</option><option>Brosur</option><option>Survey</option><option>Lainnya</option>
                </select>
            </div>
            
            <!-- TOMBOL PILIHAN TIPE INPUT -->
            <div class="col-md-2">
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="input_type" id="typeFile" value="file" checked>
                    <label class="btn btn-outline-primary" for="typeFile"><i class="fas fa-upload"></i> File</label>

                    <input type="radio" class="btn-check" name="input_type" id="typeLink" value="link">
                    <label class="btn btn-outline-primary" for="typeLink"><i class="fas fa-link"></i> Link</label>
                </div>
            </div>

            <!-- INPUT FILE (Default) -->
            <div class="col-md-3" id="inputFile">
                <input type="file" name="doc" class="form-control">
            </div>

            <!-- INPUT LINK (Hidden default) -->
            <div class="col-md-3 d-none" id="inputLink">
                <input type="url" name="link_url" id="editLink" class="form-control" placeholder="https://drive.google.com/...">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped">
    <thead><tr><th>Judul</th><th>Tipe</th><th>Sumber</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?php echo $r['title']; ?></td>
            <td>
                <?php if(!empty($r['link_url'])): ?>
                    <span class="badge bg-success">Link</span>
                <?php else: ?>
                    <span class="badge bg-primary">File</span>
                <?php endif; ?>
            </td>
            <td><small><?php echo !empty($r['link_url']) ? substr($r['link_url'], 0, 30).'...' : $r['file_path']; ?></small></td>
            <td>
                <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    // Fungsi Toggle Input
    document.querySelectorAll('input[name="input_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value == 'file') {
                document.getElementById('inputFile').classList.remove('d-none');
                document.getElementById('inputLink').classList.add('d-none');
            } else {
                document.getElementById('inputFile').classList.add('d-none');
                document.getElementById('inputLink').classList.remove('d-none');
            }
        });
    });

    function editData(data) {
        document.getElementById('editId').value = data.id;
        document.getElementById('editTitle').value = data.title;
        document.getElementById('editCat').value = data.category;
        
        // Deteksi apakah ini link atau file
        if(data.link_url && data.link_url.length > 0) {
            // Jika Link
            document.getElementById('typeLink').checked = true;
            document.getElementById('inputFile').classList.add('d-none');
            document.getElementById('inputLink').classList.remove('d-none');
            document.getElementById('editLink').value = data.link_url;
        } else {
            // Jika File
            document.getElementById('typeFile').checked = true;
            document.getElementById('inputFile').classList.remove('d-none');
            document.getElementById('inputLink').classList.add('d-none');
            document.getElementById('editLink').value = '';
        }
        // Trigger change event manually to update UI
        var event = new Event('change');
        document.querySelector('input[name="input_type"]:checked').dispatchEvent(event);
        
        window.scrollTo(0, 0);
    }
</script>
<?php require_once 'footer_admin.php'; ?>