<?php

namespace App\Services\AI\Drivers;

use App\Services\AI\AIClientInterface;
use App\Services\AI\TokenLogger;
use App\Core\Config;

class GroqDriver implements AIClientInterface
{
    public function chat(array $messages, string $model, array $options = [], ?string $featureTag = null): string
    {
        $apiKey = Config::get('GROQ_API_KEY') ?? $_ENV['GROQ_API_KEY'] ?? null;

        if (!$apiKey) {
            throw new \Exception("GROQ_API_KEY non configurata");
        }

        $url = "https://api.groq.com/openai/v1/chat/completions";

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
        ], $options);

        $headers = [
            "Authorization: Bearer " . $apiKey,
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Groq è veloce!

        $start = microtime(true);
        $responseJson = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $latencyMs = (int)((microtime(true) - $start) * 1000);

        if ($responseJson === false || $httpCode >= 400) {
            TokenLogger::log(
                provider: 'Groq',
                model: $model,
                inTokens: 0,
                outTokens: 0,
                latencyMs: $latencyMs,
                featureTag: $featureTag,
                status: 'error',
                meta: ['error' => $error, 'http_code' => $httpCode, 'raw_response' => $responseJson]
            );
            throw new \Exception("Errore Groq API HTTP $httpCode: $error - $responseJson");
        }

        $result = json_decode($responseJson, true);

        // Estrazione token
        $inTokens = $result['usage']['prompt_tokens'] ?? 0;
        $outTokens = $result['usage']['completion_tokens'] ?? 0;

        // Calcolo costo approssimativo per Groq (es. LLaMA3-70B è ~$0.59/1M in, $0.79/1M out)
        // Ipotizziamo LLaMA3-70B di base se non specificato
        $costPerMIn = 0.59;
        $costPerMOut = 0.79;

        if (strpos($model, 'mixtral') !== false) {
            $costPerMIn = 0.24;
            $costPerMOut = 0.24;
        }

        $cost = ($inTokens / 1000000 * $costPerMIn) + ($outTokens / 1000000 * $costPerMOut);

        TokenLogger::log(
            provider: 'Groq',
            model: $result['model'] ?? $model,
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
