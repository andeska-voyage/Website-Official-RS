<?php
session_start();
require_once '../config/database.php';
 $db = new Database();

if (isset($_SESSION['admin_id'])) { header('Location: index'); exit; }

 $error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db->query("SELECT * FROM admin WHERE username = :username");
    $db->bind(':username', $username);
    $admin = $db->single();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $admin['username'];
        header('Location: index');
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>body { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }</style>
</head>
<body>
    <div class="card p-5 shadow-lg" style="width: 400px; border-radius: 15px;">
        <div class="text-center mb-4">
            <i class="fas fa-hospital fa-3x text-primary mb-3"></i>
            <h3 class="text-dark">Login Admin</h3>
        </div>
        <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-muted">Username</label>
                <input type="text" name="username" class="form-control form-control-lg" placeholder="admin" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="admin123" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">MASUK</button>
        </form>
    </div>
</body>
</html>