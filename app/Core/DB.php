<?php

namespace App\Core;

use PDO;

/**
 * Singleton Pattern per la gestione della connessione Database (PDO).
 * Sicuro contro SQL injection se si usano sempre i prepared statements.
 */
class DB
{
    private static ?PDO $instance = null;

    /**
     * Ritorna l'istanza PDO configurata
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            $dbPath = Config::get('app.db_path', __DIR__ . '/../../storage/app.sqlite');

            // Assicurati che lo storage box esista
            if (!file_exists(dirname($dbPath))) {
                mkdir(dirname($dbPath), 0775, true);
            }

            self::$instance = new PDO("sqlite:{$dbPath}");
            // Abilita eccezioni in caso di errori SQL
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Ritorni fetch standard come array associativi (sicurezza e usability)
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // SQLite pragmas per maggiore robustezza
            self::$instance->exec('PRAGMA foreign_keys = ON;');
            self::$instance->exec('PRAGMA journal_mode = WAL;');
        }

        return self::$instance;
    }

    /**
     * Helper veloce per i prepared statement e fetch o string
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
