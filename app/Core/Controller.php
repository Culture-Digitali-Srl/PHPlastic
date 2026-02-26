<?php

namespace App\Core;

/**
 * Controller astratto di base opzionale che offre metodi di appoggio (es: jsonResponse)
 */
abstract class Controller
{
    /**
     * Helper per restituire stringhe JSON formattate
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
