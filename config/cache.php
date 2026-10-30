<?php
// File: config/cache.php

// Folder untuk menyimpan file cache (akan dibuat otomatis)
define('CACHE_DIR', __DIR__ . '/../cache/');

// Fungsi untuk mengambil data cache
function get_cache($key, $ttl = 300) { // Default 300 detik (5 menit)
    $file = CACHE_DIR . md5($key) . '.cache';
    if (file_exists($file) && (time() - filemtime($file) < $ttl)) {
        return unserialize(file_get_contents($file));
    }
    return false;
}

// Fungsi untuk menyimpan data cache
function set_cache($key, $data) {
    if (!is_dir(CACHE_DIR)) {
        mkdir(CACHE_DIR, 0777, true);
    }
    $file = CACHE_DIR . md5($key) . '.cache';
    file_put_contents($file, serialize($data));
}
?>