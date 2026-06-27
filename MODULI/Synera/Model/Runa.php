<?php

declare(strict_types=1);

/**
 * Model rune
 * 
 * @package Synera\Model
 */

namespace Synera\Model;

class Runa extends Simbol
{
    private string $vrstaFutharka;
    private array $galdrAsociacije;
    private array $moderneInterpretacije;
    
    public function __construct(
        string $ime,
        string $opis,
        string $vrstaFutharka,
        array $galdrAsociacije = []
    ) {
        parent::__construct($ime, $opis);
        $this->vrstaFutharka = $vrstaFutharka;
        $this->galdrAsociacije = $galdrAsociacije;
    }
    
    public function pridobiVrstoSimbola(): string
    {
        return 'runa';
    }
    
    public function izracunajEnergetskiVzorec(): array
    {
        return [
            'primarna_frekvenca' => $this->izracunajPrimarnoFrekvenco(),
            'harmonicni_vzorci' => $this->poisciHarmonicneVzorce(),
            'tok_energije' => $this->analizirajTokEnergije()
        ];
    }
    
    private function izracunajPrimarnoFrekvenco(): float
    {
        return 432.0;
    }
    
    private function poisciHarmonicneVzorce(): array
    {
        return [];
    }
    
    private function analizirajTokEnergije(): array
    {
        return [];
    }
}