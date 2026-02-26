<?php

namespace App\Http\Controllers\Admin;

use App\Core\View;
use App\Core\DB;

class SettingsController
{
    public function index(): void
    {
        $settings = DB::query("SELECT * FROM settings ORDER BY key ASC")->fetchAll();

        View::render('admin.layout', [
            'content' => 'admin.settings.index',
            'title' => 'Impostazioni Globali | PHPlastic',
            'settings' => $settings
        ]);
    }

    public function store(): void
    {
        if (!\App\Core\Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die("Errore: CSRF Token mancante o invalido.");
        }
        $action = $_POST['action'] ?? 'update_settings';

        if ($action === 'add_setting') {
            $key = trim($_POST['key'] ?? '');
            $val = trim($_POST['value'] ?? '');
            $type = $_POST['type'] ?? 'string';

            if (!empty($key)) {
                DB::query("INSERT INTO settings (key, value, type) VALUES (?, ?, ?)", [$key, $val, $type]);
            }
        } elseif ($action === 'update_settings') {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $val) {
                DB::query("UPDATE settings SET value = ? WHERE key = ?", [$val, $key]);
            }
            // Add ai_default_model if passed explicitly
            if (isset($_POST['ai_default_model'])) {
                $model = trim($_POST['ai_default_model']);
                $exists = DB::query("SELECT key FROM settings WHERE key = 'ai_default_model'")->fetch();
                if ($exists) {
                    DB::query("UPDATE settings SET value = ? WHERE key = 'ai_default_model'", [$model]);
                } else {
                    DB::query("INSERT INTO settings (key, value, type) VALUES ('ai_default_model', ?, 'string')", [$model]);
                }
            }
        }

        header('Location: ' . \App\Core\Config::get('app.base_folder') . 'admin/settings');
        exit;
    }

    public function fetchModels(): void
    {
        header('Content-Type: application/json');
        $apiKey = \App\Core\Config::get('GROQ_API_KEY') ?? $_ENV['GROQ_API_KEY'] ?? null;

        if (!$apiKey) {
            echo json_encode(['error' => 'API Key Groq non configurata.']);
            return;
        }

        $ch = curl_init("https://api.groq.com/openai/v1/models");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400 || $response === false) {
            echo json_encode(['error' => 'Errore nel fetch dei modelli da Groq.']);
            return;
        }

        echo $response;
    }

    public function reset(): void
    {
        if (!\App\Core\Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die("Errore: CSRF Token mancante o invalido.");
        }

        // Esegue i drop delle tabelle
        $tables = ['token_events', 'audit_events', 'settings', 'user_roles', 'role_permissions', 'users', 'permissions', 'roles'];
        foreach ($tables as $t) {
            DB::query("DROP TABLE IF EXISTS " . $t);
        }

        // Riesegue le migrazioni in memory tramite system/shell
        $cliPath = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'cli.php';
        if (file_exists($cliPath)) {
            shell_exec(PHP_BINARY . ' ' . escapeshellarg($cliPath) . ' migrate 2>&1');
            shell_exec(PHP_BINARY . ' ' . escapeshellarg($cliPath) . ' create-admin admin@example.com password123 2>&1');
        }

        session_destroy();
        header('Location: ' . \App\Core\Config::get('app.base_folder') . 'login');
        exit;
    }
}
