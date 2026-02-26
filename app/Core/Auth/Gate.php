<?php

namespace App\Core\Auth;

use App\Core\DB;

/**
 * Gate gestisce le autorizzazioni RBAC
 */
class Gate
{
    private static ?array $userPermissions = null;

    /**
     * Verifica se l'utente loggato possiede un determinato permesso
     */
    public static function allows(string $permissionName): bool
    {
        if (!AuthManager::isLoggedIn()) {
            return false;
        }

        $userId = $_SESSION['user_id'];

        // Cache in memory per la singola richiesta
        if (self::$userPermissions === null) {
            self::loadPermissions($userId);
        }

        return in_array($permissionName, self::$userPermissions);
    }

    /**
     * Controlla se possiede uno qualsiasi dei permessi passati
     */
    public static function allowsAny(array $permissions): bool
    {
        foreach ($permissions as $p) {
            if (self::allows($p)) {
                return true;
            }
        }
        return false;
    }

    private static function loadPermissions(int $userId): void
    {
        $sql = "
            SELECT DISTINCT p.name 
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN user_roles ur ON rp.role_id = ur.role_id
            WHERE ur.user_id = :user_id
        ";
        $stmt = DB::query($sql, ['user_id' => $userId]);
        $rows = $stmt->fetchAll();

        self::$userPermissions = array_column($rows, 'name');
    }

    /**
     * Discarica la cache interna dei permessi
     */
    public static function flushCache(): void
    {
        self::$userPermissions = null;
    }
}
