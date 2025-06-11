<?php

// Set up error reporting and timezone
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');

// Define a function to dynamically determine the base URL.
if (!function_exists('get_dynamic_base_url')) {
    function get_dynamic_base_url(): string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDirPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = ($scriptDirPath === '/' || $scriptDirPath === '\\') ? '/' : rtrim($scriptDirPath, '/\\') . '/';
        return $protocol . $host . $basePath;
    }
}

// Load the main application configuration.
$appConfigPath = __DIR__ . '/config/app.php';
if (!file_exists($appConfigPath)) {
    die("File konfigurasi utama (app.php) tidak ditemukan di: " . htmlspecialchars($appConfigPath));
}
$appConfig = require_once $appConfigPath;

// Load database configuration.
$databaseConfigPath = $appConfig['CONFIG_PATH'] . '/database.php';
if (!file_exists($databaseConfigPath)) {
    $errorMessage = "File konfigurasi database (database.php) tidak ditemukan.";
    if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
        die($errorMessage . " Path yang diharapkan: " . htmlspecialchars($databaseConfigPath));
    } else {
        http_response_code(503);
        die("Sistem sedang mengalami masalah konfigurasi. Silakan coba lagi nanti.");
    }
}
require_once $databaseConfigPath;

// Session setup: Ensure session is started before any output.
if (session_status() == PHP_SESSION_NONE) {
    session_name($appConfig['SESSION_NAME'] ?? 'PHPSESSID_APP');
    session_set_cookie_params([
        'lifetime' => $appConfig['SESSION_LIFETIME'] ?? 0,
        'path' => '/',
        'domain' => '',
        'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// --- FIX FOR BaseController Autoloading ---
// Explicitly require the BaseController class BEFORE autoloading or other controllers.
// This ensures it's always loaded and available for any controller that extends it.
$baseControllerPath = $appConfig['CONTROLLERS_PATH'] . 'BaseController.php';
if (!file_exists($baseControllerPath)) {
    die("Error: BaseController.php tidak ditemukan di jalur yang diharapkan: " . htmlspecialchars($baseControllerPath));
}
require_once $baseControllerPath;
// --- END FIX ---


// Autoloading classes (Controllers, Models).
spl_autoload_register(function ($className) use ($appConfig) {
    $pathsToTry = [
        // No need to try BaseController.php here anymore if it's explicitly required above
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


// Routing logic to determine controller, action, and parameters.
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

// Determine controller, action, and parameters based on segments.
$controllerName = !empty($segments[0]) ? ucfirst(strtolower(filter_var($segments[0], FILTER_SANITIZE_URL))) . 'Controller' : $appConfig['DEFAULT_CONTROLLER'];
$actionName     = !empty($segments[1]) ? strtolower(filter_var($segments[1], FILTER_SANITIZE_URL)) : $appConfig['DEFAULT_ACTION'];
$params         = array_slice($segments, 2);


// Dispatch the request to the appropriate controller and action.
// The $controllerFile variable is technically not needed here if class_exists and autoloader work,
// but it can be useful for debugging specific "file not found" scenarios for controllers.
// $controllerFile = $appConfig['CONTROLLERS_PATH'] . $controllerName . '.php';


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

    // Provide more specific error if controller file exists but class isn't found within it
    $controllerFileExists = file_exists(($appConfig['CONTROLLERS_PATH'] ?? 'controllers/') . $controllerName . '.php');
    if ($controllerFileExists) {
        $errorMessage = "Error 404: Class '{$controllerName}' tidak ditemukan di dalam file '" . htmlspecialchars(($appConfig['CONTROLLERS_PATH'] ?? 'controllers/') . $controllerName . '.php') . "'. Pastikan nama class dan nama file sudah benar (termasuk kapitalisasi).";
    } else {
        $errorMessage = "Error 404: File controller '" . htmlspecialchars(($appConfig['CONTROLLERS_PATH'] ?? 'controllers/') . $controllerName . '.php') . "' tidak ditemukan.";
    }

    if (($appConfig['APP_ENV'] ?? 'production') === 'development') {
        // Detailed error message already constructed above
    }
    echo $errorMessage;
}