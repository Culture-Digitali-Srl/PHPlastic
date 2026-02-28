<?php

namespace App\Http\Controllers;

use App\Core\View;
use App\Core\Security;
use App\Core\Config;
use App\Core\Auth\AuthManager;
use App\Core\Audit\AuditLogger;

/**
 * Controller for Login and Logout management
 */
class AuthController
{
    /**
     * Show the login form
     */
    public function showLoginForm(): void
    {
        // If already logged in, redirect
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
     * Process the login attempt
     */
    public function login(): void
    {
        // Manual check if form doesn't arrive as POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }

        // Validate CSRF token
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->showError('Session expired or invalid form. Please try again.');
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Attempt Auth
        if (AuthManager::attempt($email, $password)) {
            AuditLogger::log('user_login', 'users', $_SESSION['user_id'], ['email' => $email]);
            $this->redirect('admin');
        } else {
            // A user failed to login. Log the event specifying the attempted email (no password!)
            AuditLogger::log('user_login_failed', null, null, ['email_attempt' => $email]);
            $this->showError('Invalid credentials.');
        }
    }

    /**
     * Logs out the current user
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
