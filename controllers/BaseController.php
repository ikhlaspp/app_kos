<?php
abstract class BaseController {
    protected PDO $pdo;
    protected array $appConfig;

    public function __construct(PDO $pdo, array $appConfig) {
        $this->pdo = $pdo;
        $this->appConfig = $appConfig;
    }

    protected function loadView(string $viewName, array $data = [], ?string $pageTitle = null): void {
        extract($data);
        $appConfig = $this->appConfig; 

        $headerPath = $appConfig['INCLUDES_PATH'] . 'header.php';
        $viewPath   = $appConfig['VIEWS_PATH'] . str_replace('.', '/', $viewName) . '.php';
        $footerPath = $appConfig['INCLUDES_PATH'] . 'footer.php';

        // Urutan pemuatan: header, view utama, lalu footer
        if (file_exists($headerPath)) {
            require_once $headerPath; // Baris ini memuat header
        } else {
            error_log("Peringatan BaseController: File header '{$headerPath}' tidak ditemukan.");
            echo "<!DOCTYPE html><html lang=\"id\"><head><meta charset=\"UTF-8\"><title>" . htmlspecialchars($pageTitle ?? $appConfig['APP_NAME'] ?? 'Aplikasi') . "</title></head><body>"; // Fallback
        }

        if (file_exists($viewPath)) {
            require_once $viewPath;   // Baris ini memuat view utama (misal: views/kos/daftar.php)
        } else {
            error_log("Peringatan BaseController: File view utama '{$viewPath}' tidak ditemukan.");
            echo "<div style='color:red; padding:10px; border:1px solid red;'>Error: View '{$viewName}' tidak ditemukan.</div>";
        }

        if (file_exists($footerPath)) {
            require_once $footerPath; // Baris ini memuat footer (ini yang sekitar baris 48 berdasarkan error Anda)
        } else {
            error_log("Peringatan BaseController: File footer '{$footerPath}' tidak ditemukan.");
            echo "</body></html>"; // Fallback
        }
    }

    protected function redirect(string $path): void {
        $url = $path;
        if (!(strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0)) {
            $url = rtrim($this->appConfig['BASE_URL'], '/') . '/' . ltrim($path, '/');
        }
        header("Location: " . $url);
        exit;
    }

    protected function setFlashMessage(string $message, string $type = 'info'): void {
        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
        }
    }

}
?>