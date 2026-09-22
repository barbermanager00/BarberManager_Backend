<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, callable|array $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function resolve(string $requestUri, string $method): void
    {
        $proyecto_path = '/Barber_Manager';
        
        $path = str_replace($proyecto_path, '', $requestUri);
        $path = parse_url($path, PHP_URL_PATH) ?? '/';
        $path = str_replace('/public/', '/', $path);
        
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            echo json_encode(["error" => "Ruta no encontrada"]);
            exit;
        }

        if (is_callable($action)) {
            call_user_func($action);
        } elseif (is_array($action)) {
            [$class, $methodName] = $action;
            if (class_exists($class) && method_exists($class, $methodName)) {
                call_user_func([$class, $methodName]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error interno del servidor (Controlador o metodo no encontrado)"]);
            }
        }
    }
}