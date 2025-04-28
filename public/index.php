<?php
require __DIR__ . '/../core/Bootstrap.php';

use Core\Router;
use App\Modules\Auth\Controllers\AuthController;
use Core\Middleware\AuthMiddleware;
use Core\Middleware\ThrottleMiddleware;

$router = new Router();


// global: 100 req/min
$globalThrottle = new ThrottleMiddleware(100, 60);
$router->add('GET','.*', fn()=>null, [ $globalThrottle ]);


// login only: 10 attempts per 10 min
$loginThrottle = new ThrottleMiddleware(10, 600);
$router->add('POST','/login', [AuthController::class,'login'], [ $loginThrottle ]);

$router->add('GET', '/', [AuthController::class, 'home'], [AuthMiddleware::class]);
$router->add('GET', '/login', [AuthController::class, 'loginForm']);
$router->add('POST', '/login', [AuthController::class, 'login']);
$router->add('GET', '/register', [AuthController::class, 'registerForm']);
$router->add('POST', '/register', [AuthController::class, 'register']);
$router->add('POST', '/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
