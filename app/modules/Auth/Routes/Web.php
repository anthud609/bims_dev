<?php
use Core\Middleware\AuthMiddleware;
use App\Modules\Auth\Controllers\AuthController;

return [
    // GET / => AuthController::home
    ['GET', '/', [AuthController::class, 'home']],

    // Or if you prefer an inline closure for /:
    // ['GET', '/', function() {
    //     if (!\Core\Auth::check()) {
    //         header('Location: /login');
    //         exit;
    //     }
    //     (new AuthController())->home();
    // }],

    // POST /logout => AuthController::logout with AuthMiddleware
    ['POST', '/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]],

    // GET /login => AuthController::loginForm
    ['GET', '/login', [AuthController::class, 'loginForm']],

    // POST /login => AuthController::login
    ['POST', '/login', [AuthController::class, 'login']],
];
