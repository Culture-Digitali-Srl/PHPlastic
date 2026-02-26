<?php

namespace App\Http\Controllers\Admin;

use App\Core\View;
use App\Core\DB;

class AuditController
{
    public function index(): void
    {
        $sql = "
            SELECT a.*, u.name as user_name 
            FROM audit_events a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT 100
        ";
        $events = DB::query($sql)->fetchAll();

        View::render('admin.layout', [
            'content' => 'admin.audit.index',
            'title' => 'Audit Log | PHPlastic',
            'events' => $events
        ]);
    }
}
