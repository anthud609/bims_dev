<?php
use Core\Middleware\AuthMiddleware;
use App\Modules\Auth\Controllers\AuthController;

return [
    ['GET', '/', [AuthController::class, 'home']],

    ['GET', '/', function() {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        (new AuthController())->home();
    }],
    $router->add('POST', '/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]),
    $router->add('GET', '/login', [AuthController::class, 'loginForm']),
    $router->add('POST', '/login', [AuthController::class, 'login']),
];
