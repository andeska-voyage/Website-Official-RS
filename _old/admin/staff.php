<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Data Karyawan";
require_once '../config/database.php'; 
 $db = new Database();

// Proses Tambah/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? ''; 
    $name = $_POST['name']; 
    $pos = $_POST['position']; 
    $cat = $_POST['category']; 
    $img = $_FILES['image']['name'] ?? '';

    if ($id) {
        // Update
        $q = "UPDATE staff SET name=:n, position=:p, category=:c"; 
        $pars = [':n'=>$name, ':p'=>$pos, ':c'=>$cat, ':id'=>$id];
        if($img) { 
            move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img); 
            $q.=", image=:i"; 
            $pars[':i']=$img; 
        }
        $q.=" WHERE id=:id"; 
        $db->query($q); 
        foreach($pars as $k=>$v) $db->bind($k,$v);
    } else {
        // Insert
        if(!$img) $img='team-1.jpg'; // Default image
        move_uploaded_file($_FILES['image']['tmp_name'], '../img/'.$img);
        $db->query("INSERT INTO staff (name, position, category, image) VALUES (:n, :p, :c, :i)");
        $db->bind(':n',$name); $db->bind(':p',$pos); $db->bind(':c',$cat); $db->bind(':i',$img);
    }
    $db->execute(); 
    header('Location: staff');
}

// Proses Hapus
if(isset($_GET['delete'])){ 
    $db->query("DELETE FROM staff WHERE id=:id"); 
    $db->bind(':id',$_GET['delete']); 
    $db->execute(); 
    header('Location: staff'); 
}

// Ambil Data
 $db->query("SELECT * FROM staff ORDER BY category, name ASC"); 
 $rows = $db->resultSet();

require_once 'nav_admin.php';
?>

    <!-- Tampilan Notifikasi -->
    <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success">Data berhasil disimpan.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Manajemen Karyawan & Staf</span>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Form Input di Kiri -->
                <div class="col-lg-4 mb-4">
                    <div class="bg-light p-4 rounded">
                        <h5 class="mb-3">Form Input</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="editId">
                            <div class="mb-3">
                                <label class="form-label small">Nama Lengkap</label>
                                <input type="text" name="name" id="editName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Jabatan</label>
                                <input type="text" name="position" id="editPos" class="form-control" placeholder="Cth: Perawat" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Kategori</label>
                                <select name="category" id="editCat" class="form-select">
                                    <option value="Direksi">Direksi</option>
                                    <option value="Medis">Medis</option>
                                    <option value="Administrasi">Administrasi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Foto</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle me-2"></i>Simpan</button>
                        </form>
                    </div>
                </div>

                <!-- Tabel Data di Kanan -->
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Foto</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $r): ?>
                                <tr>
                                    <td width="80">
                                        <img src="../img/<?php echo $r['image']; ?>" class="rounded-circle shadow-sm" style="width:50px; height:50px; object-fit:cover;">
                                    </td>
                                    <td><strong><?php echo $r['name']; ?></strong></td>
                                    <td><?php echo $r['position']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $r['category']; ?></span></td>
                                    <td>
                                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'footer_admin.php'; ?>