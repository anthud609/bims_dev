<?php
// core/Router.php
namespace Core;

class Router
{
    protected array $routes = [];

    /**
     * @param string $method   HTTP method (GET, POST, etc.)
     * @param string $path     Request URI path
     * @param callable|array $handler  Controller method [Class, 'method'] or callable
     * @param array $middleware Array of middleware: class names, instances, or callables
     */
    public function add(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method'     => strtoupper($method),
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch the request, run middleware, and invoke handler
     */
    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        foreach ($this->routes as $route) {
            if ($method === $route['method'] && $path === $route['path']) {

                // Run middleware in order
                foreach ($route['middleware'] as $mw) {
                    if (is_string($mw) && class_exists($mw)) {
                        (new $mw())->handle();
                    } elseif (is_object($mw) && method_exists($mw, 'handle')) {
                        $mw->handle();
                    } elseif (is_callable($mw)) {
                        call_user_func($mw);
                    }
                }

                // Invoke handler
                if (is_array($route['handler'])) {
                    [$controllerClass, $methodName] = $route['handler'];
                    (new $controllerClass())->$methodName();
                } else {
                    call_user_func($route['handler']);
                }

                return;
            }
        }

        // No matching route
        http_response_code(404);
        echo "404 Not Found";
    }
}
