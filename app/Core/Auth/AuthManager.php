<?php

namespace App\Core\Auth;

use App\Core\DB;

/**
 * Gestore dell'Autenticazione v0.1: Login/Logout standard, Hash delle password con Argon2id.
 */
class AuthManager
{
    /**
     * Tenta l'autenticazione con email e password, usando Argon2id hashing
     */
    public static function attempt(string $email, string $password): bool
    {
        $stmt = DB::query("SELECT * FROM users WHERE email = :email LIMIT 1", [
            'email' => strtolower(trim($email))
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {

            // Check utente attivo se necessario
            if (isset($user['status']) && $user['status'] !== 'active') {
                return false;
            }

            // Session Fixation Prevention & Setup Session
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];

            // Imposta ultima attività per timeout sessione
            $_SESSION['last_activity'] = time();

            return true;
        }

        return false;
    }

    /**
     * Termina l'autenticazione corrente dell'utente
     */
    public static function logout(): void
    {
        // Cancella variabili di sessione personalizzate
        $_SESSION = [];

        // Distruggi il cookie di sessione
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Controlla se c'è un utente loggato validamente
     */
    public static function isLoggedIn(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Base Idle timeout: dopo 2 ore facciamo cadere la sessione (es. banking app feature)
        $timeout = 7200;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::logout();
            return false;
        }

        $_SESSION['last_activity'] = time(); // Refresh timeout
        return true;
    }

    /**
     * Utilizzato durante la fase di registrazione/creazione utente.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 1
        ]);
    }
}
