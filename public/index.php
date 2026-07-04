<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek apakah aplikasi sedang dalam mode pemeliharaan (maintenance)...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Registrasi Composer Autoloader...
require __DIR__.'/../vendor/autoload.php';

// Memuat Bootstrap Laravel dan menjalankan aplikasi...
$app = require_once __DIR__.'/../bootstrap/app.php';

if ($app instanceof Application) {
    $app->handleRequest(Request::capture());
} else {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    )->send();

    $kernel->terminate($request, $response);
}
