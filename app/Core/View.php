<?php

namespace App\Core;

/**
 * Gestisce l'inclusione pulita delle View PHP 
 */
class View
{
    /**
     * Esegue il render di un file view, iniettando le variabili necessarie.
     * Le variabili verranno protette automaticamente dall'escaping XSS di default.
     */
    public static function render(string $viewPath, array $data = []): void
    {
        $file = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($file)) {
            throw new \Exception("View non trovata: {$file}");
        }

        // Estrai l'array in singole variabili per la view
        extract($data);

        // Include la view
        require $file;
    }

    /**
     * Rende sicuro l'output sfuggendo i caratteri speciali
     */
    public static function e(string|null $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
