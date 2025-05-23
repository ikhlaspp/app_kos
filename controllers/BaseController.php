<?php
abstract class BaseController {
    protected PDO $pdo;
    protected array $appConfig;

    /**
     * Constructor untuk BaseController.
     * @param PDO $pdo Objek koneksi database.
     * @param array $appConfig Array konfigurasi aplikasi.
     */
    public function __construct(PDO $pdo, array $appConfig) {
        $this->pdo = $pdo;
        $this->appConfig = $appConfig;
    }

    /**
     * Memuat view beserta header dan footer.
     * Variabel dari $data akan tersedia di dalam view.
     * $appConfig dan $pageTitle juga tersedia di view, header, dan footer.
     *
     * @param string $viewName Nama file view (misal: 'home/index' untuk views/home/index.php).
     * @param array $data Data yang akan di-passing ke view.
     * @param string|null $pageTitle Judul halaman.
     */
    protected function loadView(string $viewName, array $data = [], ?string $pageTitle = null): void {
        extract($data);
        $appConfig = $this->appConfig; // Membuat $appConfig tersedia di scope view

        $headerPath = $appConfig['INCLUDES_PATH'] . 'header.php';
        $viewPath   = $appConfig['VIEWS_PATH'] . str_replace('.', '/', $viewName) . '.php';
        $footerPath = $appConfig['INCLUDES_PATH'] . 'footer.php';

        if (file_exists($headerPath)) {
            require_once $headerPath;
        } else {
            // Fallback header minimal jika file tidak ditemukan
            echo "<!DOCTYPE html><html lang=\"id\"><head><meta charset=\"UTF-8\"><title>" . htmlspecialchars($pageTitle ?? $appConfig['APP_NAME'] ?? 'Aplikasi') . "</title></head><body>";
        }

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Menampilkan pesan error jika file view tidak ditemukan
            echo "<div style='color:red; padding:10px; border:1px solid red;'>Error: View '{$viewName}' tidak ditemukan di '{$viewPath}'.</div>";
        }

        if (file_exists($footerPath)) {
            require_once $footerPath;
        } else {
            // Fallback footer minimal
            echo "</body></html>";
        }
    }

    /**
     * Mengarahkan pengguna ke path lain dalam aplikasi atau URL absolut.
     * @param string $path Path tujuan (misal: 'kos/daftar') atau URL lengkap.
     */
    protected function redirect(string $path): void {
        $url = $path;
        // Periksa apakah path adalah URL absolut
        if (!(strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0)) {
            // Jika bukan URL absolut, gabungkan dengan BASE_URL
            // Ini mengasumsikan path internal aplikasi.
            $url = rtrim($this->appConfig['BASE_URL'], '/') . '/' . ltrim($path, '/');
        }
        
        header("Location: " . $url);
        exit; // Hentikan eksekusi skrip setelah redirect
    }

    /**
     * Menyimpan pesan flash (notifikasi sementara) di session.
     * Pesan ini akan bisa ditampilkan di request berikutnya.
     * @param string $message Pesan yang ingin ditampilkan.
     * @param string $type Tipe pesan (misal: 'success', 'error', 'info').
     */
    protected function setFlashMessage(string $message, string $type = 'info'): void {
        // Pastikan session sudah aktif
        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION['flash_message'] = [
                'message' => $message,
                'type'    => $type
            ];
        }
    }
}
?>