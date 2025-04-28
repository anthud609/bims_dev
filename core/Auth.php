<?php
// core/Auth.php
namespace Core;

class Auth
{
    public static function check(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public static function userId(): ?int
    {
        session_start();
        return $_SESSION['user_id'] ?? null;
    }

    public static function logout(): void
    {
        session_start();
        session_destroy();
    }
}
