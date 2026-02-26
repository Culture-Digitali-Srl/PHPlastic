<?php

namespace App\Http\Controllers\Admin;

use App\Core\Config;
use App\Services\AI\Drivers\GroqDriver;

class AIDemoController
{
    /**
     * Esegue un test rapido per generare Token usage
     */
    public function runTest(): void
    {
        // Se non c'è una chiave, forniamo un default dummy per i test locali su DB
        $key = Config::get('GROQ_API_KEY') ?? $_ENV['GROQ_API_KEY'] ?? null;

        if (empty($key)) {
            // Fake simulazione di fallback per non bloccare i test senza API key reale
            \App\Services\AI\TokenLogger::log(
                provider: 'Mock',
                model: 'mock-llm-1.0',
                inTokens: rand(50, 150),
                outTokens: rand(10, 80),
                latencyMs: rand(200, 1500),
                featureTag: 'ai_demo',
                status: 'success',
                cost: 0.001
            );
            $response = "Mock: Nessuna chiave API trovata, ma ho generato token fittizi nel log per collaudare la Dashboard!";
        } else {
            try {
                $client = new GroqDriver();
                $response = $client->chat([
                    ['role' => 'user', 'content' => 'Say a very short creative greeting to a developer testing a new framework called PHPlastic!']
                ], 'llama3-8b-8192', ['max_tokens' => 50], 'ai_demo');
            } catch (\Exception $e) {
                $response = "Errore durante la chiamata: " . $e->getMessage();
            }
        }

        // Salvo il feedback in session per mostrarlo
        $_SESSION['flash_message'] = $response;

        $base = Config::get('app.base_folder', '/');
        header("Location: " . rtrim($base, '/') . "/admin/tokens");
        exit;
    }
}
