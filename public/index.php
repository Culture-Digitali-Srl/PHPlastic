<?php

/**
 * APPLICATION ENTRYPOINT
 * Intercepts all HTTP requests and dispatches them.
 */

// 1. Load Composer Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Security;
use App\Core\Router;

// 2. Load base ENV configuration
Config::loadEnv(__DIR__ . '/../.env');

// Base config via .php (optional)
Config::load('app', [
    'name' => Config::getEnv('APP_NAME', 'PHPlastic'),
    'env' => Config::getEnv('APP_ENV', 'production'),
    'base_folder' => Config::getEnv('APP_BASE_FOLDER', '/PHPlastic/public/'),
    'db_path' => __DIR__ . '/../storage/app.sqlite',
    'version' => 'v0.7.0'
]);

// 3. Environment Display Errors
if (Config::get('app.env') === 'local') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 4. Global Security
Security::sendSecurityHeaders();
Security::startSession();

// 5. Instantiate Router
$router = new Router();

// ==========================================================
// FIRST-RUN DETECTION & SETUP WIZARD
// ==========================================================
$isSetupNeeded = \App\Http\Controllers\SetupController::isSetupNeeded();

if ($isSetupNeeded) {
    // Register ONLY setup routes — block everything else
    $router->get('/setup', ['App\Http\Controllers\SetupController', 'step1']);
    $router->post('/setup/save-type', ['App\Http\Controllers\SetupController', 'saveProjectType']);
    $router->get('/setup/step2', ['App\Http\Controllers\SetupController', 'step2']);
    $router->post('/setup/save-keys', ['App\Http\Controllers\SetupController', 'saveApiKeys']);
    $router->post('/setup/test-key', ['App\Http\Controllers\SetupController', 'testApiKey']);
    $router->get('/setup/step3', ['App\Http\Controllers\SetupController', 'step3']);
    $router->post('/setup/create-admin', ['App\Http\Controllers\SetupController', 'createAdmin']);
    $router->get('/setup/step4', ['App\Http\Controllers\SetupController', 'step4']);
    $router->get('/setup/complete', ['App\Http\Controllers\SetupController', 'complete']);

    // AI generate/apply endpoints (used by Step 4)
    $router->post('/admin/wizard/generate', ['App\Http\Controllers\Admin\AIGenerateController', 'generate']);
    $router->post('/admin/wizard/apply', ['App\Http\Controllers\Admin\AIGenerateController', 'apply']);

    // Redirect everything else to /setup
    $router->setNotFoundHandler(function () {
        header('Location: ' . Config::get('app.base_folder') . 'setup');
        exit;
    });

    // Dispatch and stop here
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    $router->dispatch($method, $uri);
    exit;
}

// ==========================================================
// NORMAL ROUTING (only when setup is complete)
// ==========================================================

$router->get('/', ['App\Http\Controllers\HomeController', 'index']);

// 404 fallback
$router->setNotFoundHandler(function () {
    http_response_code(404);
    echo "<div style='font-family:sans-serif; text-align:center; padding: 50px;'>";
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested URL does not exist on this server.</p>";
    echo "<a href='" . Config::get('app.base_folder', '/') . "'>Back to Home</a>";
    echo "</div>";
});

$router->get('/login', ['App\Http\Controllers\AuthController', 'showLoginForm']);
$router->post('/login', ['App\Http\Controllers\AuthController', 'login']);
$router->get('/logout', ['App\Http\Controllers\AuthController', 'logout']);

// Setup Step 4 & completion — accessible post-install via session flag
$router->get('/setup/step4', ['App\Http\Controllers\SetupController', 'step4']);
$router->get('/setup/complete', ['App\Http\Controllers\SetupController', 'complete']);
$router->get('/setup', function () {
    header('Location: ' . Config::get('app.base_folder') . 'admin');
    exit;
});

// ==========================================================
// PROTECTED ROUTES (ADMIN)
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
        return new \App\Http\Middleware\AuthMiddleware();
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

// 6. Dispatch
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($method, $uri);
