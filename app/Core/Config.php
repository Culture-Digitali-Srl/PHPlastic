<?php

namespace App\Core;

/**
 * Gestisce la configurazione dell'applicazione in modo semplice e sicuro.
 * Permette di caricare variabili da un file .env (se presente) e file php in config/.
 */
class Config
{
    private static array $settings = [];
    private static string $version = 'v0.5.0';

    /**
     * Carica le variabili da un file .env semplice
     */
    public static function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Rimuove eventuali apici
                $value = trim($value, '"\'');

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }

    /**
     * Recupera una variabile d'ambiente con fallback
     */
    public static function getEnv(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        if ($value === false) {
            return $_ENV[$key] ?? $default;
        }

        return $value;
    }

    /**
     * Carica un file di configurazione PHP
     */
    public static function load(string $file, array $data): void
    {
        self::$settings[$file] = $data;
    }

    /**
     * Recupera un valore di configurazione (es. 'app.name')
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if ($key === 'app.version') {
            return self::$version;
        }

        $parts = explode('.', $key);
        $file = array_shift($parts);

        if (!isset(self::$settings[$file])) {
            return $default;
        }

        $value = self::$settings[$file];
        foreach ($parts as $part) {
            if (!is_array($value) || !isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }
}
