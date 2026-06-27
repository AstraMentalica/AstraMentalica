<?php

declare(strict_types=1);

/**
 * Upravljalec uporabnikov
 * 
 * @package Synera\Core
 */

namespace Synera\Core;

class UpravljalecUporabnikov
{
    private array $sejeUporabnikov = [];
    
    public function avtenticirajUporabnika(string $idSeje): ?Uporabnik
    {
        return $this->sejeUporabnikov[$idSeje] ?? null;
    }
    
    public function dodajSejo(string $idSeje, Uporabnik $uporabnik): void
    {
        $this->sejeUporabnikov[$idSeje] = $uporabnik;
    }
}