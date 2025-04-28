<?php
require __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use App\Modules\Auth\Controllers\AuthController;
use Core\Middleware\AuthMiddleware;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup database connection
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'bims',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Set the Capsule instance globally
$capsule->setAsGlobal();
$capsule->bootEloquent();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();

// Load routes (normally from your module route files)
$router->add('GET', '/', [AuthController::class, 'home'], [AuthMiddleware::class]);
$router->add('GET', '/login', [AuthController::class, 'loginForm']);
$router->add('POST', '/login', [AuthController::class, 'login']);
$router->add('GET', '/register', [AuthController::class, 'registerForm']);
$router->add('POST', '/register', [AuthController::class, 'register']);
$router->add('POST', '/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

// Dispatch request
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
