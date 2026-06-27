<?php

declare(strict_types=1);

/**
 * Sistem frekvenc in zvočnih kopeli
 * 
 * @package Synera\Core
 */

namespace Synera\Core;

class SistemFrekvenc
{
    private array $knjiznicaFrekvenc = [];
    private GeneratorBinauralnihTonov $generatorBinauralnihTonov;
    
    public function __construct()
    {
        $this->generatorBinauralnihTonov = new GeneratorBinauralnihTonov();
        $this->naloziKnjiznicoFrekvenc();
    }
    
    public function ustvariZvocnoKopel(
        array $ciljneFrekvence,
        string $vrstaTerapije,
        int $trajanje = 1800
    ): ZvocnaKopel {
        $osnovneFrekvence = $this->izberiOsnovneFrekvence($ciljneFrekvence, $vrstaTerapije);
        $binauralniVzorci = $this->generatorBinauralnihTonov->generirajTerapijskiVzorec(
            $osnovneFrekvence,
            $trajanje
        );
        
        return new ZvocnaKopel(
            $osnovneFrekvence,
            $binauralniVzorci,
            $vrstaTerapije,
            $trajanje
        );
    }
    
    private function izberiOsnovneFrekvence(array $ciljneFrekvence, string $vrstaTerapije): array
    {
        return [
            'osnovna_frekvenca' => 432.0,
            'terapijske_frekvence' => $ciljneFrekvence,
            'harmoniki' => $this->izracunajHarmonike($ciljneFrekvence)
        ];
    }
}