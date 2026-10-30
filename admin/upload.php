<?php
// File: rsofficial/admin/upload.php

require_once __DIR__ . '/auth.php';

// 1. KEAMANAN: Pastikan hanya Admin yang bisa upload
if (!$isAdmin) {
    http_response_code(403);
    echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Akses Ditolak. Sesi Kadaluarsa.']]);
    exit;
}

// Pastikan folder uploads ada dan bisa ditulis (writable)
 $uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Gagal membuat folder uploads. Cek permission folder.']]);
        exit;
    }
}

// Cek apakah ada file yang dikirim
if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
    
    $file = $_FILES['upload'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // 2. VALIDASI: Izinkan JPG, PNG, GIF, dan WebP
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($fileType, $allowedTypes)) {
        
        // Batasi ukuran file (Max 10MB)
        if ($fileSize <= 10 * 1024 * 1024) {
            
            // Buat nama file unik (Time + Random) agar tidak tertimpa
            $newFileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileType;
            $destination = $uploadDir . $newFileName;

            // 3. PROSES KOMPRESI (Dengan Fallback Keamanan)
            $compressed = false;
            if (function_exists('gd_info')) {
                $info = getimagesize($fileTmp);
                if ($info) {
                    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($fileTmp);
                    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($fileTmp);
                    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($fileTmp);
                    elseif ($info['mime'] == 'image/webp') $image = imagecreatefromwebp($fileTmp);
                    else $image = false;

                    if ($image) {
                        $width = imagesx($image);
                        $height = imagesy($image);
                        $max_width = 1200;

                        if ($width > $max_width) {
                            // Resize jika lebih besar dari 1200px
                            $new_width = $max_width;
                            $new_height = floor($height * ($max_width / $width));
                            $tmp = imagecreatetruecolor($new_width, $new_height);
                            
                            // Jaga transparansi untuk PNG
                            if ($info['mime'] == 'image/png') {
                                imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
                                imagealphablending($tmp, false);
                                imagesavealpha($tmp, true);
                            }

                            imagecopyresampled($tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                            
                            if ($info['mime'] == 'image/png') imagepng($tmp, $destination, 8);
                            elseif ($info['mime'] == 'image/gif') imagegif($tmp, $destination);
                            elseif ($info['mime'] == 'image/webp') imagewebp($tmp, $destination, 80);
                            else imagejpeg($tmp, $destination, 80);
                            
                            imagedestroy($tmp);
                            $compressed = true;
                        }
                        imagedestroy($image);
                    }
                }
            }

            // 4. FALLBACK: Jika kompresi gagal/ tidak didukung, simpan gambar aslinya
            if (!$compressed) {
                if (!move_uploaded_file($fileTmp, $destination)) {
                    $msg = "Gagal memindahkan file ke server. Cek permission folder 'uploads'.";
                    http_response_code(400);
                    echo json_encode(["uploaded" => 0, "error" => ["message" => $msg]]);
                    exit;
                }
            }
            
            // Return format JSON yang diminta CKEditor
            $url = '../uploads/' . $newFileName; 
            echo json_encode([
                "uploaded" => 1,
                "fileName" => $newFileName,
                "url" => $url
            ]);
            exit;

        } else {
            $msg = "Ukuran file terlalu besar (Max 10MB).";
        }
    } else {
        $msg = "Format file tidak didukung. Hanya JPG, PNG, GIF, dan WebP.";
    }
} else {
    $uploadError = $_FILES['upload']['error'] ?? 'Unknown';
    $msg = "Gagal upload file (Error Code: $uploadError).";
}

// ERROR: Return pesan error
http_response_code(400);
echo json_encode([
    "uploaded" => 0,
    "error" => ["message" => $msg]
]);
?>
