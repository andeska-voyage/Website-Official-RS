<?php
// =========================================================
// MATIKAN TAMPILAN ERROR (PRODUKSI)
// Ini mencegah path server terekspos jika ada error
// =========================================================
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// ======================================================
// ================= SESSION SECURITY =================
if (session_status() == PHP_SESSION_NONE) {
    // Pengaturan Cookie Session
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax' // Pastikan ini 'Lax', bukan 'Strict'
    ]);
    
    session_start();
}

// ======================================================
// 3. DATABASE CLASS
// ======================================================
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct(){
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            // Jika koneksi gagal, tampilkan halaman error custom
            $this->error = $e->getMessage();
            // Log error ke file
            error_log("[DB Error] " . $this->error, 0);
            // Tampilkan pesan ramah (jangan expose detail error ke user)
            die("<h1>Maaf, terjadi kesalahan sistem.</h1><p>Tim kami sedang memperbaikinya. Coba beberapa saat lagi.</p>");
        }
    }

    // Method untuk mendapatkan query terakhir (dibutuhkan untuk debugging bind)
    public function getQuery() {
        return $this->stmt->queryString; 
    }

    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }

    public function bind($param, $value, $type = null){
        if(is_null($type)){
            switch(true){
                case is_int($value): $type = PDO::PARAM_INT; break;
                case is_bool($value): $type = PDO::PARAM_BOOL; break;
                case is_null($value): $type = PDO::PARAM_NULL; break;
                default: $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(){
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            error_log("[Query Error] " . $e->getMessage());
            return false; // Kembalikan false agar bisa cek di controller
        }
    }

    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll();
    }

    public function single(){
        $this->execute();
        return $this->stmt->fetch();
    }

    public function rowCount(){
        return $this->stmt->rowCount();
    }
}

// Kredensial DB
define('DB_HOST', 'localhost');
define('DB_USER', 'db_rsia');
define('DB_PASS', 'pT5sBDFEjHRKTrRh');
define('DB_NAME', 'db_rsia');
?>