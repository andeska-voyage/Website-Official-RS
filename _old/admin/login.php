<?php
// ======================================================
// 1. START SESSION & REQUIRE FILES
// ======================================================
// Session harus start pertama kali
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';
require_once '../config/csrf.php'; // Panggil helper CSRF

 $db = new Database();
 $error = '';

// ======================================================
// 2. PROSES LOGIN (POST)
// ======================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- A. VALIDASI CSRF TOKEN ---
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        // Jika token tidak valid, hentikan proses
        $error = "Sesi tidak valid (CSRF Token Mismatch). Silakan refresh halaman.";
    } else {
        // --- B. AMBIL DATA INPUT ---
        $username = $_POST['username'];
        $password = $_POST['password'];

        // --- C. CEK DATABASE ---
        $db->query("SELECT * FROM admin WHERE username = :username");
        $db->bind(':username', $username);
        $admin = $db->single();

        if ($admin && password_verify($password, $admin['password'])) {
            
            // --- D. LOGIN SUKSES ---
            // 1. Regenerate ID (Anti Session Fixation)
            session_regenerate_id(true);

            // 2. Set Session Admin
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];

            // 3. Hapus CSRF Token lama (opsional, untuk keamanan extra)
            unset($_SESSION['csrf_token']);

            // 4. Redirect ke Dashboard
            header('Location: index');
            exit;
        } else {
            $error = "Username atau Password salah!";
        }
    }
}

// ======================================================
// 3. HALAMAN HTML
// ======================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
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
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- INPUT CSRF TOKEN (HIDDEN) -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">MASUK</button>
        </form>
    </div>

    <!-- Font Awesome untuk Icon -->
    <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
</body>
</html>