<?php

namespace App\Core;

/**
 * Router minimalista per catturare e dispatchare HTTP Requests.
 * (OPcache invalidated for REST verbs)
 *
 * @method void setNotFoundHandler(callable $handler)
 */
class Router
{
    private array $routes = [];
    private $notFoundHandler = null;

    /**
     * Setta un handler custom per le route non trovate (Fallbacks)
     */
    public function setNotFoundHandler(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * Aggiunge una route GET
     */
    public function get(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->add('GET', $path, $handler, $middlewares);
    }

    /**
     * Aggiunge una route POST
     */
    public function post(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->add('POST', $path, $handler, $middlewares);
    }

    /**
     * Aggiunge una route PUT
     */
    public function put(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->add('PUT', $path, $handler, $middlewares);
    }

    /**
     * Aggiunge una route PATCH
     */
    public function patch(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->add('PATCH', $path, $handler, $middlewares);
    }

    /**
     * Aggiunge una route DELETE
     */
    public function delete(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->add('DELETE', $path, $handler, $middlewares);
    }

    private function add(string $method, string $path, array|callable $handler, array $middlewares): self
    {
        // Converte "{id}" in espressione regolare
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $regex = "#^{$regex}$#";

        $this->routes[] = compact('method', 'path', 'regex', 'handler', 'middlewares');
        return $this;
    }

    /**
     * Risolve e lancia la route appropriata
     */
    public function dispatch(string $requestMethod, string $requestUri)
    {
        // ==========================================================
        // DYNAMIC MODULES AUTO-DISCOVERY (v0.6)
        // ==========================================================
        $modulesPath = dirname(__DIR__) . '/Modules';
        if (is_dir($modulesPath)) {
            $moduleRoutes = glob($modulesPath . '/*/routes.php');
            if ($moduleRoutes) {
                $router = $this; // Alias proxy per l'inclusione
                foreach ($moduleRoutes as $routeFile) {
                    require_once $routeFile;
                }
            }
        }

        $uri = parse_url($requestUri, PHP_URL_PATH);

        // Rimuovi prefissi come il /public/ basepath in ambiente dev (xampp)
        $baseFolder = trim(Config::get('app.base_folder', '/'), '/');
        if (!empty($baseFolder) && str_starts_with(trim($uri, '/'), $baseFolder)) {
            $uri = substr($uri, strlen('/' . $baseFolder));
        }
        $uri = empty($uri) ? '/' : $uri;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                // Filtriamo solo i parametri associativi
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Esegui Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    if ($middleware instanceof \Closure) {
                        $mw = $middleware();
                    } else {
                        $mw = new $middleware();
                    }
                    $result = $mw->handle();
                    if ($result === false) {
                        return; // O gestisci in altro modo la risposta
                    }
                }

                // Esegue l'handler
                if (is_callable($route['handler'])) {
                    return call_user_func_array($route['handler'], array_values($params));
                }

                if (is_array($route['handler']) && count($route['handler']) === 2) {
                    [$class, $method] = $route['handler'];
                    $controller = new $class();
                    return call_user_func_array([$controller, $method], array_values($params));
                }
            }
        }

        // Se non troviamo route 404
        $this->sendNotFound();
    }

    private function sendNotFound(): void
    {
        if ($this->notFoundHandler) {
            call_user_func($this->notFoundHandler);
        } else {
            http_response_code(404);
            echo "404 - Not Found";
        }
    }
}
