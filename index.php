<?php

if (!file_exists(__DIR__ . '/config/app.php')) {
    die("File konfigurasi utama (app.php) tidak ditemukan.");
}
$appConfig = require_once __DIR__ . '/config/app.php';

if (!file_exists($appConfig['CONFIG_PATH'] . '/database.php')) {
    // Jika APP_ENV adalah development, tampilkan error lebih detail.
    // Jika production, tampilkan pesan umum atau redirect ke halaman error.
    $errorMessage = "File konfigurasi database (database.php) tidak ditemukan.";
    if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
        die($errorMessage . " Path yang diharapkan: " . htmlspecialchars($appConfig['CONFIG_PATH'] . '/database.php'));
    } else {
        // Di produksi, jangan tampilkan path. Bisa redirect ke halaman error kustom.
        http_response_code(503); // Service Unavailable
        die("Sistem sedang mengalami masalah konfigurasi. Silakan coba lagi nanti.");
    }
}
require_once $appConfig['CONFIG_PATH'] . '/database.php'; // $pdo sekarang tersedia

//    Autoloader Sederhana untuk Controllers dan Models
//    Otomatis memuat file class saat class tersebut pertama kali dibutuhkan.
spl_autoload_register(function ($className) use ($appConfig) {
    $pathsToTry = [
        // Path untuk Controllers
        ($appConfig['CONTROLLERS_PATH'] ?? 'controllers/') . $className . '.php',
        // Path untuk Models
        ($appConfig['MODELS_PATH'] ?? 'models/') . $className . '.php',
        // Anda bisa menambahkan path lain di sini jika perlu, misalnya untuk /libs
        // ($appConfig['LIBS_PATH'] ?? 'libs/') . $className . '.php',
    ];

    foreach ($pathsToTry as $file) {
        // Ganti slash agar konsisten dengan sistem operasi (opsional, PHP biasanya handle)
        $normalizedFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);
        if (file_exists($normalizedFile)) {
            require_once $normalizedFile;
            return; // Hentikan pencarian setelah file ditemukan dan dimuat
        }
    }
    // Jika class tidak ditemukan setelah mencoba semua path, PHP akan melempar error "Class not found"
    // error_log("Autoloader: Class {$className} tidak ditemukan di path yang terdaftar.");
});

//    Logika Routing
$routePath = '';

// Prioritaskan mengambil rute dari parameter 'route' (hasil dari .htaccess RewriteRule)
if (isset($_GET['route'])) {
    $routePath = trim(filter_var($_GET['route'], FILTER_SANITIZE_URL), '/');
} else {
    // Fallback jika .htaccess tidak berfungsi atau akses langsung ke index.php
    // Ini mencoba mengambil path dari REQUEST_URI relatif terhadap BASE_URL.
    $requestUriPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $basePathFromConfig = trim(parse_url($appConfig['BASE_URL'], PHP_URL_PATH), '/');
    
    if (!empty($basePathFromConfig) && strpos($requestUriPath, $basePathFromConfig) === 0) {
        $routePath = trim(substr($requestUriPath, strlen($basePathFromConfig)), '/');
    } elseif (empty($basePathFromConfig)) {
        // Jika BASE_URL adalah root domain ('/')
        $routePath = $requestUriPath;
    }

    // Hapus 'index.php' dari path jika ada (misal, jika URL adalah /index.php/controller/action)
    if (strpos($routePath, 'index.php') === 0) {
        $routePath = trim(substr($routePath, strlen('index.php')), '/');
    }
}

// Pecah $routePath menjadi segmen-segmen untuk controller, action, dan parameter
$segments = !empty($routePath) ? explode('/', $routePath) : [];

// Tentukan nama Controller dan Action (method)
// Gunakan nilai default dari $appConfig jika tidak ada segmen di URL.
$controllerName = !empty($segments[0]) ? ucfirst(strtolower(filter_var($segments[0], FILTER_SANITIZE_URL))) . 'Controller' : $appConfig['DEFAULT_CONTROLLER'];
$actionName     = !empty($segments[1]) ? strtolower(filter_var($segments[1], FILTER_SANITIZE_URL)) : $appConfig['DEFAULT_ACTION'];
$params         = array_slice($segments, 2); // Sisa segmen adalah parameter untuk action

//   Dispatcher: Memuat dan Menjalankan Controller & Action
$controllerFile = $appConfig['CONTROLLERS_PATH'] . $controllerName . '.php';

// Cek apakah class controller ada (autoloader akan mencoba memuat filenya jika belum)
if (class_exists($controllerName)) {
    try {
        $controllerInstance = new $controllerName($pdo, $appConfig); // Buat instance controller, lewatkan dependensi

        if (method_exists($controllerInstance, $actionName)) {
            // Panggil action (method) pada controller dengan parameter yang ada
            call_user_func_array([$controllerInstance, $actionName], $params);
        } else {
            // Action (method) tidak ditemukan di dalam controller
            http_response_code(404); // Not Found
            // Anda bisa memuat view error 404 kustom di sini
            // require_once $appConfig['VIEWS_PATH'] . 'errors/404_action.php';
            echo "Error 404: Action '{$actionName}' tidak ditemukan pada controller '{$controllerName}'.";
        }
    } catch (ArgumentCountError $ace) {
        // Tangani error jika jumlah argumen/parameter untuk action tidak sesuai
        http_response_code(400); // Bad Request
        $errorMessage = "Error 400: Jumlah parameter tidak sesuai untuk method '{$actionName}' di controller '{$controllerName}'.";
        if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
            $errorMessage .= "<br><pre>Pesan Detail: " . htmlspecialchars($ace->getMessage()) . "</pre>";
        }
        // error_log("ArgumentCountError: {$controllerName}@{$actionName} - " . $ace->getMessage());
        echo $errorMessage;
    } catch (Throwable $th) { // Throwable menangkap Error dan Exception (PHP 7+)
        // Tangani error umum lainnya yang mungkin terjadi di dalam constructor controller atau action
        http_response_code(500); // Internal Server Error
        $errorMessage = "Error 500: Terjadi kesalahan internal pada aplikasi.";
        if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
            $errorMessage .= "<br>Pesan: <pre>" . htmlspecialchars($th->getMessage()) . "</pre>";
            $errorMessage .= "<br>Trace: <pre>" . htmlspecialchars($th->getTraceAsString()) . "</pre>";
        }
        // error_log("Kesalahan Aplikasi di {$controllerName}@{$actionName}: " . $th->getMessage() . "\nTrace: " . $th->getTraceAsString());
        echo $errorMessage;
    }
} else {
    // Class controller tidak ditemukan (setelah autoloader mencoba)
    http_response_code(404); // Not Found
    $errorMessage = "Error 404: Controller '{$controllerName}' tidak ditemukan.";
    // Beri pesan yang lebih spesifik jika file ada tapi class tidak, atau file tidak ada
    if (file_exists($controllerFile)) {
         $errorMessage = "Error 404: Class '{$controllerName}' tidak ditemukan di dalam file '{$controllerFile}'. Pastikan nama class dan nama file sudah benar (termasuk kapitalisasi).";
    } else {
         $errorMessage = "Error 404: File controller '{$controllerFile}' tidak ditemukan.";
    }
    if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
        // Tidak perlu menambahkan detail lagi karena pesan error sudah cukup jelas
    }
    echo $errorMessage;
}
?>