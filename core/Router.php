<?php
// core/Router.php
namespace Core;

class Router
{
    protected array $routes = [];

    public function add(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($method === $route['method'] && $path === $route['path']) {
                
                // Run middleware first
                foreach ($route['middleware'] as $middleware) {
                    (new $middleware())->handle();
                }

                [$controllerClass, $methodName] = $route['handler'];
                (new $controllerClass())->$methodName();
                return;
            }
        }

        // No route matched
        http_response_code(404);
        echo "404 Not Found";
    }
}
