<?php
// 1. PANGGIL DATABASE DULU (Agar Session Mulai dengan Pengaturan yang Benar)
require_once '../config/database.php';
require_once '../config/csrf.php';

// 2. SETELAH ITU, BARU KIRIM HEADER ANTI-CACHE
// Ini memaksa browser untuk tidak menyimpan halaman ini, sehingga token selalu baru.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

 $db = new Database();
 $error = '';

// 3. PROSES LOGIN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = "Sesi halaman sudah kadaluarsa. Silakan coba lagi.";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $db->query("SELECT * FROM admin WHERE username = :username");
        $db->bind(':username', $username);
        $admin = $db->single();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            unset($_SESSION['csrf_token']);
            header('Location: index');
            exit;
        } else {
            $error = "Username atau Password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <!-- Meta tag tambahan untuk memastikan tidak cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }</style>
</head>
<body>
    <div class="card p-5 shadow" style="width: 400px;">
        <div class="text-center mb-4">
            <i class="fas fa-hospital fa-3x text-primary mb-3"></i>
            <h3 class="text-dark">Login Admin</h3>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">MASUK</button>
        </form>
    </div>
    <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
</body>
</html>