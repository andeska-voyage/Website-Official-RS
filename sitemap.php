<?php
// sitemap.php
require_once 'config/database.php';
 $db = new Database();

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// 1. Halaman Statis
 $pages = ['index', 'layanan', 'dokter', 'berita', 'pengumuman', 'download', 'team'];
foreach($pages as $p) {
    echo '<url>';
    echo '<loc>https://official.rsiarestuibu.my.id/' . $p . '</loc>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// 2. Halaman Dinamis (Berita)
 $db->query("SELECT slug, created_at FROM posts ORDER BY created_at DESC");
 $posts = $db->resultSet();
foreach($posts as $p) {
    echo '<url>';
    echo '<loc>https://official.rsiarestuibu.my.id/artikel/' . $p['slug'] . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($p['created_at'])) . '</lastmod>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

// 3. Halaman Dokter
 $db->query("SELECT slug FROM dokter WHERE status='1'");
 $doks = $db->resultSet();
foreach($doks as $d) {
    echo '<url>';
    echo '<loc>https://official.rsiarestuibu.my.id/dokter/' . $d['slug'] . '</loc>';
    echo '<priority>0.5</priority>';
    echo '</url>';
}

echo '</urlset>';
?>