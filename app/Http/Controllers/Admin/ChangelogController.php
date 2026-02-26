<?php

namespace App\Http\Controllers\Admin;

use App\Core\View;

class ChangelogController
{
    public function index(): void
    {
        $changelogPath = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'CHANGELOG.md';
        $content = '';

        if (file_exists($changelogPath)) {
            $content = file_get_contents($changelogPath);
        }

        // Un parser Markdown molto basico (Header e List item)
        $htmlContent = $this->parseMarkdownLite($content);

        View::render('admin.layout', [
            'content' => 'admin.changelog.index',
            'title' => 'Release Notes | PHPlastic',
            'changelogHtml' => $htmlContent
        ]);
    }

    private function parseMarkdownLite(string $md): string
    {
        if (empty($md)) {
            return '<p class="text-gray-500">Nessun changelog disponibile.</p>';
        }

        // H2 (Versioni)
        $md = preg_replace('/^## (.*)$/m', '<h2 class="text-2xl font-bold text-indigo-700 mt-8 mb-4 border-b pb-2">$1</h2>', $md);

        // H3 (Aggiunto, Risolto, etc)
        $md = preg_replace('/^### (.*)$/m', '<h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3">$1</h3>', $md);

        // H1 (Titolo principale)
        $md = preg_replace('/^# (.*)$/m', '<h1 class="text-3xl font-extrabold text-gray-900 mb-6">$1</h1>', $md);

        // Liste puntate
        $md = preg_replace('/^- (.*)$/m', '<li class="mb-2 text-gray-600 flex items-start"><svg class="w-4 h-4 text-indigo-400 mt-1 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span>$1</span></li>', $md);

        // Grassetto
        $md = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $md);

        // Code inline
        $md = preg_replace('/`(.*?)`/', '<code class="bg-gray-100 text-pink-600 px-1 py-0.5 rounded font-mono text-sm">$1</code>', $md);

        // Ritorno a capo (paragrafi semplici)
        $md = nl2br($md);

        // Wrappa le liste puntate in UL
        $md = preg_replace('/(<li.*<\/li>)/s', '<ul class="space-y-1 mb-4">$1</ul>', $md);

        return '<div class="changelog-content">' . $md . '</div>';
    }
}
