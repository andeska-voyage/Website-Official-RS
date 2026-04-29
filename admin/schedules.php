<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Jadwal Dokter";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

 $kd_dokter_filter = $_GET['kd'] ?? '';

// Proses Simpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $kd = $_POST['kd_dokter']; $hari = $_POST['hari_kerja']; $mulai = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai']; $kuota = $_POST['kuota']; $poli = $_POST['kd_poli'];

    $db->query("INSERT INTO jadwal (kd_dokter, hari_kerja, jam_mulai, jam_selesai, kuota, kd_poli) VALUES (:kd, :hari, :mulai, :selesai, :kuota, :poli)");
    $db->bind(':kd', $kd); $db->bind(':hari', $hari); $db->bind(':mulai', $mulai);
    $db->bind(':selesai', $selesai); $db->bind(':kuota', $kuota); $db->bind(':poli', $poli);
    $db->execute();
    header("Location: schedules?kd=$kd");
}

// Proses Hapus
if(isset($_GET['delete'])){ 
    $kd = $_GET['kd']; $hari = $_GET['hari']; $mulai = $_GET['mulai'];
    $db->query("DELETE FROM jadwal WHERE kd_dokter=:kd AND hari_kerja=:hari AND jam_mulai=:mulai");
    $db->bind(':kd', $kd); $db->bind(':hari', $hari); $db->bind(':mulai', $mulai);
    $db->execute();
    header("Location: schedules?kd=$kd"); 
}

// Ambil Data
 $db->query("SELECT kd_dokter, nm_dokter FROM dokter ORDER BY nm_dokter"); $dokters = $db->resultSet();
 $db->query("SELECT * FROM poliklinik"); $polis = $db->resultSet();

 $sql = "SELECT jadwal.kd_dokter, jadwal.hari_kerja, jadwal.jam_mulai, jadwal.jam_selesai, jadwal.kuota, dokter.nm_dokter, poliklinik.nm_poli 
        FROM jadwal JOIN dokter ON jadwal.kd_dokter = dokter.kd_dokter 
        LEFT JOIN poliklinik ON jadwal.kd_poli = poliklinik.kd_poli";
if($kd_dokter_filter) { $sql .= " WHERE jadwal.kd_dokter = :kd"; }
 $sql .= " ORDER BY FIELD(jadwal.hari_kerja, 'SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AKHAD'), jadwal.jam_mulai";

 $db->query($sql);
if($kd_dokter_filter) $db->bind(':kd', $kd_dokter_filter);
 $schedules = $db->resultSet();

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">Manajemen Jadwal Praktek</div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="kd" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Dokter --</option>
                    <?php foreach($dokters as $d): ?>
                    <option value="<?php echo $d['kd_dokter']; ?>" <?php echo ($kd_dokter_filter == $d['kd_dokter']) ? 'selected' : ''; ?>>
                        <?php echo $d['nm_dokter']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if($kd_dokter_filter): ?>
        <div class="bg-light p-3 rounded mb-4 border">
            <h6>Tambah Jadwal untuk: <strong><?php echo $kd_dokter_filter; ?></strong></h6>
            <form method="POST" class="row g-2">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="kd_dokter" value="<?php echo $kd_dokter_filter; ?>">
                <div class="col-md-2">
                    <select name="hari_kerja" class="form-select" required>
                        <option>SENIN</option><option>SELASA</option><option>RABU</option>
                        <option>KAMIS</option><option>JUMAT</option><option>SABTU</option><option>AKHAD</option>
                    </select>
                </div>
                <div class="col-md-2"><input type="time" name="jam_mulai" class="form-control" required></div>
                <div class="col-md-2"><input type="time" name="jam_selesai" class="form-control" required></div>
                <div class="col-md-2"><input type="number" name="kuota" class="form-control" placeholder="Kuota" required></div>
                <div class="col-md-2">
                    <select name="kd_poli" class="form-select">
                        <option value="">Pilih Poli</option>
                        <?php foreach($polis as $p): ?>
                        <option value="<?php echo $p['kd_poli']; ?>"><?php echo $p['nm_poli']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Tambah</button></div>
            </form>
        </div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead class="bg-light"><tr><th>Dokter</th><th>Hari</th><th>Jam</th><th>Poli</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach($schedules as $s): ?>
                <tr>
                    <td><?php echo $s['nm_dokter']; ?></td>
                    <td><?php echo $s['hari_kerja']; ?></td>
                    <td><?php echo $s['jam_mulai']; ?> - <?php echo $s['jam_selesai']; ?></td>
                    <td><?php echo $s['nm_poli']; ?></td>
                    <td>
                        <a href="?delete=1&kd=<?php echo $s['kd_dokter']; ?>&hari=<?php echo $s['hari_kerja']; ?>&mulai=<?php echo $s['jam_mulai']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer_admin.php'; ?>