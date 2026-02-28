<?php

namespace App\Http\Controllers;

use App\Core\View;
use App\Core\Config;
use App\Core\DB;
use App\Core\Security;
use App\Core\Auth\AuthManager;

/**
 * Handles the first-run setup wizard (4 steps).
 * This controller is only accessible when no database/users exist.
 */
class SetupController
{
    /**
     * Check if setup is needed (no users table or no users).
     */
    public static function isSetupNeeded(): bool
    {
        $dbPath = Config::get('app.db_path', __DIR__ . '/../../storage/app.sqlite');

        if (!file_exists($dbPath)) {
            return true;
        }

        try {
            $pdo = DB::getInstance();
            $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetch();
            if (!$result) {
                return true;
            }
            $count = DB::query("SELECT COUNT(*) as c FROM users")->fetch();
            return ($count['c'] ?? 0) == 0;
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Step 1: Choose project type
     */
    public function step1(): void
    {
        Security::startSession();
        View::render('setup.layout', [
            'content' => 'setup.step1',
            'title' => 'Setup — What do you want to build?',
            'currentStep' => 1
        ]);
    }

    /**
     * POST: Save project type choice, go to step 2
     */
    public function saveProjectType(): void
    {
        Security::startSession();
        $type = $_POST['project_type'] ?? 'custom';
        $customPrompt = trim($_POST['custom_prompt'] ?? '');

        $prompts = [
            'website' => 'I need a modern showcase website with a homepage, an about page, a services page, and a contact form. Create a "pages" table (id, title, slug, content, created_at) and a "contacts" table (id, name, email, message, created_at). Build public views for each page and an admin CRUD to manage pages.',
            'ecommerce' => 'I need a micro e-commerce with product catalog, shopping cart, and checkout. Create tables: "products" (id, name, description, price, image_url, stock, category, created_at), "orders" (id, customer_name, customer_email, total, status, created_at), "order_items" (id, order_id, product_id, quantity, price). Build public catalog views and admin product management.',
            'dashboard' => 'I need a management dashboard with CRUD for entities. Create tables: "clients" (id, name, email, phone, company, status, created_at), "projects" (id, client_id, name, description, status, deadline, created_at), "tasks" (id, project_id, title, description, priority, status, assigned_to, created_at). Build admin views with data tables and forms.',
            'api' => 'I need a RESTful API module with JSON endpoints. Create tables: "items" (id, name, description, category, status, created_at, updated_at). Build a full CRUD REST API controller in app/Modules/Api/ with routes for GET /api/items, GET /api/items/{id}, POST /api/items, PUT /api/items/{id}, DELETE /api/items/{id}. Return JSON responses.',
            'custom' => $customPrompt
        ];

        $_SESSION['setup_project_type'] = $type;
        $_SESSION['setup_prompt'] = $prompts[$type] ?? $customPrompt;

        header('Location: ' . Config::get('app.base_folder') . 'setup/step2');
        exit;
    }

    /**
     * Step 2: API Keys
     */
    public function step2(): void
    {
        Security::startSession();
        if (empty($_SESSION['setup_project_type'])) {
            header('Location: ' . Config::get('app.base_folder') . 'setup');
            exit;
        }

        View::render('setup.layout', [
            'content' => 'setup.step2',
            'title' => 'Setup — API Configuration',
            'currentStep' => 2
        ]);
    }

    /**
     * POST: Save API keys to .env
     */
    public function saveApiKeys(): void
    {
        Security::startSession();
        $groqKey = trim($_POST['groq_api_key'] ?? '');

        $envPath = dirname(__DIR__, 2) . '/.env';
        $envContent = '';

        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
        }

        // Update or add GROQ_API_KEY
        if (!empty($groqKey)) {
            if (preg_match('/^GROQ_API_KEY=.*/m', $envContent)) {
                $envContent = preg_replace('/^GROQ_API_KEY=.*/m', "GROQ_API_KEY={$groqKey}", $envContent);
            } else {
                $envContent .= "\nGROQ_API_KEY={$groqKey}";
            }
            $_SESSION['setup_has_api_key'] = true;
        } else {
            $_SESSION['setup_has_api_key'] = false;
        }

        // Ensure APP_ENV is set
        if (!preg_match('/^APP_ENV=.*/m', $envContent)) {
            $envContent .= "\nAPP_ENV=local";
        }

        file_put_contents($envPath, trim($envContent) . "\n");

        // Reload env
        Config::loadEnv($envPath);

        header('Location: ' . Config::get('app.base_folder') . 'setup/step3');
        exit;
    }

    /**
     * POST AJAX: Test API key
     */
    public function testApiKey(): void
    {
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $key = trim($data['key'] ?? '');

        if (empty($key)) {
            echo json_encode(['success' => false, 'error' => 'No API key provided.']);
            return;
        }

        $ch = curl_init("https://api.groq.com/openai/v1/models");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $key]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo json_encode(['success' => true, 'message' => 'Connection successful! Key is valid.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid key or Groq unreachable (HTTP ' . $httpCode . ').']);
        }
    }

    /**
     * Step 3: Admin Account
     */
    public function step3(): void
    {
        Security::startSession();
        if (empty($_SESSION['setup_project_type'])) {
            header('Location: ' . Config::get('app.base_folder') . 'setup');
            exit;
        }

        View::render('setup.layout', [
            'content' => 'setup.step3',
            'title' => 'Setup — Create Admin Account',
            'currentStep' => 3,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    /**
     * POST: Run migrations and create admin user
     */
    public function createAdmin(): void
    {
        Security::startSession();

        $name = trim($_POST['name'] ?? 'Admin');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['setup_error'] = 'Email and password are required.';
            header('Location: ' . Config::get('app.base_folder') . 'setup/step3');
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['setup_error'] = 'Passwords do not match.';
            header('Location: ' . Config::get('app.base_folder') . 'setup/step3');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['setup_error'] = 'Password must be at least 6 characters.';
            header('Location: ' . Config::get('app.base_folder') . 'setup/step3');
            exit;
        }

        // Run migrations directly (not via shell_exec which has config issues)
        try {
            $db = DB::getInstance();

            // Core tables (v0.1)
            $db->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // RBAC tables (v0.2)
            $db->exec("CREATE TABLE IF NOT EXISTS roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(50) NOT NULL UNIQUE
            )");
            $db->exec("CREATE TABLE IF NOT EXISTS permissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(50) NOT NULL UNIQUE,
                description VARCHAR(255)
            )");
            $db->exec("CREATE TABLE IF NOT EXISTS role_permissions (
                role_id INTEGER,
                permission_id INTEGER,
                PRIMARY KEY (role_id, permission_id),
                FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE,
                FOREIGN KEY(permission_id) REFERENCES permissions(id) ON DELETE CASCADE
            )");
            $db->exec("CREATE TABLE IF NOT EXISTS user_roles (
                user_id INTEGER,
                role_id INTEGER,
                PRIMARY KEY (user_id, role_id),
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
            )");
            $db->exec("CREATE TABLE IF NOT EXISTS audit_events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                action VARCHAR(255) NOT NULL,
                entity_type VARCHAR(100) NULL,
                entity_id VARCHAR(100) NULL,
                meta_json TEXT NULL,
                ip VARCHAR(45) NULL,
                ua TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            $db->exec("CREATE TABLE IF NOT EXISTS settings (
                key VARCHAR(100) PRIMARY KEY,
                value TEXT,
                type VARCHAR(20) DEFAULT 'string',
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // Token tracking (v0.3)
            $db->exec("CREATE TABLE IF NOT EXISTS token_events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                provider VARCHAR(50) NOT NULL,
                model VARCHAR(100) NOT NULL,
                in_tokens INTEGER DEFAULT 0,
                out_tokens INTEGER DEFAULT 0,
                total_tokens INTEGER DEFAULT 0,
                cost DECIMAL(10, 6) DEFAULT 0,
                feature VARCHAR(100) NULL,
                status VARCHAR(50) DEFAULT 'success',
                latency_ms INTEGER DEFAULT 0,
                meta_json TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
            )");

            // Seed default roles
            $roleCount = $db->query("SELECT count(*) as c FROM roles")->fetch();
            if (($roleCount['c'] ?? 0) == 0) {
                $db->exec("INSERT INTO roles (name) VALUES ('admin'), ('manager'), ('operator'), ('viewer')");
                $db->exec("INSERT INTO permissions (name, description) VALUES 
                    ('manage_users', 'Manage users and roles'),
                    ('view_audit', 'Read audit logs'),
                    ('manage_settings', 'Modify system settings')");
                $db->exec("INSERT INTO role_permissions (role_id, permission_id)
                    SELECT r.id, p.id FROM roles r, permissions p WHERE r.name = 'admin'");
            }
        } catch (\Exception $e) {
            $_SESSION['setup_error'] = 'Migration error: ' . $e->getMessage();
            header('Location: ' . Config::get('app.base_folder') . 'setup/step3');
            exit;
        }

        // Create admin user
        try {
            $hash = AuthManager::hashPassword($password);
            $db = DB::getInstance();
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, status) VALUES (?, ?, ?, 'active')");
            $stmt->execute([$name, $email, $hash]);
            $userId = $db->lastInsertId();

            $roleStmt = $db->prepare("SELECT id FROM roles WHERE name = 'admin'");
            $roleStmt->execute();
            $role = $roleStmt->fetch();

            if ($role) {
                $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$userId, $role['id']]);
            }

            $db->commit();

            // Auto-login
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            // Check if we should go to step 4 (AI generation)
            if (!empty($_SESSION['setup_has_api_key']) && !empty($_SESSION['setup_prompt']) && $_SESSION['setup_project_type'] !== 'custom') {
                header('Location: ' . Config::get('app.base_folder') . 'setup/step4');
            } else {
                // No AI key or custom — go directly to dashboard
                header('Location: ' . Config::get('app.base_folder') . 'admin');
            }
            exit;
        } catch (\PDOException $e) {
            $_SESSION['setup_error'] = 'Database error: ' . $e->getMessage();
            header('Location: ' . Config::get('app.base_folder') . 'setup/step3');
            exit;
        }
    }

    /**
     * Step 4: AI Generation
     */
    public function step4(): void
    {
        Security::startSession();
        $prompt = $_SESSION['setup_prompt'] ?? '';
        $projectType = $_SESSION['setup_project_type'] ?? 'custom';

        View::render('setup.layout', [
            'content' => 'setup.step4',
            'title' => 'Setup — Generate Your App',
            'currentStep' => 4,
            'prompt' => $prompt,
            'projectType' => $projectType
        ]);
    }

    /**
     * POST: Mark setup as complete, redirect to dashboard
     */
    public function complete(): void
    {
        Security::startSession();
        unset($_SESSION['setup_project_type'], $_SESSION['setup_prompt'], $_SESSION['setup_has_api_key']);

        header('Location: ' . Config::get('app.base_folder') . 'admin');
        exit;
    }
}
