<?php

namespace App\Http\Controllers\Admin;

use App\Core\View;
use App\Core\DB;

class TokenDashboardController
{
    public function index(): void
    {
        // Statistiche Generali
        $sqlStats = "
            SELECT 
                COUNT(*) as total_requests,
                SUM(in_tokens) as total_in,
                SUM(out_tokens) as total_out,
                SUM(total_tokens) as sum_tokens,
                SUM(cost) as total_cost
            FROM token_events
            WHERE status = 'success'
        ";
        $stats = DB::query($sqlStats)->fetch() ?: [];

        // Ultime richieste
        $sqlList = "
            SELECT t.*, u.name as user_name 
            FROM token_events t
            LEFT JOIN users u ON t.user_id = u.id
            ORDER BY t.created_at DESC
            LIMIT 50
        ";
        $events = DB::query($sqlList)->fetchAll();

        View::render('admin.layout', [
            'content' => 'admin.tokens.index',
            'title' => 'Token & Cost Dashboard | PHPlastic',
            'stats' => $stats,
            'events' => $events
        ]);
    }
}
