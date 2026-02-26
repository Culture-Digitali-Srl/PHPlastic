<?php

/**
 * ENTRYPOINT DELL'APPLICAZIONE
 * Questo file intercetta tutte le richieste HTTP.
 */

// 1. Caricamento Autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Security;
use App\Core\Router;

// 2. Lettura configurazione ENV base
Config::loadEnv(__DIR__ . '/../.env');

// Configurazione base via .php se la vogliamo (opzionale)
Config::load('app', [
    'name' => Config::getEnv('APP_NAME', 'PHPlastic'),
    'env' => Config::getEnv('APP_ENV', 'production'),
    // Basepath su xampp: se la cartella è dentro un htdocs 
    'base_folder' => Config::getEnv('APP_BASE_FOLDER', '/PHPlastic/public/'),
    'db_path' => __DIR__ . '/../storage/app.sqlite'
]);

// 3. Environment Display Errors based
if (Config::get('app.env') === 'local') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 4. Sicurezza globale
Security::sendSecurityHeaders();
Security::startSession();

// 5. Instanzia Router e definisci le route
$router = new Router();

// ==========================================================
// ROTTE PUBBLICHE E AUTH
// ==========================================================

$router->get('/', ['App\Http\Controllers\HomeController', 'index']);

// Fallback per rotte inesistenti (generico)
$router->setNotFoundHandler(function () {
    http_response_code(404);
    echo "<div style='font-family:sans-serif; text-align:center; padding: 50px;'>";
    echo "<h1>404 - Pagina Non Trovata</h1>";
    echo "<p>L'URL richiesto non esiste su questo server.</p>";
    echo "<a href='" . Config::get('app.base_folder', '/') . "'>Torna alla Home</a>";
    echo "</div>";
});

$router->get('/login', ['App\Http\Controllers\AuthController', 'showLoginForm']);
$router->post('/login', ['App\Http\Controllers\AuthController', 'login']);
$router->get('/logout', ['App\Http\Controllers\AuthController', 'logout']);

// ==========================================================
// ROTTE PROTETTE (ADMIN)
// ==========================================================

$router->get('/admin', ['App\Http\Controllers\AdminController', 'dashboard'], [
    \App\Http\Middleware\AuthMiddleware::class
]);

$router->get('/admin/users', ['App\Http\Controllers\Admin\UserController', 'index'], [
    function () {
        return new \App\Http\Middleware\AuthMiddleware('manage_users');
    }
]);

$router->get('/admin/audit', ['App\Http\Controllers\Admin\AuditController', 'index'], [
    function () {
        return new \App\Http\Middleware\AuthMiddleware('view_audit');
    }
]);

$router->get('/admin/settings', ['App\Http\Controllers\Admin\SettingsController', 'index'], [
    function () {
        return new \App\Http\Middleware\AuthMiddleware('manage_settings');
    }
]);

$router->post('/admin/settings', ['App\Http\Controllers\Admin\SettingsController', 'store'], [
    function () {
        return new \App\Http\Middleware\AuthMiddleware('manage_settings');
    }
]);

$router->post('/admin/settings/reset', ['App\Http\Controllers\Admin\SettingsController', 'reset'], [
    function () {
        return new \App\Http\Middleware\AuthMiddleware('manage_settings');
    }
]);

$router->get('/admin/settings/models', ['App\Http\Controllers\Admin\SettingsController', 'fetchModels'], [
    function () {
        return new \App\Http\Middleware\AuthMiddleware('manage_settings');
    }
]);

$router->get('/admin/changelog', ['App\Http\Controllers\Admin\ChangelogController', 'index'], [
    function () {
        return new \App\Http\Middleware\AuthMiddleware(); // Visibile a tutti i logged o aggiungi permesso se vuoi
    }
]);

$router->get('/admin/tokens', ['App\Http\Controllers\Admin\TokenDashboardController', 'index'], [
    \App\Http\Middleware\AuthMiddleware::class
]);

$router->get('/admin/wizard', ['App\Http\Controllers\Admin\WizardController', 'index'], [
    \App\Http\Middleware\AuthMiddleware::class
]);

$router->post('/admin/wizard/generate', ['App\Http\Controllers\Admin\AIGenerateController', 'generate'], [
    \App\Http\Middleware\AuthMiddleware::class
]);

$router->post('/admin/wizard/apply', ['App\Http\Controllers\Admin\AIGenerateController', 'apply'], [
    \App\Http\Middleware\AuthMiddleware::class
]);

$router->get('/admin/ai-demo', ['App\Http\Controllers\Admin\AIDemoController', 'runTest'], [
    \App\Http\Middleware\AuthMiddleware::class
]);

// 6. Dispatcher
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($method, $uri);
