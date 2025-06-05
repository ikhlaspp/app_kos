<?php

if (!file_exists(__DIR__ . '/config/app.php')) {
    die("File konfigurasi utama (app.php) tidak ditemukan.");
}
$appConfig = require_once __DIR__ . '/config/app.php';

if (!file_exists($appConfig['CONFIG_PATH'] . '/database.php')) {
    $errorMessage = "File konfigurasi database (database.php) tidak ditemukan.";
    if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
        die($errorMessage . " Path yang diharapkan: " . htmlspecialchars($appConfig['CONFIG_PATH'] . '/database.php'));
    } else {
        http_response_code(503); 
        die("Sistem sedang mengalami masalah konfigurasi. Silakan coba lagi nanti.");
    }
}
require_once $appConfig['CONFIG_PATH'] . '/database.php'; 


spl_autoload_register(function ($className) use ($appConfig) {
    $pathsToTry = [
        ($appConfig['CONTROLLERS_PATH'] ?? 'controllers/') . $className . '.php',
        ($appConfig['MODELS_PATH'] ?? 'models/') . $className . '.php',
    ];

    foreach ($pathsToTry as $file) {
        $normalizedFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);
        if (file_exists($normalizedFile)) {
            require_once $normalizedFile;
            return; 
        }
    }
});

$routePath = '';

if (isset($_GET['route'])) {
    $routePath = trim(filter_var($_GET['route'], FILTER_SANITIZE_URL), '/');
} else {
    $requestUriPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $basePathFromConfig = trim(parse_url($appConfig['BASE_URL'], PHP_URL_PATH), '/');
    
    if (!empty($basePathFromConfig) && strpos($requestUriPath, $basePathFromConfig) === 0) {
        $routePath = trim(substr($requestUriPath, strlen($basePathFromConfig)), '/');
    } elseif (empty($basePathFromConfig)) {
        $routePath = $requestUriPath;
    }
    if (strpos($routePath, 'index.php') === 0) {
        $routePath = trim(substr($routePath, strlen('index.php')), '/');
    }
}

$segments = !empty($routePath) ? explode('/', $routePath) : [];
$controllerName = !empty($segments[0]) ? ucfirst(strtolower(filter_var($segments[0], FILTER_SANITIZE_URL))) . 'Controller' : $appConfig['DEFAULT_CONTROLLER'];
$actionName     = !empty($segments[1]) ? strtolower(filter_var($segments[1], FILTER_SANITIZE_URL)) : $appConfig['DEFAULT_ACTION'];
$params         = array_slice($segments, 2); 
$controllerFile = $appConfig['CONTROLLERS_PATH'] . $controllerName . '.php';

if (class_exists($controllerName)) {
    try {
        $controllerInstance = new $controllerName($pdo, $appConfig); 

        if (method_exists($controllerInstance, $actionName)) {
            call_user_func_array([$controllerInstance, $actionName], $params);
        } else {
            http_response_code(404); 
            echo "Error 404: Action '{$actionName}' tidak ditemukan pada controller '{$controllerName}'.";
        }
    } catch (ArgumentCountError $ace) {
        http_response_code(400); 
        $errorMessage = "Error 400: Jumlah parameter tidak sesuai untuk method '{$actionName}' di controller '{$controllerName}'.";
        if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
            $errorMessage .= "<br><pre>Pesan Detail: " . htmlspecialchars($ace->getMessage()) . "</pre>";
        }
        echo $errorMessage;
    } catch (Throwable $th) { 
        http_response_code(500); 
        $errorMessage = "Error 500: Terjadi kesalahan internal pada aplikasi.";
        if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
            $errorMessage .= "<br>Pesan: <pre>" . htmlspecialchars($th->getMessage()) . "</pre>";
            $errorMessage .= "<br>Trace: <pre>" . htmlspecialchars($th->getTraceAsString()) . "</pre>";
        }
        echo $errorMessage;
    }
} else {
    http_response_code(404); 
    $errorMessage = "Error 404: Controller '{$controllerName}' tidak ditemukan.";
    if (file_exists($controllerFile)) {
         $errorMessage = "Error 404: Class '{$controllerName}' tidak ditemukan di dalam file '{$controllerFile}'. Pastikan nama class dan nama file sudah benar (termasuk kapitalisasi).";
    } else {
         $errorMessage = "Error 404: File controller '{$controllerFile}' tidak ditemukan.";
    }
    if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
    }
    echo $errorMessage;
}
?>