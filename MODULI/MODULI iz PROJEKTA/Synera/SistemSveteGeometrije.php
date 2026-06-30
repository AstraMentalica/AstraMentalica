<?php

declare(strict_types=1);

/**
 * Sistem svete geometrije
 * 
 * @package Synera\Core
 */

namespace Synera\Core;

class SistemSvoteGeometrije
{
    private array $geometrijskiVzorci = [];
    
    public function __construct()
    {
        $this->inicializirajSvoteVzorce();
    }
    
    private function inicializirajSvoteVzorce(): void
    {
        $this->geometrijskiVzorci = [
            'merkaba' => new MerkabaZvezda(),
            'cvet_zivljenja' => new CvetZivljenja(),
            'metatronova_kocka' => new MetatronovaKocka()
        ];
    }
    
    public function generirajAktivacijskoZaporedje(
        string $imeVzorca,
        array $energijaUporabnika
    ): array {
        $vzorec = $this->geometrijskiVzorci[$imeVzorca] ?? null;
        
        if (!$vzorec) {
            throw new \InvalidArgumentException("Neveljaven geometrijski vzorec: $imeVzorca");
        }
        
        return $vzorec->generirajAktivacijskoZaporedje($energijaUporabnika);
    }
}