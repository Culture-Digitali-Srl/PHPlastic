<?php

namespace App\Http\Controllers;

use App\Core\View;
use App\Core\Auth\AuthManager;

/**
 * Controller per la gestione dell'Area Admin / Cruscotto
 */
class AdminController
{
    /**
     * Dashboard principale
     */
    public function dashboard(): void
    {
        View::render('admin.layout', [
            'content' => 'admin.dashboard',
            'title' => 'Dashboard | PHPlastic Admin',
            'userName' => $_SESSION['user_name'] ?? 'Guest'
        ]);
    }
}
