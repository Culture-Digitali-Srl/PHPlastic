<?php

namespace App\Core\Audit;

use App\Core\DB;
use App\Core\Auth\AuthManager;

/**
 * AuditLogger registra permanentemente azioni rilevanti a fini di auditing.
 */
class AuditLogger
{
    /**
     * Registra un evento nel DB
     * @param string $action L'azione eseguita (es. 'user_login', 'delete_user')
     * @param string|null $entityType Il tipo di entità toccata (es. 'users', 'settings')
     * @param string|null $entityId ID dell'entità
     * @param array $metaJson Dati addizionali da serializzare
     */
    public static function log(string $action, ?string $entityType = null, ?string $entityId = null, array $metaJson = []): void
    {
        $userId = AuthManager::isLoggedIn() ? $_SESSION['user_id'] : null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $meta = empty($metaJson) ? null : json_encode($metaJson);

        try {
            DB::query(
                "INSERT INTO audit_events (user_id, action, entity_type, entity_id, meta_json, ip, ua) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$userId, $action, $entityType, $entityId, $meta, $ip, $ua]
            );
        } catch (\PDOException $e) {
            // Su un fallimento audit, idealmente logghi su file per fallback
            error_log("Audit Log Failure: " . $e->getMessage());
        }
    }
}
