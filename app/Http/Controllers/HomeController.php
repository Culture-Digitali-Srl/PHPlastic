<?php

namespace App\Http\Controllers;

use App\Core\View;
use App\Core\Config;

class HomeController
{
    public function index(): void
    {
        View::render('public.layout', [
            'content' => 'public.home',
            'title' => 'PHPlastic - Il Framework Versatile',
            'version' => Config::get('app.version', 'v0.5.0')
        ]);
    }
}
