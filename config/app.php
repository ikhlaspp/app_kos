<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Jakarta');

if (!function_exists('get_dynamic_base_url')) {
    function get_dynamic_base_url() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDirPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');

        if ($scriptDirPath === '/' || $scriptDirPath === '\\') {
            $basePath = '/';
        } else {
             $basePath = rtrim($scriptDirPath, '/\\') . '/';
        }
        return $protocol . $host . $basePath;
    }
}

$appConfig = [
    'APP_NAME'          => 'Sistem Booking Kos Vina',
    'APP_ENV'           => 'development', 
    'BASE_URL'          => get_dynamic_base_url(),

   'ROOT_PATH'         => dirname(__DIR__), 
    'CONFIG_PATH'       => __DIR__,          
    'CONTROLLERS_PATH'  => dirname(__DIR__) . '/controllers/',
    'MODELS_PATH'       => dirname(__DIR__) . '/models/',
    'VIEWS_PATH'        => dirname(__DIR__) . '/views/',
    'INCLUDES_PATH'     => dirname(__DIR__) . '/includes/',
    'LIBS_PATH'         => dirname(__DIR__) . '/libs/',

    'UPLOADS_FS_PATH'   => dirname(__DIR__) . '/uploads/', 

    'DB_CONNECTION' => 'mysql',
    'DB_HOST'       => 'localhost',
    'DB_PORT'       => '3306',
    'DB_DATABASE'   => 'kamar_kos', 
    'DB_USERNAME'   => 'root',
    'DB_PASSWORD'   => '',
    'DB_CHARSET'    => 'utf8mb4',

    'DEFAULT_CONTROLLER' => 'HomeController',
    'DEFAULT_ACTION'     => 'index',

    'SESSION_NAME'      => 'KOS_BOOKING_SESSION_V2', 
    'SESSION_LIFETIME'  => 3600, 
];

$appConfig['ASSETS_URL'] = rtrim($appConfig['BASE_URL'], '/') . '/assets/';
$appConfig['UPLOADS_URL_PATH'] = rtrim($appConfig['BASE_URL'], '/') . '/uploads/';

if (session_status() == PHP_SESSION_NONE) {
    session_name($appConfig['SESSION_NAME']);
    session_start();
}

return $appConfig;
?>