<?php
// File: config/csrf.php

function generateCSRFToken() {
    // Selalu buat token baru jika belum ada, atau jika ingin selalu fresh
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    // Cek apakah token ada di session dan sama dengan yang dikirim
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}
?>