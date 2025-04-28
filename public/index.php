<?php
require __DIR__ . '/../core/Bootstrap.php';

use Core\Router;
use App\Modules\Auth\Controllers\AuthController;
use Core\Middleware\AuthMiddleware;

$router = new Router();

$router->add('GET', '/', [AuthController::class, 'home'], [AuthMiddleware::class]);
$router->add('GET', '/login', [AuthController::class, 'loginForm']);
$router->add('POST', '/login', [AuthController::class, 'login']);
$router->add('GET', '/register', [AuthController::class, 'registerForm']);
$router->add('POST', '/register', [AuthController::class, 'register']);
$router->add('POST', '/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
