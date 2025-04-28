<?php
// core/Controller.php
namespace Core;

class Controller
{
    // Common methods for all controllers can go here

    protected function view(string $path, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../app/modules/' . $path . '.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
