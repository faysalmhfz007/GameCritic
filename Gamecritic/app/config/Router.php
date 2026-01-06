<?php
class Router {
    private $routes = [];

    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($path, $controller, $action) {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post($path, $controller, $action) {
        $this->addRoute('POST', $path, $controller, $action);
    }

    public function dispatch($requestMethod, $requestUri) {
        // Remove query string from URI
        $uri = parse_url($requestUri, PHP_URL_PATH);

        // Normalize URI
        if ($uri === false || $uri === null) {
            $uri = '/';
        }
        if ($uri === '') {
            $uri = '/';
        }
        if ($uri[0] !== '/') {
            $uri = '/' . ltrim($uri, '/');
        }

        // Handle Gamecritic subfolder
        if (strpos($uri, '/Gamecritic') === 0) {
            $uri = substr($uri, strlen('/Gamecritic'));
        }
        
        // Handle public subfolder
        if (strpos($uri, '/public') === 0) {
            $uri = substr($uri, strlen('/public'));
        }
        
        // Debug: Show what URI we're processing
        error_log("Router: Original URI: " . $_SERVER['REQUEST_URI']);
        error_log("Router: Processed URI: " . $uri);

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        // Treat index.php as root
        if ($uri === '/index.php' || $uri === '/public/index.php') {
            $uri = '/';
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $uri)) {
                $params = $this->extractParams($route['path'], $uri);
                return $this->executeController($route['controller'], $route['action'], $params);
            }
        }
        
        // Debug: Show what URI we're looking for
        echo "404 - Page Not Found<br>";
        echo "Looking for: {$requestMethod} {$uri}<br>";
        echo "Available routes:<br>";
        foreach ($this->routes as $route) {
            echo "- {$route['method']} {$route['path']}<br>";
        }
        http_response_code(404);
    }

    private function matchPath($routePath, $requestPath) {
        // Convert route parameters to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }

    private function extractParams($routePath, $requestPath) {
        $params = [];
        
        // Extract parameter names from route
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        // Convert route to regex and extract values
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        preg_match($pattern, $requestPath, $paramValues);
        
        // Combine names and values
        for ($i = 0; $i < count($paramNames[1]); $i++) {
            $params[$paramNames[1][$i]] = $paramValues[$i + 1];
        }
        
        return $params;
    }

    private function executeController($controllerName, $actionName, $params = []) {
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}Controller.php";
        
        // Debug: Show what file we're looking for
        error_log("Router: Looking for controller file: {$controllerFile}");
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: {$controllerFile}");
        }
        
        error_log("Router: Controller file found, requiring it...");
        require_once $controllerFile;
        
        $controllerClass = $controllerName . 'Controller';
        error_log("Router: Creating controller instance: {$controllerClass}");
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $actionName)) {
            throw new Exception("Action not found: {$actionName} in {$controllerClass}");
        }
        
        error_log("Router: Executing action: {$actionName}");
        return call_user_func_array([$controller, $actionName], $params);
    }
}
?>


