<?php
session_start(); 
if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
 $pageTitle = "Profil & Pengaturan";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; // CSRF
 $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $history = $_POST['history']; $vision = $_POST['vision']; $mission = $_POST['mission'];
    $motto = $_POST['motto']; $goals = $_POST['goals'];
    
    // Logo
    $logo_name = $_POST['old_logo'] ?? 'default.jpg';
    if (!empty($_FILES['logo']['name'])) {
        $logo_name = time() . '_logo_' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], '../img/' . $logo_name);
    }
    // Org Chart
    $org_name = $_POST['old_org'] ?? '';
    if (!empty($_FILES['org_chart']['name'])) {
        $org_name = time() . '_org_' . basename($_FILES['org_chart']['name']);
        move_uploaded_file($_FILES['org_chart']['tmp_name'], '../img/' . $org_name);
    }

    $db->query("UPDATE site_profile SET logo=:logo, org_chart=:org, history=:h, vision=:v, mission=:m, motto=:mt, goals=:g WHERE id=1");
    $db->bind(':logo', $logo_name); $db->bind(':org', $org_name); $db->bind(':h', $history);
    $db->bind(':v', $vision); $db->bind(':m', $mission); $db->bind(':mt', $motto); $db->bind(':g', $goals);
    $db->execute();
    echo "<script>alert('Sukses'); window.location='settings';</script>";
}

 $db->query("SELECT * FROM site_profile WHERE id=1"); $profile = $db->single();
require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-gear-fill me-2"></i>Pengaturan Website</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="row mb-4">
                <div class="col-md-6 text-center border-end">
                    <h6 class="text-muted mb-3">Logo Website</h6>
                    <img src="../img/<?php echo $profile['logo']; ?>" class="img-thumbnail rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                    <input type="file" name="logo" class="form-control form-control-sm">
                    <input type="hidden" name="old_logo" value="<?php echo $profile['logo']; ?>">
                </div>
                <div class="col-md-6 text-center">
                    <h6 class="text-muted mb-3">Bagan Organisasi</h6>
                    <?php if(!empty($profile['org_chart'])): ?>
                    <img src="../img/<?php echo $profile['org_chart']; ?>" class="img-thumbnail mb-2" style="max-height: 200px;">
                    <?php else: echo "<div class='bg-light py-5 mb-2'>No Image</div>"; endif; ?>
                    <input type="file" name="org_chart" class="form-control form-control-sm">
                    <input type="hidden" name="old_org" value="<?php echo $profile['org_chart']; ?>">
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6 mb-3"><label>Motto</label><input type="text" name="motto" value="<?php echo htmlspecialchars($profile['motto']); ?>" class="form-control"></div>
                <div class="col-md-6 mb-3"><label>Sejarah</label><textarea name="history" class="form-control" rows="4"><?php echo $profile['history']; ?></textarea></div>
                <div class="col-md-6 mb-3"><label>Visi</label><textarea name="vision" class="form-control" rows="2"><?php echo $profile['vision']; ?></textarea></div>
                <div class="col-md-6 mb-3"><label>Misi</label><textarea name="mission" class="form-control" rows="4"><?php echo $profile['mission']; ?></textarea></div>
                <div class="col-md-12 mb-3"><label>Tujuan</label><textarea name="goals" class="form-control" rows="4"><?php echo $profile['goals']; ?></textarea></div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Simpan Perubahan</button>
        </form>
    </div>
</div>
<?php require_once 'footer_admin.php'; ?>