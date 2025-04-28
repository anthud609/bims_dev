<?php
// core/Controller.php
namespace Core;

class Controller
{
    // Common methods for all controllers can go here

    /**
     * Renders a module‐view wrapped in the global layout.
     *
     * @param  string  $path  e.g. 'Auth/Views/login'
     * @param  array   $data  an associative array of vars for the view
     */
    protected function view(string $path, array $data = [], string $layout = 'default'): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../app/modules/' . $path . '.php';

        if ($layout === 'auth') {
            require __DIR__ . '/../app/Views/AuthLayout.php';
        } else {
            require __DIR__ . '/../app/Views/Layout.php';
        }
    }


    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
