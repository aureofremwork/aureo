<?php
namespace System;

/**
 * Clase Router - Sistema de enrutamiento mejorado de Áureo Framework
 * 
 * Esta clase maneja el enrutamiento de las solicitudes HTTP a los controladores
 * y métodos correspondientes, con soporte para:
 * - Rutas dinámicas con parámetros
 * - Múltiples métodos HTTP
 * - Alias de rutas
 * - Middleware
 */
class Router {
    /**
     * Almacena todas las rutas registradas
     * @var array
     */
    private $routes = [];

    /**
     * Almacena los alias de las rutas
     * @var array
     */
    private $aliases = [];

    /**
     * Almacena los middleware globales
     * @var array
     */
    private $middleware = [];

    /**
     * Registra una ruta GET
     * @param string $path Ruta URL
     * @param string|callable $handler Controlador@método o función callback
     * @param string|null $alias Alias opcional para la ruta
     * @return Router
     */
    public function get($path, $handler, $alias = null) {
        return $this->addRoute('GET', $path, $handler, $alias);
    }

    /**
     * Registra una ruta POST
     * @param string $path Ruta URL
     * @param string|callable $handler Controlador@método o función callback
     * @param string|null $alias Alias opcional para la ruta
     * @return Router
     */
    public function post($path, $handler, $alias = null) {
        return $this->addRoute('POST', $path, $handler, $alias);
    }

    /**
     * Registra una ruta PUT
     * @param string $path Ruta URL
     * @param string|callable $handler Controlador@método o función callback
     * @param string|null $alias Alias opcional para la ruta
     * @return Router
     */
    public function put($path, $handler, $alias = null) {
        return $this->addRoute('PUT', $path, $handler, $alias);
    }

    /**
     * Registra una ruta DELETE
     * @param string $path Ruta URL
     * @param string|callable $handler Controlador@método o función callback
     * @param string|null $alias Alias opcional para la ruta
     * @return Router
     */
    public function delete($path, $handler, $alias = null) {
        return $this->addRoute('DELETE', $path, $handler, $alias);
    }

    /**
     * Añade una ruta al array de rutas
     * @param string $method Método HTTP
     * @param string $path Ruta URL
     * @param string|callable $handler Controlador@método o función callback
     * @param string|null $alias Alias opcional para la ruta
     * @return Router
     */
    private function addRoute($method, $path, $handler, $alias = null) {
        $route = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => []
        ];

        // Convertir los parámetros de la ruta en expresiones regulares
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<\\1>[^/]+)', $path);
        $route['pattern'] = '#^' . $pattern . '$#';

        $this->routes[] = $route;

        // Registrar alias si se proporciona
        if ($alias) {
            $this->aliases[$alias] = $path;
        }

        return $this;
    }

    /**
     * Aplica middleware a la última ruta registrada
     * @param string|array $middleware Nombre del middleware o array de middlewares
     * @return Router
     */
    public function middleware($middleware) {
        $lastRoute = count($this->routes) - 1;
        if ($lastRoute >= 0) {
            $middleware = is_array($middleware) ? $middleware : [$middleware];
            $this->routes[$lastRoute]['middleware'] = array_merge(
                $this->routes[$lastRoute]['middleware'],
                $middleware
            );
        }
        return $this;
    }

    /**
     * Obtiene la URL para un alias de ruta
     * @param string $alias Nombre del alias
     * @param array $params Parámetros para la ruta
     * @return string
     */
    public function getRouteByAlias($alias, $params = []) {
        if (!isset($this->aliases[$alias])) {
            throw new \Exception("Alias de ruta '$alias' no encontrado");
        }

        $path = $this->aliases[$alias];
        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }
        return $path;
    }

    /**
     * Procesa la solicitud actual y ejecuta el controlador correspondiente
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $path, $matches)) {
                // Filtrar los parámetros numéricos de los matches
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                // Ejecutar middleware de la ruta
                foreach ($route['middleware'] as $middleware) {
                    $middlewareClass = "\\App\\Middleware\\" . $middleware;
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle()) {
                            return;
                        }
                    }
                }

                return $this->executeHandler($route['handler'], $params);
            }
        }

        // Si no se encuentra la ruta, mostrar error 404
        header('HTTP/1.1 404 Not Found');
        echo 'Página no encontrada';
    }

    /**
     * Ejecuta el controlador y método especificados
     * @param string|callable $handler Controlador@método o función callback
     * @param array $params Parámetros de la ruta
     */
    private function executeHandler($handler, $params = []) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        list($controller, $method) = explode('@', $handler);
        
        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $method)) {
                return call_user_func_array([$controllerInstance, $method], $params);
            }
        }

        throw new \Exception('Controlador o método no encontrado');
    }
}