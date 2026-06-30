<?php

declare(strict_types=1);

/**
 * Upravljalec podatkovne baze
 * 
 * @package Synera\Core
 */

namespace Synera\Core;

class UpravljalecPodatkovneBaze
{
    private \PDO $povezava;
    
    public function __construct()
    {
        $this->vzpostaviPovezavo();
    }
    
    private function vzpostaviPovezavo(): void
    {
        $nizPovezave = 'mysql:host=localhost;dbname=synera;charset=utf8mb4';
        $this->povezava = new \PDO($nizPovezave, 'uporabnisko_ime', 'geslo');
    }
    
    public function pridobiPovezavo(): \PDO
    {
        return $this->povezava;
    }
}