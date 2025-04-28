<?php
// core/Middleware/AuthMiddleware.php
namespace Core\Middleware;

use Core\Auth;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
    }
}
