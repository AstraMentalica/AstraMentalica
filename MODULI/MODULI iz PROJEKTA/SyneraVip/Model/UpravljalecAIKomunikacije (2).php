<?php

declare(strict_types=1);

/**
 * Model zvočne kopeli
 * 
 * @package Synera\Model
 */

namespace Synera\Model;

class ZvocnaKopel
{
    private array $frekvence;
    private array $binauralniVzorci;
    private string $vrstaTerapije;
    private int $trajanje;
    
    public function __construct(
        array $frekvence,
        array $binauralniVzorci,
        string $vrstaTerapije,
        int $trajanje
    ) {
        $this->frekvence = $frekvence;
        $this->binauralniVzorci = $binauralniVzorci;
        $this->vrstaTerapije = $vrstaTerapije;
        $this->trajanje = $trajanje;
    }
    
    public function predvajaj(): void
    {
        $this->inicializirajZvocniMotor();
        $this->aplicirajFrekvencnoModulacijo();
        $this->izvediTerapijskoZaporedje();
    }
    
    private function inicializirajZvocniMotor(): void
    {
        // Inicializacija zvočnega sistema
    }
    
    private function aplicirajFrekvencnoModulacijo(): void
    {
        // Aplikacija frekvenčne modulacije
    }
    
    private function izvediTerapijskoZaporedje(): void
    {
        // Izvedba terapijskega zaporedja
    }
    
    public function pridobiTrajanje(): int
    {
        return $this->trajanje;
    }
    
    public function pridobiVrstoTerapije(): string
    {
        return $this->vrstaTerapije;
    }
}