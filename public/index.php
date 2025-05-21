<?php
require_once 'config/routes.php';
require_once 'controller/HomeController.php';
require_once 'controller/UserController.php';
require_once 'controller/RegisterController.php';

class Router {
    private $routes;

    public function __construct($routes) {
        $this->routes = $routes;
    }

    public function route() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');

        foreach ($this->routes as $route => $action) {
            if ($route === $uri) {
                try {
                    list($controller, $method) = explode('@', $action);
                    if (!class_exists($controller)) {
                        throw new Exception("Controller $controller not found");
                    }
                    $controllerInstance = new $controller();
                    if (!method_exists($controllerInstance, $method)) {
                        throw new Exception("Method $method not found in $controller");
                    }
                    $controllerInstance->$method();
                    return;
                } catch (Exception $e) {
                    error_log("Routing error: " . $e->getMessage());
                    http_response_code(500);
                    echo "Internal Server Error";
                    return;
                }
            }
        }

        // Handle 404
        http_response_code(404);
        require 'view/404.php';
    }
}

$router = new Router($routes);
$router->route();