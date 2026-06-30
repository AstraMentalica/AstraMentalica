<?php

declare(strict_types=1);

/**
 * Osnovni model simbola
 * 
 * @package Synera\Model
 */

namespace Synera\Model;

abstract class Simbol
{
    protected int $id;
    protected string $ime;
    protected string $opis;
    protected array $frekvence;
    protected string $izvor;
    protected array $pomeni;
    
    public function __construct(
        string $ime,
        string $opis,
        array $frekvence = []
    ) {
        $this->ime = $ime;
        $this->opis = $opis;
        $this->frekvence = $frekvence;
    }
    
    abstract public function pridobiVrstoSimbola(): string;
    abstract public function izracunajEnergetskiVzorec(): array;
    
    public function pridobiIme(): string
    {
        return $this->ime;
    }
    
    public function pridobiOpis(): string
    {
        return $this->opis;
    }
}