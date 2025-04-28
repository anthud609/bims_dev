<?php
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
    ['GET', '/login', [AuthController::class, 'loginForm']],
    ['POST', '/login', [AuthController::class, 'login']],
    ['GET', '/register', [AuthController::class, 'registerForm']],
    ['POST', '/register', [AuthController::class, 'register']],
    ['POST', '/logout', [AuthController::class, 'logout']],
];
