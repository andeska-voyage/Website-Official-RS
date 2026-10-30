<?php
// File: admin/repair.php
// Fungsi: Memperbaiki data admin dan menampilkan password yang benar

require_once '../config/database.php';
 $db = new Database();

// 1. Hapus admin lama (biar bersih)
 $db->query("DELETE FROM admin WHERE username = 'admin'");
 $db->execute();

// 2. Buat password baru yang PASTI BENAR
 $pass_asli = 'admin123';
 $pass_hash = password_hash($pass_asli, PASSWORD_DEFAULT);

// 3. Masukkan data baru
 $db->query("INSERT INTO admin (username, password) VALUES ('admin', :pass)");
 $db->bind(':pass', $pass_hash);

if($db->execute()) {
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px; background: #f0f0f0; padding: 20px; border-radius: 10px; max-width: 500px; margin-left: auto; margin-right: auto;'>";
    echo "<h1 style='color: green;'>✅ Sukses!</h1>";
    echo "<h3>Data admin sudah diperbaiki.</h3>";
    echo "<p>Silakan login dengan:</p>";
    echo "<table style='margin: auto; background: white; padding: 10px; border: 1px solid #ccc;'>";
    echo "<tr><td><strong>Username</strong></td><td>: admin</td></tr>";
    echo "<tr><td><strong>Password</strong></td><td>: admin123</td></tr>";
    echo "</table><br>";
    echo "<a href='login' style='display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Menuju Halaman Login</a>";
    echo "<br><br><small style='color: red;'><strong>PENTING:</strong> Hapus file repair.php ini setelah berhasil login!</small>";
    echo "</div>";
} else {
    echo "Gagal memperbaiki database.";
}
?>