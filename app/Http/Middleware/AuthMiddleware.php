<?php

namespace App\Http\Middleware;

use App\Core\Auth\AuthManager;
use App\Core\Auth\Gate;
use App\Core\Config;

/**
 * Middleware di protezione base per verificare l'autenticazione.
 * Supporta anche un parametro 'permission' opzionale.
 */
class AuthMiddleware
{
    private string $requiredPermission = '';

    public function __construct(string $requiredPermission = '')
    {
        $this->requiredPermission = $requiredPermission;
    }

    public function handle(): bool
    {
        if (!AuthManager::isLoggedIn()) {
            $this->denyAccess('Unauthorized', 401, 'login');
            return false;
        }

        if ($this->requiredPermission !== '') {
            if (!Gate::allows($this->requiredPermission)) {
                $this->denyAccess('Forbidden', 403, 'admin'); // o pagina 403
                return false;
            }
        }

        return true;
    }

    private function denyAccess(string $jsonMsg, int $code, string $redirect): void
    {
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            http_response_code($code);
            echo json_encode(['error' => $jsonMsg]);
            exit;
        }

        $base = Config::get('app.base_folder', '/');
        header("Location: " . rtrim($base, '/') . "/" . $redirect);
        exit;
    }
}
