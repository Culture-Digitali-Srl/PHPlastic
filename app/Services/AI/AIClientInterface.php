<?php

namespace App\Services\AI;

interface AIClientInterface
{
    /**
     * Invia un prompt chat e restituisce la risposta.
     * 
     * @param array $messages Array di messaggi in formato standard [{"role": "user", "content": "..."}]
     * @param string $model Modello da utilizzare
     * @param array $options Opzioni addizionali (temperature, max_tokens, etc)
     * @param string|null $featureTag Tag della feature consumante per tracciamento costi
     * @return string Risposta testo puro (content)
     */
    public function chat(array $messages, string $model, array $options = [], ?string $featureTag = null): string;
}
