<?php

namespace App\Http\Controllers;

use App\Core\View;
use App\Core\Security;
use App\Core\Config;
use App\Core\Auth\AuthManager;
use App\Core\Audit\AuditLogger;

/**
 * Controller per la gestione di Login e Logout
 */
class AuthController
{
    /**
     * Mostra il form di login
     */
    public function showLoginForm(): void
    {
        // Se già loggato, reindirizza
        if (AuthManager::isLoggedIn()) {
            $this->redirect('admin');
            return;
        }

        View::render('layout', [
            'content' => 'auth/login',
            'title' => 'Login',
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    /**
     * Elabora il tentativo di login
     */
    public function login(): void
    {
        // Controllo manuale temporaneo se form non arriva
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }

        // Valida CSRF Base
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->showError('Sessione scaduta o form non valido. Riprova.');
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Tenta auth
        if (AuthManager::attempt($email, $password)) {
            AuditLogger::log('user_login', 'users', $_SESSION['user_id'], ['email' => $email]);
            $this->redirect('admin');
        } else {
            // Un utente fallisce il login. Logghiamo l'evento specificando la mail tentata (senza password!)
            AuditLogger::log('user_login_failed', null, null, ['email_attempt' => $email]);
            $this->showError('Credenziali non valide.');
        }
    }

    /**
     * Effettua il logout dell'utente corrente
     */
    public function logout(): void
    {
        if (AuthManager::isLoggedIn()) {
            AuditLogger::log('user_logout', 'users', $_SESSION['user_id']);
        }
        AuthManager::logout();
        $this->redirect('login');
    }

    private function redirect(string $path): void
    {
        $base = Config::get('app.base_folder', '/');
        header("Location: " . rtrim($base, '/') . "/" . ltrim($path, '/'));
        exit;
    }

    private function showError(string $error): void
    {
        View::render('layout', [
            'content' => 'auth/login',
            'title' => 'Login',
            'csrf_token' => Security::generateCsrfToken(),
            'error' => $error
        ]);
    }
}
