<?php
require_once 'config/database.php';
 $db = new Database();
header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

 $pages = ['', 'layanan', 'dokter', 'berita', 'pengumuman', 'download', 'team'];
foreach($pages as $p) {
    echo '<url>';
    echo '<loc>https://official.rsiarestuibu.my.id/' . $p . '</loc>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// Ambil Berita dengan ID (Bukan Slug)
 $db->query("SELECT id, created_at FROM posts ORDER BY created_at DESC");
 $posts = $db->resultSet();
foreach($posts as $p) {
    echo '<url>';
    echo '<loc>https://official.rsiarestuibu.my.id/artikel?data=' . base64_encode($p['id']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($p['created_at'])) . '</lastmod>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}
echo '</urlset>';
?>