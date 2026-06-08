<?php
// ============================================================
// Router
// Web-Based Crop Insurance System
// Simple HTTP router for clean RESTful routing
// ============================================================

class Router {
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }

    // ---- Route registration ----

    public function get(string $path, callable|array $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, callable|array $handler): void {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $this->basePath . $path,
            'handler' => $handler,
        ];
    }

    // ---- Dispatch ----

    public function dispatch(): void {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            $params = [];
            if ($route['method'] !== $method) continue;
            if (!$this->matchPath($route['path'], $uri, $params)) continue;

            $this->callHandler($route['handler'], $params);
            return;
        }

        sendNotFound('Route not found.');
    }

    // ---- Path matching (supports {param} placeholders) ----

    private function matchPath(string $routePath, string $uri, array &$params): bool {
        $routePath = '/' . trim($routePath, '/');
        $pattern   = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern   = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) return false;

        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        return true;
    }

    // ---- Call handler (closure or [Controller::class, 'method']) ----

    private function callHandler(callable|array $handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        [$class, $method] = $handler;

        if (!class_exists($class)) {
            sendServerError("Controller class '$class' not found.");
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            sendServerError("Method '$method' not found in '$class'.");
        }

        $controller->$method($params);
    }
}
