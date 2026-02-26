<?php

namespace App\Services\AIPatcher;

use App\Core\Config;

class PolicyGate
{
    /**
     * Valida se un dato file path (relativo alla root del progetto) è autorizzato per la modifica AI.
     */
    public static function isAllowed(string $relativePath): bool
    {
        // Rimuoviamo eventuali leading slashes per standardizzare
        $path = ltrim(str_replace('\\', '/', $relativePath), '/');

        // Path ESPLICITAMENTE BLOCCATI (Blacklist)
        $blockedPaths = [
            'public/index.php',
            'tools/cli.php',
            '.env',
            'config.php',
            'app/Core',
            'composer.json',
            '.gitignore'
        ];

        foreach ($blockedPaths as $blocked) {
            // Se il path inizia con la stringa bloccata o combacia esattamente
            if (str_starts_with($path, $blocked)) {
                return false;
            }
        }

        // Path ESPLICITAMENTE PERMESSI (Whitelist)
        $allowedPrefixes = [
            'app/Http/Controllers',
            'app/Modules', // Per i futuri moduli generati dall'AI
            'resources/views', // Modificato per permettere TUTTE le viste (admin, public, etc)
            'public/assets',   // Permette la generazione di eventuali CSS/JS
            'routes',     // In caso spostassimo rotte qui
        ];

        foreach ($allowedPrefixes as $allowed) {
            if (str_starts_with($path, $allowed)) {
                // Prevenzione base Path Traversal per extra sicurezza
                if (str_contains($path, '..')) {
                    return false;
                }
                return true;
            }
        }

        // Se non ricade nella whitelist, lo blocchiamo per sicurezza
        return false;
    }
}
