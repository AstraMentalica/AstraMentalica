<?php

declare(strict_types=1);

/**
 * Model uporabnika
 * 
 * @package Synera\Model
 */

namespace Synera\Model;

class Uporabnik
{
    private int $id;
    private string $uporabniskoIme;
    private string $eposta;
    private StatusUporabnika $status;
    private \DateTime $ustvarjen;
    
    public function __construct(
        string $uporabniskoIme,
        string $eposta,
        StatusUporabnika $status = StatusUporabnika::S0_GOST
    ) {
        $this->uporabniskoIme = $uporabniskoIme;
        $this->eposta = $eposta;
        $this->status = $status;
        $this->ustvarjen = new \DateTime();
    }
    
    public function nadgradiStatus(StatusUporabnika $novStatus): bool
    {
        if ($this->status->lahkoNadgradiNa($novStatus)) {
            $this->status = $novStatus;
            return true;
        }
        
        return false;
    }
    
    public function pridobiRavenDostopa(): int
    {
        return $this->status->pridobiRavenDostopa();
    }
    
    public function pridobiUporabniskoIme(): string
    {
        return $this->uporabniskoIme;
    }
    
    public function pridobiStatus(): StatusUporabnika
    {
        return $this->status;
    }
}