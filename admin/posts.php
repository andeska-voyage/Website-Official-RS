<?php
require_once __DIR__ . '/auth.php';

 $pageTitle = "Berita / Artikel";
require_once '../config/database.php'; 
require_once '../config/csrf.php'; 
 $db = new Database();

// ======================================================
// FUNGSI KOMPRESI GAMBAR ASLI (Max 1200px)
// ======================================================
function compressImage($source, $destination, $quality, $max_width = 1200) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source);
    else return false; 

    $width = imagesx($image);
    $height = imagesy($image);

    if ($width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($height * ($max_width / $width));
        $tmp = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        if ($info['mime'] == 'image/png') imagepng($tmp, $destination, 8);
        else imagejpeg($tmp, $destination, $quality);
        imagedestroy($tmp);
    } else {
        if ($info['mime'] == 'image/png') imagepng($image, $destination, 8);
        else imagejpeg($image, $destination, $quality);
    }
    
    imagedestroy($image);
    return true;
}

// ======================================================
// FUNGSI BUAT THUMBNAIL KECIL (Max 400px)
// ======================================================
function createThumbnail($source, $destination, $max_width = 400) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source);
    else return false;

    $width = imagesx($image);
    $height = imagesy($image);
    
    $new_width = $max_width;
    $new_height = floor($height * ($max_width / $width));
    $tmp = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    if ($info['mime'] == 'image/png') imagepng($tmp, $destination, 8);
    else imagejpeg($tmp, $destination, 80);
    
    imagedestroy($tmp);
    imagedestroy($image);
    return true;
}

// ======================================================
// PROSES SIMPAN / UPDATE
// ======================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("<script>alert('Akses ditolak!'); history.back();</script>");
    }

    $id = $_POST['id'] ?? ''; 
    $title = $_POST['title']; 
    $content = $_POST['content']; 
    $category_id = $_POST['category_id']; 
    $img = $_FILES['image']['name'] ?? '';

    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = trim($slug, '-');

    if($id){
        $q = "UPDATE posts SET title=:t, slug=:slug, content=:c, category_id=:cat"; 
        $p = [':t'=>$title, ':slug'=>$slug, ':c'=>$content, ':cat'=>$category_id, ':id'=>$id];
        if($img){ 
            $db->query("SELECT image FROM posts WHERE id=:id"); 
            $db->bind(':id',$id); 
            $old = $db->single();
            if ($old && $old['image'] != 'blog-1.jpg') {
                if (file_exists('../img/' . $old['image'])) unlink('../img/' . $old['image']);
                if (file_exists('../img/thumb_' . $old['image'])) unlink('../img/thumb_' . $old['image']);
            }

            $img = time() . '_' . basename($_FILES['image']['name']);
            $target = '../img/' . $img;
            $tmp = $_FILES['image']['tmp_name'];
            
            compressImage($tmp, $target, 75);
            createThumbnail($tmp, '../img/thumb_' . $img);
            
            $q.=", image=:i"; $p[':i']=$img; 
        }
        $q.=" WHERE id=:id"; 
        $db->query($q); 
        foreach($p as $k=>$v) $db->bind($k,$v);
    } else {
        if(!$img) $img='blog-1.jpg'; 
        else {
            $img = time() . '_' . basename($_FILES['image']['name']);
            $target = '../img/' . $img;
            $tmp = $_FILES['image']['tmp_name'];
            compressImage($tmp, $target, 75);
            createThumbnail($tmp, '../img/thumb_' . $img);
        }
        
        $db->query("INSERT INTO posts (title, slug, content, category_id, image) VALUES (:t, :slug, :c, :cat, :i)");
        $db->bind(':t',$title); $db->bind(':slug',$slug); $db->bind(':c',$content); $db->bind(':cat',$category_id); $db->bind(':i',$img);
    } 
    $db->execute(); 
    
    // HAPUS CACHE HOMEPAGE
    @unlink(__DIR__ . '/../cache/' . md5('homepage_news') . '.cache');
    
    header('Location: posts');
}

// ======================================================
// PROSES HAPUS (DENGAN HAPUS FILE GAMBAR DI KONTEN JUGA)
// ======================================================
if(isset($_GET['delete'])){ 
    $db->query("SELECT image, content FROM posts WHERE id=:id"); 
    $db->bind(':id', $_GET['delete']); 
    $row = $db->single();
    
    if ($row) {
        // A. Hapus Gambar Utama & Thumbnail
        if ($row['image'] != 'blog-1.jpg') {
            if (file_exists('../img/' . $row['image'])) unlink('../img/' . $row['image']);
            if (file_exists('../img/thumb_' . $row['image'])) unlink('../img/thumb_' . $row['image']);
        }
        
        // B. Hapus Gambar di dalam Konten CKEditor (Folder uploads)
        if (!empty($row['content'])) {
            preg_match_all('/src="(?:\.\.\/)?uploads\/([^"]+)"/', $row['content'], $matches);
            if (isset($matches[1]) && !empty($matches[1])) {
                foreach ($matches[1] as $filename) {
                    $file_path = '../uploads/' . $filename;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
        }
    }
    
    // Hapus data dari database
    $db->query("DELETE FROM posts WHERE id=:id"); 
    $db->bind(':id', $_GET['delete']); 
    $db->execute(); 
    
    // HAPUS CACHE HOMEPAGE
    @unlink(__DIR__ . '/../cache/' . md5('homepage_news') . '.cache');
    
    header('Location: posts'); 
}

// ======================================================
// AMBIL DATA UNTUK DITAMPILKAN (DENGAN PAGINATION)
// ======================================================
// 1. Hitung Total Data
 $db->query("SELECT COUNT(*) as total FROM posts");
 $totalRows = $db->single()['total'];

// 2. Konfigurasi Pagination
 $limit = 10; // Jumlah berita per halaman
 $totalPages = ceil($totalRows / $limit);
 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
 $page = max(1, $page); // Pastikan halaman minimal 1
 $offset = ($page - 1) * $limit;

// 3. Query Data dengan Limit & Offset
 $db->query("SELECT posts.*, categories.name as cname 
            FROM posts 
            LEFT JOIN categories ON posts.category_id = categories.id 
            ORDER BY posts.created_at DESC 
            LIMIT :offset, :limit"); 
 $db->bind(':offset', $offset, PDO::PARAM_INT);
 $db->bind(':limit', $limit, PDO::PARAM_INT);
 $rows = $db->resultSet();

// 4. Ambil Data Kategori untuk Form
 $db->query("SELECT * FROM categories"); 
 $cats = $db->resultSet();

require_once 'nav_admin.php';
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary"><i class="bi bi-newspaper me-2"></i>Tulis Berita Baru</h5></div>
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data" id="formPost">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Judul Berita</label>
                    <input type="text" name="title" id="editTitle" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Kategori</label>
                    <select name="category_id" id="editCat" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach($cats as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Isi Konten</label>
                <textarea name="content" id="editor" class="form-control" rows="10"></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Gambar Utama (Sampul)</label>
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted">Akan otomatis dibuatkan versi Thumbnail (400px) dan Asli (1200px).</small>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2"><i class="bi bi-save me-2"></i>Publikasikan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Berita</h5>
        <small class="text-muted">Total: <?php echo $totalRows; ?> Berita</small>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="bg-light"><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th width="150">Aksi</th></tr></thead>
            <tbody>
                <?php if(!empty($rows)): ?>
                    <?php foreach($rows as $r): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($r['title']); ?></strong></td>
                        <td><span class="badge bg-secondary"><?php echo $r['cname']; ?></span></td>
                        <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                        <td>
                            <button onclick="editData(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></button>
                            <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus berita ini? Semua gambar terkait akan dihapus permanen dari server.')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada berita.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Nav Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination">
                    <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.ckeditor.com/4.20.0/full-all/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', { 
        height: 300, 
        versionCheck: false,
        extraPlugins: 'uploadimage',
        imageUploadUrl: 'upload.php',
        filebrowserImageUploadUrl: 'upload.php',
        disallowedContent: 'img[width,height,style]',
        disableObjectResizing: true
    });

    function editData(data) {
        $('#editId').val(data.id); 
        $('#editTitle').val(data.title); 
        $('#editCat').val(data.category_id);
        CKEDITOR.instances.editor.setData(data.content);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
</script>
<?php require_once 'footer_admin.php'; ?>