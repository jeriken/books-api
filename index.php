<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Fast-path /ping before Laravel boots (avoids cold-start latency)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    $path = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
    if (rtrim($path, '/') === '/ping' || $path === '/ping') {
        header('Content-Type: application/json');
        echo '{"success":true}';
        exit;
    }
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
