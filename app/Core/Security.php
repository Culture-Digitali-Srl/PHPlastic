<?php

namespace App\Core;

/**
 * Gestisce l'estrazione ed eventuale sanificazione dei dati sicuri
 */
class Security
{
    /**
     * Avvia e mette in sicurezza la sessione
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Hardening sicurezze di base PHP 8
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();

            // Rigenera ID per prevenire session fixation
            if (!isset($_SESSION['__last_regen']) || time() - $_SESSION['__last_regen'] > 300) {
                self::regenerateSession();
            }
        }
    }

    public static function regenerateSession(): void
    {
        session_regenerate_id(true);
        $_SESSION['__last_regen'] = time();
    }

    /**
     * Genera un token CSRF e lo salva in sessione
     */
    public static function generateCsrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida un token CSRF fornito
     */
    public static function validateCsrfToken(?string $token): bool
    {
        self::startSession();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Imposta i Security Headers principali. Invocare all'inizio.
     */
    public static function sendSecurityHeaders(): void
    {
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        // header("Content-Security-Policy: default-src 'self' https://cdn.tailwindcss.com; img-src 'self' data:;"); 
    }
}
