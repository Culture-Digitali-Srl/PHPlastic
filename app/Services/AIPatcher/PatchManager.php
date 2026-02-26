<?php

namespace App\Services\AIPatcher;

use App\Core\Config;
use App\Services\AIPatcher\PolicyGate;

class PatchManager
{
    /**
     * Applica un array di file, dove le chiavi sono path relativi e i valori i contenuti dei file.
     * Restituisce un report sull'esito.
     */
    public function applyFiles(array $files): array
    {
        $baseDir = dirname(__DIR__, 3); // Punta alla root del progetto da /app/Services/AIPatcher
        $report = [
            'success' => true,
            'applied' => [],
            'rejected' => [],
            'errors' => []
        ];

        foreach ($files as $relativePath => $content) {
            if (!PolicyGate::isAllowed($relativePath)) {
                $report['rejected'][] = $relativePath;
                $report['success'] = false;
                $report['errors'][] = "PolicyGate ha respinto la modifica al file: {$relativePath}";
                continue;
            }

            $absolutePath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $dir = dirname($absolutePath);

            try {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Scrittura brutale completa (per moduli creati da zero)
                // In una futura iterazione avanzata, potremmo far elaborare a PatchManager veri file .diff 
                // tramite librerie come xdiff o patching basato su chunk per il momento per il Wizard 
                // è richiesto lo scaffolding intero dei file.
                $result = file_put_contents($absolutePath, $content);

                if ($result !== false) {
                    $report['applied'][] = $relativePath;
                } else {
                    $report['success'] = false;
                    $report['errors'][] = "Impossibile scrivere il file: {$relativePath}";
                }
            } catch (\Exception $e) {
                $report['success'] = false;
                $report['errors'][] = "Eccezione durante la scrittura di {$relativePath}: " . $e->getMessage();
            }
        }

        return $report;
    }

    /**
     * Applica le query SQL strutturali al Database
     */
    public function applyMigrations(array $sqlQueries): array
    {
        $report = ['success' => true, 'applied' => [], 'errors' => []];

        if (empty($sqlQueries)) {
            return $report;
        }

        try {
            $db = \App\Core\DB::getInstance();
            $db->beginTransaction();

            foreach ($sqlQueries as $query) {
                // Esecuzione secca senza parametri per DDL
                $db->exec($query);
                $report['applied'][] = "Query SQL eseguita con successo.";
            }

            $db->commit();
        } catch (\Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            $report['success'] = false;
            $report['errors'][] = "Errore critico DB (Rollback eseguito): " . $e->getMessage();
        }

        return $report;
    }
}
