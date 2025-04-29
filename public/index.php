<?php
require __DIR__ . '/../core/Bootstrap.php';

use Core\Router;

// Create a new router instance
$router = new Router();

// 1) Only define two static routes manually:
//    - GET /
//    - GET /dashboard
$logger = Core\LoggingServiceProvider::getLogger();

// // DEBUG: very low‐level (dev only)
// $logger->debug('Index loaded!', ['uid' => uniqid()]);

// // INFO: high‐level business event
// $logger->info('User profile viewed');

// // NOTICE: normal but significant
// $cacheKey = 'home_page';
// $logger->notice('Cache miss, regenerating', ['cache_key' => $cacheKey]);

// // WARNING: recoverable problem
// $limit   = 100;
// $window  = 60;
// $logger->warning('Rate limit nearing', ['limit' => $limit, 'window' => $window]);

// // ERROR: log inside a catch so $e is defined
// try {
//     // simulate something that throws
//     throw new \RuntimeException('Simulated payment failure');
// } catch (\Throwable $e) {
//     $logger->error('Payment processing failed', [
//         'order_id'  => 42,
//         'exception' => $e,           // Monolog will stringify this
//     ]);
// }

// // CRITICAL: component outage
// $dbHost = $_ENV['DB_HOST'] ?? 'unknown';
// $logger->critical('Database connection lost', ['host' => $dbHost]);

// // ALERT: immediate action required
// $clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
// $logger->alert('Security breach detected', ['ip' => $clientIp]);

// // EMERGENCY: system unusable
// $logger->emergency('Out of memory, shutting down!');


// 2) Automatically load and merge each module's Web routes
$modulesDir     = __DIR__ . '/../app/modules';
$webRouteFiles  = glob($modulesDir . '/*/Routes/Web.php');

foreach ($webRouteFiles as $webFile) {
    // Each file returns an array of route definitions
    // e.g. [ ['GET','/auth/login',[AuthController::class,'login']], ... ]
    $routes = require $webFile;

    foreach ($routes as $r) {
        // Basic shape: ['METHOD', '/uri', callableOrArrayHandler, (optional) middlewareArray]
        // If your route array has only 3 items, add an empty array for middleware:
        $method     = $r[0];
        $path       = $r[1];
        $handler    = $r[2] ?? null;
        $middleware = $r[3] ?? [];
        
        $router->add($method, $path, $handler, $middleware);
    }
}


// 3) Optionally load and merge each module's API routes
$apiRouteFiles = glob($modulesDir . '/*/Routes/Api.php');

foreach ($apiRouteFiles as $apiFile) {
    $routes = require $apiFile;

    foreach ($routes as $r) {
        $method     = $r[0];
        $path       = $r[1];
        $handler    = $r[2] ?? null;
        $middleware = $r[3] ?? [];

        $router->add($method, $path, $handler, $middleware);
    }
}


// 4) Finally, dispatch
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
