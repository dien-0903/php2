<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/app/Core/bootstrap.php';

try {
    $router = new Router();
    $router->dispatch($_SERVER["REQUEST_URI"]);
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Lỗi hệ thống</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}