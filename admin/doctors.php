<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Data Dokter";
require_once '../config/database.php'; 
 $db = new Database();

// ================= PROSES SIMPAN (INSERT/UPDATE) =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menggunakan Null Coalescing Operator (??) untuk mencegah error undefined key
    $kd_dokter      = $_POST['kd_dokter'] ?? '';
    $nm_dokter      = $_POST['nm_dokter'] ?? '';
    $jk             = $_POST['jk'] ?? 'L';
    $tmp_lahir      = $_POST['tmp_lahir'] ?? '-';
    $tgl_lahir      = $_POST['tgl_lahir'] ?? '1970-01-01';
    $gol_drh        = $_POST['gol_drh'] ?? '-';
    $agama          = $_POST['agama'] ?? 'ISLAM';
    $almt_tgl       = $_POST['almt_tgl'] ?? '-';
    $no_telp        = $_POST['no_telp'] ?? '0';
    $email          = $_POST['email'] ?? ''; // Kolom ini NOT NULL di DB
    $stts_nikah     = $_POST['stts_nikah'] ?? 'MENIKAH';
    $kd_sps         = $_POST['kd_sps'] ?? '-';
    $alumni         = $_POST['alumni'] ?? '-';
    $no_ijn_praktek = $_POST['no_ijn_praktek'] ?? '-';
    $status         = $_POST['status'] ?? '1';
    
    // Ambil nama foto lama dari input hidden
    $foto_lama = $_POST['old_foto'] ?? 'default.jpg';
    $foto_name = $foto_lama; // Defaultnya pakai foto lama

    // Logika Upload Foto Baru
    if (!empty($_FILES['foto']['name'])) {
        // Buat nama file baru
        $foto_name = time() . '_' . basename($_FILES['foto']['name']);
        
        // Upload file baru
        if (move_uploaded_file($_FILES['foto']['tmp_name'], '../img/' . $foto_name)) {
            // Jika upload sukses, HAPUS file lama (biar tidak menumpuk)
            // Kondisi: Jangan hapus jika foto lamanya adalah 'default.jpg'
            if ($foto_lama != 'default.jpg' && file_exists('../img/' . $foto_lama)) {
                unlink('../img/' . $foto_lama);
            }
        } else {
            echo "<script>alert('Gagal upload gambar. Cek permission folder img.');</script>";
            // Kembalikan nama foto ke lama jika upload gagal
            $foto_name = $foto_lama; 
        }
    }

    // Cek apakah Insert atau Update
    $db->query("SELECT * FROM dokter WHERE kd_dokter = :kd");
    $db->bind(':kd', $kd_dokter);
    $exists = $db->single();

    if ($exists) {
        // Query Update (TANPA ALIAS)
        $sql = "UPDATE dokter SET 
                nm_dokter=:nm_dokter, jk=:jk, tmp_lahir=:tmp_lahir, tgl_lahir=:tgl_lahir, 
                gol_drh=:gol_drh, agama=:agama, almt_tgl=:almt_tgl, no_telp=:no_telp, 
                email=:email, stts_nikah=:stts_nikah, kd_sps=:kd_sps, alumni=:alumni, 
                no_ijn_praktek=:no_ijn_praktek, status=:status, foto=:foto 
                WHERE kd_dokter=:kd_dokter";
    } else {
        // Query Insert (TANPA ALIAS)
        $sql = "INSERT INTO dokter (
                kd_dokter, nm_dokter, jk, tmp_lahir, tgl_lahir, gol_drh, agama, 
                almt_tgl, no_telp, email, stts_nikah, kd_sps, alumni, 
                no_ijn_praktek, status, foto
                ) VALUES (
                :kd_dokter, :nm_dokter, :jk, :tmp_lahir, :tgl_lahir, :gol_drh, :agama, 
                :almt_tgl, :no_telp, :email, :stts_nikah, :kd_sps, :alumni, 
                :no_ijn_praktek, :status, :foto
                )";
    }
    
    $db->query($sql);
    // Binding Data
    $db->bind(':kd_dokter', $kd_dokter);
    $db->bind(':nm_dokter', $nm_dokter);
    $db->bind(':jk', $jk);
    $db->bind(':tmp_lahir', $tmp_lahir);
    $db->bind(':tgl_lahir', $tgl_lahir);
    $db->bind(':gol_drh', $gol_drh);
    $db->bind(':agama', $agama);
    $db->bind(':almt_tgl', $almt_tgl);
    $db->bind(':no_telp', $no_telp);
    $db->bind(':email', $email);
    $db->bind(':stts_nikah', $stts_nikah);
    $db->bind(':kd_sps', $kd_sps);
    $db->bind(':alumni', $alumni);
    $db->bind(':no_ijn_praktek', $no_ijn_praktek);
    $db->bind(':status', $status);
    $db->bind(':foto', $foto_name);
    
    if($db->execute()) {
        echo "<script>alert('Data dokter berhasil disimpan'); window.location='doctors';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data ke database.');</script>";
    }
}

// ================= PROSES HAPUS =================
if (isset($_GET['delete'])) {
    // Ambil nama foto sebelum hapus data
    $db->query("SELECT foto FROM dokter WHERE kd_dokter = :kd");
    $db->bind(':kd', $_GET['delete']);
    $row = $db->single();
    
    if ($row) {
        $foto_hapus = $row['foto'];
        // Hapus data di DB
        $db->query("DELETE FROM dokter WHERE kd_dokter = :kd");
        $db->bind(':kd', $_GET['delete']);
        if($db->execute()){
            // Hapus file fisik jika bukan default
            if ($foto_hapus != 'default.jpg' && file_exists('../img/' . $foto_hapus)) {
                unlink('../img/' . $foto_hapus);
            }
        }
    }
    header('Location: doctors');
}

// ================= AMBIL DATA =================
// Query TANPA ALIAS
 $db->query("SELECT dokter.kd_dokter, dokter.nm_dokter, dokter.foto, dokter.status, dokter.email, spesialis.nm_sps FROM dokter LEFT JOIN spesialis ON dokter.kd_sps = spesialis.kd_sps ORDER BY dokter.nm_dokter ASC");
 $dokters = $db->resultSet();

// Ambil Data Spesialis untuk Dropdown
 $db->query("SELECT * FROM spesialis ORDER BY nm_sps");
 $spesialis_list = $db->resultSet();

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
        <span><i class="bi bi-people-fill me-2"></i>Data Dokter</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDoctor" onclick="resetForm()">
            <i class="bi bi-plus-circle me-1"></i> Tambah Dokter
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Kode</th>
                        <th>Foto</th>
                        <th>Nama Dokter</th>
                        <th>Spesialis</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dokters as $d): ?>
                    <tr>
                        <td><?php echo $d['kd_dokter']; ?></td>
                        <td><img src="../img/<?php echo $d['foto']; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:50%;" alt=""></td>
                        <td><?php echo $d['nm_dokter']; ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo $d['nm_sps']; ?></span></td>
                        <td><small><?php echo $d['email']; ?></small></td>
                        <td>
                            <?php if($d['status'] == '1'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Non-Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editDoctor(<?php echo htmlspecialchars(json_encode($d)); ?>)"><i class="bi bi-pencil"></i></button>
                            <a href="schedules?kd=<?php echo $d['kd_dokter']; ?>" class="btn btn-sm btn-info text-white" title="Jadwal"><i class="bi bi-clock"></i></a>
                            <a href="?delete=<?php echo $d['kd_dokter']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini? File foto juga akan terhapus.')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Dokter -->
<div class="modal fade" id="modalDoctor" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Dokter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Input tersembunyi untuk menyimpan nama foto lama -->
                    <input type="hidden" name="old_foto" id="old_foto">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Kode Dokter (PK)</label>
                            <input type="text" name="kd_dokter" id="f_kd" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nm_dokter" id="f_nm" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label>Email (Wajib)</label>
                            <input type="email" name="email" id="f_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>No. Telp</label>
                            <input type="text" name="no_telp" id="f_telp" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Spesialisasi</label>
                            <select name="kd_sps" id="f_sps" class="form-select">
                                <?php foreach($spesialis_list as $sp): ?>
                                <option value="<?php echo $sp['kd_sps']; ?>"><?php echo $sp['nm_sps']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                        
                        <!-- Field Hidden untuk data yang jarang diisi tapi wajib di DB -->
                        <input type="hidden" name="tmp_lahir" value="-">
                        <input type="hidden" name="tgl_lahir" value="1970-01-01">
                        <input type="hidden" name="agama" value="ISLAM">
                        <input type="hidden" name="almt_tgl" value="-">
                        <input type="hidden" name="alumni" value="-">
                        <input type="hidden" name="no_ijn_praktek" value="-">
                        <input type="hidden" name="jk" id="f_jk" value="L">
                        <input type="hidden" name="gol_drh" value="-">
                        <input type="hidden" name="stts_nikah" value="MENIKAH">

                        <div class="col-md-6 mb-3">
                            <label>Foto</label>
                            <input type="file" name="foto" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin ganti foto.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editDoctor(data) {
        document.getElementById('f_kd').value = data.kd_dokter;
        document.getElementById('f_kd').readOnly = true; 
        document.getElementById('f_nm').value = data.nm_dokter;
        document.getElementById('f_email').value = data.email;
        document.getElementById('f_telp').value = data.no_telp;
        document.getElementById('f_sps').value = data.kd_sps;
        document.getElementById('old_foto').value = data.foto;
        
        var myModal = new bootstrap.Modal(document.getElementById('modalDoctor'));
        myModal.show();
    }
    
    function resetForm() {
        document.getElementById('f_kd').value = "";
        document.getElementById('f_kd').readOnly = false;
        document.getElementById('f_nm').value = "";
        document.getElementById('f_email').value = "";
        document.getElementById('old_foto').value = "default.jpg";
    }
</script>

<?php require_once 'footer_admin.php'; ?>