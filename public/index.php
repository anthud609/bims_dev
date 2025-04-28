<?php
require __DIR__ . '/../vendor/autoload.php';

$routes = require __DIR__ . '/../app/modules/Auth/Routes/Web.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

foreach ($routes as [$routeMethod, $routePath, $handler]) {
    if ($method === $routeMethod && $path === $routePath) {
        [$controllerClass, $methodName] = $handler;
        (new $controllerClass())->$methodName();
        exit;
    }
}

http_response_code(404);
echo "404 Not Found";
