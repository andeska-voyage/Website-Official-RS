<?php
session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: login'); exit; }
require_once '../config/database.php'; $db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $history = $_POST['history'];
    $vision = $_POST['vision'];
    $mission = $_POST['mission'];
    $motto = $_POST['motto'];
    $goals = $_POST['goals'];
    $fileName = $_POST['old_logo'];

    if (!empty($_FILES['logo']['name'])) {
        $fileName = time() . '_' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], '../img/' . $fileName);
    }

    $db->query("UPDATE site_profile SET logo=:logo, history=:history, vision=:vision, mission=:mission, motto=:motto, goals=:goals WHERE id=1");
    $db->bind(':logo', $fileName);
    $db->bind(':history', $history);
    $db->bind(':vision', $vision);
    $db->bind(':mission', $mission);
    $db->bind(':motto', $motto);
    $db->bind(':goals', $goals);
    $db->execute();
    
    echo "<script>alert('Profil berhasil diperbarui'); window.location='settings';</script>";
}

 $db->query("SELECT * FROM site_profile WHERE id=1");
 $profile = $db->single();
require_once 'nav_admin.php';
?>

    <h2>Pengaturan Profil Rumah Sakit</h2>
    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    <img src="../img/<?php echo $profile['logo']; ?>" class="img-thumbnail rounded-circle mb-2" style="width:150px; height:150px; object-fit:cover;" id="previewLogo">
                    <input type="file" name="logo" class="form-control" onchange="document.getElementById('previewLogo').src = window.URL.createObjectURL(this.files[0])">
                    <input type="hidden" name="old_logo" value="<?php echo $profile['logo']; ?>">
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label>Motto</label>
                        <input type="text" name="motto" class="form-control" value="<?php echo htmlspecialchars($profile['motto']); ?>">
                    </div>
                    <div class="mb-3">
                        <label>Sejarah</label>
                        <textarea name="history" id="editor_history" class="form-control summernote-editor"><?php echo $profile['history']; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label>Visi</label>
                <textarea name="vision" id="editor_vision" class="form-control summernote-editor" rows="2"><?php echo $profile['vision']; ?></textarea>
            </div>
            <div class="mb-3">
                <label>Misi</label>
                <textarea name="mission" id="editor_mission" class="form-control summernote-editor" rows="4"><?php echo $profile['mission']; ?></textarea>
            </div>
            <div class="mb-3">
                <label>Tujuan</label>
                <textarea name="goals" id="editor_goals" class="form-control summernote-editor" rows="4"><?php echo $profile['goals']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </form>
    </div>

    <!-- TinyMCE -->
        <!-- Summernote untuk Settings -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi semua textarea dengan class summernote-editor
            $('.summernote-editor').summernote({
                height: 150,
                toolbar: [
                    ['font', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol']],
                ]
            });
        });
    </script>
<?php require_once 'footer_admin.php'; ?>