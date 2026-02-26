<?php

namespace App\Services\AI;

use App\Core\DB;
use App\Core\Auth\AuthManager;

/**
 * TokenLogger intercetta e salva sul DB ogni consumo generato da chiamate AI.
 */
class TokenLogger
{
    public static function log(
        string $provider,
        string $model,
        int $inTokens,
        int $outTokens,
        int $latencyMs,
        ?string $featureTag = null,
        string $status = 'success',
        float $cost = 0.0,
        array $meta = []
    ): void {
        $userId = AuthManager::isLoggedIn() ? $_SESSION['user_id'] : null;
        $totalTokens = $inTokens + $outTokens;
        $metaJson = empty($meta) ? null : json_encode($meta);

        try {
            DB::query(
                "INSERT INTO token_events 
                (user_id, provider, model, in_tokens, out_tokens, total_tokens, cost, feature, status, latency_ms, meta_json) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $userId,
                    $provider,
                    $model,
                    $inTokens,
                    $outTokens,
                    $totalTokens,
                    $cost,
                    $featureTag,
                    $status,
                    $latencyMs,
                    $metaJson
                ]
            );
        } catch (\PDOException $e) {
            error_log("Errore TokenLogger: " . $e->getMessage());
        }
    }
}
