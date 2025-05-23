<?php

$host     = 'localhost';         
$db_name  = 'app_kos';          
$username = 'root';              
$password = '';                  
$charset  = 'utf8mb4';            
$dsn = "mysql:host={$host};dbname={$db_name};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,       
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,              
    PDO::ATTR_EMULATE_PREPARES   => false,                         
];


$pdo = null; 

try {

    $pdo = new PDO($dsn, $username, $password, $options);

} catch (\PDOException $e) {
   
    $current_env = 'production'; 
    if (isset($GLOBALS['appConfig']['APP_ENV'])) { 
         $current_env = $GLOBALS['appConfig']['APP_ENV'];
    } elseif (defined('APP_ENV')) { 
         $current_env = APP_ENV;
    }

    if ($current_env === 'development') {

        header('Content-Type: text/plain; charset=utf-8'); 
        echo "KONEKSI DATABASE GAGAL!\n\n";
        echo "Pesan Error: " . $e->getMessage() . "\n";
        echo "Kode Error: " . $e->getCode() . "\n";
        echo "File: " . $e->getFile() . " pada baris " . $e->getLine() . "\n\n";
        echo "Detail Koneksi yang Digunakan (password disembunyikan untuk keamanan):\n";
        echo "DSN: " . htmlspecialchars($dsn) . "\n";
        echo "User: " . htmlspecialchars($username) . "\n\n";
        echo "Pastikan hal berikut:\n";
        echo "1. Server database MySQL (host: '{$host}') Anda sudah berjalan.\n";
        echo "2. Nama database ('{$db_name}') sudah benar dan sudah dibuat.\n";
        echo "3. Username ('{$username}') dan password sudah benar.\n";
        echo "4. User ('{$username}') memiliki hak akses yang cukup ke database '{$db_name}'.\n";
    } else {
        header('HTTP/1.1 503 Service Unavailable'); 
          exit('Maaf, saat ini kami sedang mengalami kendala teknis pada layanan database. Silakan coba beberapa saat lagi.');
    }
    exit; 
}

?>