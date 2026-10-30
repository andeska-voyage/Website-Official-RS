<?php
require_once __DIR__ . '/auth.php';
if (!$isAdmin) {
    http_response_code(403);
    exit('Akses ditolak.');
}
require_once __DIR__ . '/../config/csrf.php';
$db = new Database();
$pageTitle = 'Pengelolaan Akun User';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Sesi halaman sudah kadaluarsa. Silakan muat ulang halaman.';
    } elseif (isset($_POST['delete_id'])) {
        $db->query('DELETE FROM dashboard_users WHERE id = :id');
        $db->bind(':id', (int) $_POST['delete_id'], PDO::PARAM_INT);
        if ($db->execute()) $message = 'Akun user berhasil dihapus.';
        else $error = 'Akun gagal dihapus.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $strongPassword = strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
        if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
            $error = 'Username harus 3–50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
        } elseif ($password !== $passwordConfirmation) {
            $error = 'Konfirmasi password tidak sama.';
        } elseif (!$strongPassword) {
            $error = 'Password minimal 8 karakter dan harus memuat huruf besar, huruf kecil, angka, serta karakter khusus.';
        } else {
            $db->query('INSERT INTO dashboard_users (username, password_hash) VALUES (:username, :password_hash)');
            $db->bind(':username', $username);
            $db->bind(':password_hash', password_hash($password, PASSWORD_DEFAULT));
            if ($db->execute()) $message = 'Akun user berhasil dibuat.';
            else $error = 'Username sudah digunakan atau akun gagal dibuat.';
        }
    }
}

$db->query('SELECT id, username, is_active, created_at FROM dashboard_users ORDER BY username');
$users = $db->resultSet();
require_once __DIR__ . '/nav_admin.php';
?>
<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Buat akun user hanya-baca</div>
                <div class="card-body">
                    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" minlength="3" maxlength="50" required></div>
                        <div class="mb-3">
                            <label class="form-label" for="userPassword">Password</label>
                            <div class="input-group">
                                <input type="password" id="userPassword" name="password" class="form-control" minlength="8" autocomplete="new-password" aria-describedby="passwordHelp passwordRequirements" required>
                                <button class="btn btn-outline-secondary password-visibility-toggle" type="button" data-target="userPassword" aria-label="Tampilkan password" aria-pressed="false"><i class="bi bi-eye"></i></button>
                            </div>
                            <div id="passwordHelp" class="form-text">Penuhi semua syarat berikut untuk mengaktifkan tombol buat akun.</div>
                            <div class="progress mt-3" style="height: 7px;" aria-hidden="true">
                                <div id="passwordStrengthBar" class="progress-bar bg-danger" role="presentation" style="width: 0%; transition: width .3s ease, background-color .3s ease;"></div>
                            </div>
                            <ul id="passwordRequirements" class="list-unstyled small mt-3 mb-0" aria-live="polite">
                                <li class="password-rule text-muted mb-1" data-rule="length"><i class="bi bi-circle me-2"></i>Minimal 8 karakter</li>
                                <li class="password-rule text-muted mb-1" data-rule="uppercase"><i class="bi bi-circle me-2"></i>Memiliki huruf besar (A–Z)</li>
                                <li class="password-rule text-muted mb-1" data-rule="lowercase"><i class="bi bi-circle me-2"></i>Memiliki huruf kecil (a–z)</li>
                                <li class="password-rule text-muted mb-1" data-rule="number"><i class="bi bi-circle me-2"></i>Memiliki angka (0–9)</li>
                                <li class="password-rule text-muted" data-rule="special"><i class="bi bi-circle me-2"></i>Memiliki karakter khusus (contoh: *, #, $, !, &amp;)</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="userPasswordConfirmation">Ulangi Password</label>
                            <div class="input-group">
                                <input type="password" id="userPasswordConfirmation" name="password_confirmation" class="form-control" minlength="8" autocomplete="new-password" aria-describedby="passwordMatchMessage" required>
                                <button class="btn btn-outline-secondary password-visibility-toggle" type="button" data-target="userPasswordConfirmation" aria-label="Tampilkan konfirmasi password" aria-pressed="false"><i class="bi bi-eye"></i></button>
                            </div>
                            <div id="passwordMatchMessage" class="form-text" aria-live="polite">Ulangi password yang sama.</div>
                        </div>
                        <button id="createUserButton" class="btn btn-primary" type="submit" disabled><i class="bi bi-person-plus me-1"></i>Buat Akun</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card"><div class="card-header">Akun user</div><div class="table-responsive"><table class="table table-hover align-middle mb-0">
                <thead><tr><th>Username</th><th>Status</th><th>Dibuat</th><th></th></tr></thead><tbody>
                <?php foreach ($users as $user): ?><tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>"><?php echo $user['is_active'] ? 'Aktif' : 'Nonaktif'; ?></span></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td><form method="POST" onsubmit="return confirm('Hapus akun ini?')"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>"><input type="hidden" name="delete_id" value="<?php echo (int) $user['id']; ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button></form></td>
                </tr><?php endforeach; ?>
                <?php if (!$users): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada akun user.</td></tr><?php endif; ?>
                </tbody></table></div></div>
        </div>
    </div>
</div>
<script>
(() => {
    const passwordInput = document.getElementById('userPassword');
    const confirmationInput = document.getElementById('userPasswordConfirmation');
    const submitButton = document.getElementById('createUserButton');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const matchMessage = document.getElementById('passwordMatchMessage');
    const checks = {
        length: value => Array.from(value).length >= 8,
        uppercase: value => /[A-Z]/.test(value),
        lowercase: value => /[a-z]/.test(value),
        number: value => /[0-9]/.test(value),
        special: value => /[^A-Za-z0-9]/.test(value)
    };

    function updatePasswordFeedback() {
        const value = passwordInput.value;
        let passed = 0;
        Object.entries(checks).forEach(([rule, check]) => {
            const item = document.querySelector(`[data-rule="${rule}"]`);
            const icon = item.querySelector('i');
            const valid = check(value);
            item.classList.toggle('text-success', valid);
            item.classList.toggle('text-muted', !valid);
            icon.classList.toggle('bi-check-circle-fill', valid);
            icon.classList.toggle('bi-circle', !valid);
            if (valid) passed++;
        });
        strengthBar.style.width = `${passed * 20}%`;
        strengthBar.classList.toggle('bg-danger', passed < 3);
        strengthBar.classList.toggle('bg-warning', passed >= 3 && passed < 5);
        strengthBar.classList.toggle('bg-success', passed === 5);
        const passwordsMatch = value.length > 0 && value === confirmationInput.value;
        matchMessage.textContent = confirmationInput.value.length === 0
            ? 'Ulangi password yang sama.'
            : passwordsMatch ? 'Password cocok.' : 'Password belum cocok.';
        matchMessage.classList.toggle('text-success', passwordsMatch);
        matchMessage.classList.toggle('text-danger', confirmationInput.value.length > 0 && !passwordsMatch);
        submitButton.disabled = passed !== 5 || !passwordsMatch;
    }

    passwordInput.addEventListener('input', updatePasswordFeedback);
    confirmationInput.addEventListener('input', updatePasswordFeedback);
    document.querySelectorAll('.password-visibility-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(showPassword));
            button.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
            button.querySelector('i').classList.toggle('bi-eye', !showPassword);
            button.querySelector('i').classList.toggle('bi-eye-slash', showPassword);
        });
    });
    updatePasswordFeedback();
})();
</script>
<?php require_once __DIR__ . '/footer_admin.php'; ?>
