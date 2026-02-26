<?php

namespace App\Http\Controllers\Admin;

use App\Core\View;
use App\Core\DB;

class UserController
{
    public function index(): void
    {
        $sql = "
            SELECT u.*, GROUP_CONCAT(r.name) as roles 
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ";
        $users = DB::query($sql)->fetchAll();

        View::render('admin.layout', [
            'content' => 'admin.users.index',
            'title' => 'Gestione Utenti | PHPlastic',
            'users' => $users
        ]);
    }
}
