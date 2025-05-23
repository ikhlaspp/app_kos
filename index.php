<?php

$appConfig = require_once __DIR__ . '/config/app.php';
require_once $appConfig['CONFIG_PATH'] . '/database.php';

// Autoloader Sederhana untuk Controllers dan Models
spl_autoload_register(function ($className) use ($appConfig) {
    $pathsToTry = [
        $appConfig['CONTROLLERS_PATH'] . $className . '.php',
        $appConfig['MODELS_PATH'] . $className . '.php',
        // Anda bisa menambahkan path lain di sini jika perlu, misal untuk /libs
    ];

    foreach ($pathsToTry as $file) {
        if (file_exists($file)) {
            require_once $file;
            return; // Hentikan pencarian setelah file ditemukan dan dimuat
        }
    }
});

// Logika Routing
$routePath = '';

// Prioritaskan mengambil rute dari parameter 'route' (hasil dari .htaccess)
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
$controllerName = !empty($segments[0]) ? ucfirst(strtolower($segments[0])) . 'Controller' : $appConfig['DEFAULT_CONTROLLER'];
$actionName     = !empty($segments[1]) ? strtolower($segments[1]) : $appConfig['DEFAULT_ACTION'];
$params         = array_slice($segments, 2); // Sisa segmen adalah parameter untuk action

// 4. Dispatcher: Memuat dan Menjalankan Controller & Action
$controllerFile = $appConfig['CONTROLLERS_PATH'] . $controllerName . '.php';

// Cek apakah class controller ada (autoloader akan mencoba memuat filenya)
if (class_exists($controllerName)) {
    $controllerInstance = new $controllerName($pdo, $appConfig); // Buat instance controller

    if (method_exists($controllerInstance, $actionName)) {
        try {
            // Panggil action (method) pada controller dengan parameter
            call_user_func_array([$controllerInstance, $actionName], $params);
        } catch (ArgumentCountError $ace) {
            // Tangani error jika jumlah argumen/parameter untuk action tidak sesuai
            http_response_code(400); // Bad Request
            echo "Error 400: Jumlah parameter tidak sesuai untuk method '{$actionName}' di controller '{$controllerName}'.";
            if ($appConfig['APP_ENV'] === 'development') {
                echo "<br><pre>Pesan Error: " . htmlspecialchars($ace->getMessage()) . "</pre>";
            }
        } catch (Throwable $th) {
            // Tangani error umum lainnya yang mungkin terjadi di dalam controller atau action
            http_response_code(500); // Internal Server Error
            echo "Error 500: Terjadi kesalahan internal pada aplikasi.";
            if ($appConfig['APP_ENV'] === 'development') {
                echo "<br>Pesan: <pre>" . htmlspecialchars($th->getMessage()) . "</pre>";
                echo "<br>Trace: <pre>" . htmlspecialchars($th->getTraceAsString()) . "</pre>";
            }
            // error_log("Kesalahan Aplikasi di {$controllerName}@{$actionName}: " . $th->getMessage());
        }
    } else {
        // Action (method) tidak ditemukan di dalam controller
        http_response_code(404); // Not Found
        echo "Error 404: Action '{$actionName}' tidak ditemukan pada controller '{$controllerName}'.";
    }
} else {
    // Class controller tidak ditemukan (setelah autoloader mencoba)
    http_response_code(404); // Not Found
    // Beri pesan yang lebih spesifik jika file ada tapi class tidak
    if (file_exists($controllerFile)) {
         echo "Error 404: Class '{$controllerName}' tidak ditemukan di dalam file '{$controllerFile}'. Pastikan nama class dan nama file sudah benar (termasuk kapitalisasi).";
    } else {
         echo "Error 404: File controller '{$controllerFile}' tidak ditemukan.";
    }
}
?>