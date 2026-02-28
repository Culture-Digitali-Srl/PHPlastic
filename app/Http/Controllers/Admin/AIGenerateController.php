<?php

namespace App\Http\Controllers\Admin;

use App\Services\AI\Drivers\GroqDriver;
use App\Services\AIPatcher\PolicyGate;
use App\Services\AIPatcher\PatchManager;
use App\Core\Audit\AuditLogger;

class AIGenerateController
{
    /**
     * Receives prompt from frontend, asks Groq for the structured architecture and returns pre-flight results.
     */
    public function generate(): void
    {
        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $prompt = $data['prompt'] ?? '';

        if (empty($prompt)) {
            echo json_encode(['error' => 'Empty prompt provided.']);
            return;
        }

        $systemPrompt = "
        You are an AI specialized in scaffolding PHP code for the 'PHPlastic' native framework.
        - The framework root uses native PHP and PDO. There are no ORMs. Route, View, Controller.
        - Controllers for APIs or Modules can extend `App\Core\Controller` to use `\$this->jsonResponse()`.
        - Queries are made using `App\Core\DB`.
        
        **New Dynamic Module Architecture v0.6:**
        If the user asks for complex functionality (e.g., E-Commerce, Blog, API), you **MUST** create controllers within a domain folder in `app/Modules/{ModuleName}/`.
        Example: `app/Modules/Shop/ShopController.php`.
        If you use the modules path, you MUST provide the `app/Modules/{ModuleName}/routes.php` file in which you will write routes using `\$router->get(...)` (and routing them to your Controller with the fully qualified namespace).
        
        **SQL Database Migrations:**
        If you identify the need for tables for the requested domain, you must provide pure SQLite instructions (DDL `CREATE TABLE IF NOT EXISTS...`). 
        Use the special key `sql_migrations` in the root of your JSON output.

        RESPOND **EXCLUSIVELY** in perfect raw JSON array/object format, NEVER use backticks or markdown tags like ` ```json `. Return ONLY the raw JSON.
        Required structure:
        {
            \"sql_migrations\": [
                \"CREATE TABLE IF NOT EXISTS notes (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, content TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP);\"
            ],
            \"files\": [
                {
                    \"file\": \"app/Modules/Notes/routes.php\",
                    \"content\": \"<?php\\n\\n\$router->get('/notes', [\\\\App\\\\Modules\\\\Notes\\\\NotesController::class, 'index']);\"
                },
                {
                    \"file\": \"app/Modules/Notes/NotesController.php\",
                    \"content\": \"<?php\\n...\"
                }
            ]
        }
        
        The user requested: $prompt
        ";

        try {
            $client = new GroqDriver();

            // Attempt to fetch from database settings, otherwise safe fallback
            $modelSetting = \App\Core\DB::query("SELECT value FROM settings WHERE key = 'ai_default_model'")->fetch();
            $model = ($modelSetting && !empty($modelSetting['value'])) ? $modelSetting['value'] : 'llama-3.1-8b-instant';

            $response = $client->chat([
                ['role' => 'user', 'content' => $systemPrompt]
            ], $model, ['max_tokens' => 2000, 'temperature' => 0.2], 'ai_wizard');

            // Robust JSON extraction of array or object
            $startArray = strpos($response, '[');
            $startObj = strpos($response, '{');

            $startPos = false;
            $endPos = false;

            if ($startObj !== false && ($startArray === false || $startObj < $startArray)) {
                $startPos = $startObj;
                $endPos = strrpos($response, '}');
            } elseif ($startArray !== false) {
                $startPos = $startArray;
                $endPos = strrpos($response, ']');
            }

            if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
                $response = substr($response, $startPos, $endPos - $startPos + 1);
            }

            $files = json_decode($response, true);

            if (!is_array($files)) {
                echo json_encode(['error' => 'The AI response was not a valid JSON.', 'raw' => $response]);
                return;
            }

            // Backward compatibility support for array or dictionary output
            $extractedFiles = isset($files['files']) ? $files['files'] : (is_array(reset($files)) ? $files : []);
            $sqlMigrations = isset($files['sql_migrations']) ? $files['sql_migrations'] : [];

            // Pre-flight filter with PolicyGate
            $vetted = [];
            $rejected = [];
            foreach ($extractedFiles as $f) {
                if (isset($f['file']) && isset($f['content'])) {
                    if (PolicyGate::isAllowed($f['file'])) {
                        $vetted[] = $f;
                    } else {
                        $rejected[] = $f['file'];
                    }
                }
            }

            // Save to session for the "Apply" step
            $_SESSION['pending_ai_patch'] = $vetted;
            $_SESSION['pending_ai_sql'] = $sqlMigrations;

            echo json_encode([
                'success' => true,
                'proposed_files' => count($vetted),
                'proposed_sql' => count($sqlMigrations),
                'files' => $vetted,
                'sql' => $sqlMigrations,
                'rejected_paths' => $rejected
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => "Groq API Error: " . $e->getMessage()]);
        }
    }

    /**
     * Applies approved changes saved in session
     */
    public function apply(): void
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['pending_ai_patch']) || empty($_SESSION['pending_ai_patch'])) {
            echo json_encode(['error' => 'No pending modifications found for approval.']);
            return;
        }

        $filesData = $_SESSION['pending_ai_patch'];
        $formattedFiles = [];
        foreach ($filesData as $f) {
            $formattedFiles[$f['file']] = $f['content'];
        }

        $patchManager = new PatchManager();
        $report = $patchManager->applyFiles($formattedFiles);

        $sqlReport = ['success' => true, 'applied' => [], 'errors' => []];
        if (isset($_SESSION['pending_ai_sql']) && is_array($_SESSION['pending_ai_sql'])) {
            $sqlReport = $patchManager->applyMigrations($_SESSION['pending_ai_sql']);
        }

        if ($report['success'] === true || count($report['applied']) > 0 || count($sqlReport['applied']) > 0) {
            AuditLogger::log('applied_ai_patch', 'System', null, [
                'files' => $report['applied'],
                'sql' => $sqlReport['applied']
            ]);
            unset($_SESSION['pending_ai_patch']);
            unset($_SESSION['pending_ai_sql']);
        }

        echo json_encode([
            'files_report' => $report,
            'sql_report' => $sqlReport,
            'success' => $report['success'] && $sqlReport['success'],
            'applied' => array_merge($report['applied'] ?? [], $sqlReport['applied'] ?? []),
            'errors' => array_merge($report['errors'] ?? [], $sqlReport['errors'] ?? [])
        ]);
    }
}
