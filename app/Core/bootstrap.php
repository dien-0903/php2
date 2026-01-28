<?php

define('BASE_PATH', dirname(__DIR__, 2));            
define('APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'app');
define('CORE_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Core');
define('VIEW_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'views');
define('STORAGE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'storage');

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME']; 

$baseDir = str_replace(['/public/index.php', '/index.php', '/public'], '', $scriptName);
$baseDir = rtrim($baseDir, '/');

define('BASE_URL', $protocol . "://" . $host . $baseDir);

$autoloadPath = BASE_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

if (class_exists('Dotenv\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->safeLoad();
}
require_once CORE_PATH . '/View.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Model.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Controller.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Router.php';

spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});


$cachePath = STORAGE_PATH . DIRECTORY_SEPARATOR . 'cache';
if (!file_exists($cachePath)) {
    mkdir($cachePath, 0777, true);
}