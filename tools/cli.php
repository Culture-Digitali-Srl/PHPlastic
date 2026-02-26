<?php

/**
 * CLI Tooling per migrations, seeding, module creation
 */

if (php_sapi_name() !== 'cli') {
    die("Questo script può essere eseguito solo da riga di comando (CLI).\n");
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\DB;
use App\Core\Auth\AuthManager;

// Lettura e setup config
Config::loadEnv(__DIR__ . '/../.env');
Config::load('app', [
    'db_path' => __DIR__ . '/../storage/app.sqlite'
]);

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'migrate':
        echo "=> Esecuzione Migrations...\n";
        runMigrations();
        break;

    case 'make:admin':
        echo "=> Creazione Admin User...\n";
        createAdminUser();
        break;

    case 'help':
    default:
        echo "PHPlastic CLI Tools\n";
        echo "Uso: php tools/cli.php [command]\n\n";
        echo "Comandi:\n";
        echo "  migrate       - Crea le tabelle del database MVP\n";
        echo "  make:admin    - Crea il primo utente amministratore\n";
        break;
}

function runMigrations()
{
    $db = DB::getInstance();

    // Creazione tabella users (MVP v0.1)
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            status VARCHAR(50) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    echo "Tabella 'users' verificata.\n";

    // --- Inizio v0.2 schema ---
    $db->exec("
        CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE
        );
        CREATE TABLE IF NOT EXISTS permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE,
            description VARCHAR(255)
        );
        CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INTEGER,
            permission_id INTEGER,
            PRIMARY KEY (role_id, permission_id),
            FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE,
            FOREIGN KEY(permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS user_roles (
            user_id INTEGER,
            role_id INTEGER,
            PRIMARY KEY (user_id, role_id),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS audit_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NULL,
            action VARCHAR(255) NOT NULL,
            entity_type VARCHAR(100) NULL,
            entity_id VARCHAR(100) NULL,
            meta_json TEXT NULL,
            ip VARCHAR(45) NULL,
            ua TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS settings (
            key VARCHAR(100) PRIMARY KEY,
            value TEXT,
            type VARCHAR(20) DEFAULT 'string',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    echo "Tabelle 'roles', 'permissions', 'audit_events', 'settings' verificate.\n";

    // --- Inizio v0.3 schema ---
    $db->exec("
        CREATE TABLE IF NOT EXISTS token_events (
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
        );
    ");
    echo "Tabella 'token_events' verificata.\n";

    // Seed dei ruoli predefiniti se non esistono
    $stmt = $db->query("SELECT count(*) as c FROM roles");
    if ($stmt->fetch()['c'] == 0) {
        $db->exec("
            INSERT INTO roles (name) VALUES ('admin'), ('manager'), ('operator'), ('viewer');
            INSERT INTO permissions (name, description) VALUES 
                ('manage_users', 'Gestione utenti e ruoli'),
                ('view_audit', 'Lettura audit logs'),
                ('manage_settings', 'Modifica impostazioni di sistema');
        ");

        // Assegna tutto all'admin
        $db->exec("
            INSERT INTO role_permissions (role_id, permission_id)
            SELECT r.id, p.id FROM roles r, permissions p WHERE r.name = 'admin';
        ");
        echo "Seed ruoli base inseriti.\n";
    }
}

function createAdminUser()
{
    $email = "admin@example.com";
    $password = "password123"; // in produzione verrà chiesto via prompt
    $hash = AuthManager::hashPassword($password);

    $db = DB::getInstance();

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, status) VALUES (?, ?, ?, 'active')");
        $stmt->execute(['Admin', $email, $hash]);
        $userId = $db->lastInsertId();

        $stmt = $db->prepare("SELECT id FROM roles WHERE name = 'admin'");
        $stmt->execute();
        $roleInfo = $stmt->fetch();

        if ($roleInfo) {
            $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->execute([$userId, $roleInfo['id']]);
        }

        $db->commit();
        echo "Utente admin creato e ruolo assegnato!\nEmail: $email\nPassword: $password\n";
    } catch (\PDOException $e) {
        $db->rollBack();
        if ($e->getCode() == 23000) {
            echo "Errore: l'utente $email esiste già. Provo ad assegnargli il ruolo admin se non lo ha...\n";
            // Assegna il ruolo admin all'utente esistente se mancante
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $u = $stmt->fetch();
            if ($u) {
                $stmt = $db->prepare("SELECT id FROM roles WHERE name = 'admin'");
                $stmt->execute();
                $r = $stmt->fetch();
                if ($r) {
                    try {
                        $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                        $stmt->execute([$u['id'], $r['id']]);
                        echo "Ruolo admin assegnato all'utente pre-esistente: $email\n";
                    } catch (\PDOException $ex) {
                        echo "L'utente $email aveva originariamente o ha già il ruolo admin.\n";
                    }
                }
            }
        } else {
            echo "Errore DB: " . $e->getMessage() . "\n";
        }
    }
}
