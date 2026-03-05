<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Fast-path: handle /ping before Laravel boots
$uri    = $_SERVER['REQUEST_URI'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? '';
if ($method === 'GET' && (rtrim(strtok($uri, '?'), '/') === '/ping' || $uri === '/ping')) {
    header('Content-Type: application/json');
    echo '{"success":true}';
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
