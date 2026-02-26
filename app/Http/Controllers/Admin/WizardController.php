<?php

namespace App\Http\Controllers\Admin;

use App\Core\View;

class WizardController
{
    public function index(): void
    {
        View::render('admin.layout', [
            'content' => 'admin.wizard.index',
            'title' => 'Bootstrap Wizard | PHPlastic'
        ]);
    }
}
