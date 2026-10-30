<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }

 $pageTitle = "Profil & Pengaturan";
require_once '../config/database.php'; 
 $db = new Database();

// Ambil Data Saat Ini
 $db->query("SELECT * FROM site_profile WHERE id=1");
 $profile = $db->single();

// Proses Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $history = $_POST['history'];
    $vision = $_POST['vision'];
    $mission = $_POST['mission'];
    $motto = $_POST['motto'];
    $goals = $_POST['goals'];
    
    // --- LOGO ---
    $logo_name = $profile['logo']; // Default dari DB
    if (!empty($_FILES['logo']['name'])) {
        $logo_name = time() . '_logo_' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], '../img/' . $logo_name);
        // Hapus logo lama jika bukan default
        if ($profile['logo'] != 'default.jpg' && file_exists('../img/' . $profile['logo'])) {
            unlink('../img/' . $profile['logo']);
        }
    }

    // --- BAGAN ORGANISASI (ORG CHART) ---
    $org_name = $profile['org_chart']; // Default dari DB
    if (!empty($_FILES['org_chart']['name'])) {
        $org_name = time() . '_org_' . basename($_FILES['org_chart']['name']);
        move_uploaded_file($_FILES['org_chart']['tmp_name'], '../img/' . $org_name);
        // Hapus bagan lama
        if (!empty($profile['org_chart']) && file_exists('../img/' . $profile['org_chart'])) {
            unlink('../img/' . $profile['org_chart']);
        }
    }

    // Update Database
    $db->query("UPDATE site_profile SET logo=:logo, org_chart=:org, history=:history, vision=:vision, mission=:mission, motto=:motto, goals=:goals WHERE id=1");
    $db->bind(':logo', $logo_name);
    $db->bind(':org', $org_name);
    $db->bind(':history', $history);
    $db->bind(':vision', $vision);
    $db->bind(':mission', $mission);
    $db->bind(':motto', $motto);
    $db->bind(':goals', $goals);
    
    if($db->execute()) {
        echo "<script>alert('Profil berhasil diperbarui'); window.location='settings';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data');</script>";
    }
    
    // Refresh data
    $db->query("SELECT * FROM site_profile WHERE id=1");
    $profile = $db->single();
}

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary"><i class="bi bi-gear-fill me-2"></i>Pengaturan Website</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            
            <!-- Baris 1: Logo & Bagan -->
            <div class="row mb-4">
                <div class="col-md-6 text-center border-end">
                    <h6 class="text-muted mb-3">Logo Website</h6>
                    <img src="../img/<?php echo $profile['logo']; ?>" class="img-thumbnail rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;" alt="Logo">
                    <input type="file" name="logo" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 text-center">
                    <h6 class="text-muted mb-3">Bagan Organisasi</h6>
                    <?php if(!empty($profile['org_chart'])): ?>
                    <img src="../img/<?php echo $profile['org_chart']; ?>" class="img-thumbnail mb-2" style="max-height: 200px; width: auto;" alt="Org Chart">
                    <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center mb-2" style="height: 150px;">
                        <span class="text-muted">Belum ada gambar</span>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="org_chart" class="form-control form-control-sm">
                    <small class="text-muted">Format: JPG/PNG. Disarankan Landscape.</small>
                </div>
            </div>

            <hr class="my-4">

            <!-- Baris 2: Data Profil -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Motto</label>
                    <input type="text" name="motto" value="<?php echo htmlspecialchars($profile['motto']); ?>" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Sejarah</label>
                    <textarea name="history" id="editor_history" class="form-control" rows="4"><?php echo $profile['history']; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Visi</label>
                    <textarea name="vision" id="editor_vision" class="form-control" rows="2"><?php echo $profile['vision']; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Misi</label>
                    <textarea name="mission" id="editor_mission" class="form-control" rows="4"><?php echo $profile['mission']; ?></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Tujuan</label>
                    <textarea name="goals" id="editor_goals" class="form-control" rows="4"><?php echo $profile['goals']; ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
    $('.form-control').summernote({
        height: 150,
        toolbar: [
            ['style', ['style']], 
            ['font', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ]
    });
</script>

<?php require_once 'footer_admin.php'; ?>