<?php

namespace App\Services\AI\Drivers;

use App\Services\AI\AIClientInterface;
use App\Services\AI\TokenLogger;
use App\Core\Config;

class OpenRouterDriver implements AIClientInterface
{
    public function chat(array $messages, string $model, array $options = [], ?string $featureTag = null): string
    {
        $apiKey = Config::get('OPENROUTER_API_KEY') ?? $_ENV['OPENROUTER_API_KEY'] ?? null;

        if (!$apiKey) {
            throw new \Exception("OPENROUTER_API_KEY non configurata");
        }

        $url = "https://openrouter.ai/api/v1/chat/completions";

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
        ], $options);

        // Header suggeriti da OpenRouter per statistiche
        $headers = [
            "Authorization: Bearer " . $apiKey,
            "HTTP-Referer: " . Config::get('app.url', 'http://localhost'),
            "X-Title: PHPlastic Starter Kit",
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $start = microtime(true);
        $responseJson = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $latencyMs = (int)((microtime(true) - $start) * 1000);

        if ($responseJson === false || $httpCode >= 400) {
            TokenLogger::log(
                provider: 'OpenRouter',
                model: $model,
                inTokens: 0,
                outTokens: 0,
                latencyMs: $latencyMs,
                featureTag: $featureTag,
                status: 'error',
                meta: ['error' => $error, 'http_code' => $httpCode, 'raw_response' => $responseJson]
            );
            throw new \Exception("Errore OpenRouter API HTTP $httpCode: $error - $responseJson");
        }

        $result = json_decode($responseJson, true);

        // Estrazione token
        $inTokens = $result['usage']['prompt_tokens'] ?? 0;
        $outTokens = $result['usage']['completion_tokens'] ?? 0;
        $cost = $result['usage']['total_cost'] ?? 0.0; // OpenRouter rida spesso il cost 

        TokenLogger::log(
            provider: 'OpenRouter',
            model: $result['model'] ?? $model, // Rispettiamo il routing model se cambiato dall'endpoint
            inTokens: $inTokens,
            outTokens: $outTokens,
            latencyMs: $latencyMs,
            featureTag: $featureTag,
            status: 'success',
            cost: $cost
        );

        return $result['choices'][0]['message']['content'] ?? '';
    }
}
