<?php

namespace App\Http\Controllers\Admin;

use App\Services\AI\Drivers\GroqDriver;
use App\Services\AIPatcher\PolicyGate;
use App\Services\AIPatcher\PatchManager;
use App\Core\Audit\AuditLogger;

class AIGenerateController
{
    /**
     * Riceve il prompt dal frontend, chiede a Groq l'architettura in un formato strutturato e restituisce il pre-flight.
     */
    public function generate(): void
    {
        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $prompt = $data['prompt'] ?? '';

        if (empty($prompt)) {
            echo json_encode(['error' => 'Prompt vuoto fornito.']);
            return;
        }

        $systemPrompt = "
        Sei un AI specializzato nello scaffold di codice PHP per il framework nativo 'PHPlastic'.
        - La root del framework usa PHP nativo e PDO. Non ci sono ORM. Route, View, Controller.
        - Controller per API o Moduli possono estendere `App\Core\Controller` per usare `\$this->jsonResponse()`.
        - Le query si fanno con `App\Core\DB`.
        
        **Nuova Architettura a Moduli Dinamici v0.6:**
        Se l'utente chiede funzionalità complesse (es. E-Commerce, Blog, API), **DEVI** creare i controller all'interno di una cartella di dominio in `app/Modules/{NomeModulo}/`.
        Esempio: `app/Modules/Shop/ShopController.php`.
        Se usi la strada dei moduli, DEVI fornire il file `app/Modules/{NomeModulo}/routes.php` nel quale scriverai rotte usando `\$router->get(...)` (e instradandole al tuo Controller col fully qualified namespace).
        
        **Migrazioni DataBase SQL:**
        Se ravvisi il bisogno di tabelle per il dominio richiesto, devi fornire istruzioni SQLite purissime (DDL `CREATE TABLE IF NOT EXISTS...`). 
        Usa la chiave speciale `sql_migrations` nell'oggetto radice del tuo output JSON.

        RISPONDI **ESCLUSIVAMENTE** in puro formato JSON array/object perfetto, senza MAI usare backtick o tag markdown ` ```json `. Restituisci SOLO IL JSON crudo.
        Struttura richiesta:
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
        
        L'utente ha richiesto: $prompt
        ";

        try {
            $client = new GroqDriver();

            // Tentiamo di prelevare dal database settings, altrimenti fallback sicuro
            $modelSetting = \App\Core\DB::query("SELECT value FROM settings WHERE key = 'ai_default_model'")->fetch();
            $model = ($modelSetting && !empty($modelSetting['value'])) ? $modelSetting['value'] : 'llama-3.1-8b-instant';

            $response = $client->chat([
                ['role' => 'user', 'content' => $systemPrompt]
            ], $model, ['max_tokens' => 2000, 'temperature' => 0.2], 'ai_wizard');

            // Estrazione robusta del JSON array o object
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
                echo json_encode(['error' => 'La risposta dell\'AI non era un JSON valido.', 'raw' => $response]);
                return;
            }

            // Supporto retrocompatibile (se l'AI dà dritto array o dict)
            $extractedFiles = isset($files['files']) ? $files['files'] : (is_array(reset($files)) ? $files : []);
            $sqlMigrations = isset($files['sql_migrations']) ? $files['sql_migrations'] : [];

            // Filtro Pre-flight con PolicyGate
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

            // Salviamo in sessione per l'eventuale step "Apply"
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
            echo json_encode(['error' => "Errore API Groq: " . $e->getMessage()]);
        }
    }

    /**
     * Applica le modifiche approvate salvate in sessione
     */
    public function apply(): void
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['pending_ai_patch']) || empty($_SESSION['pending_ai_patch'])) {
            echo json_encode(['error' => 'Nessuna modifica in attesa di approvazione trovata.']);
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
