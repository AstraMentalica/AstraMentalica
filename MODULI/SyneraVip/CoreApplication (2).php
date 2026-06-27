<?php

declare(strict_types=1);

/**
 * Glavna aplikacija Synera
 * 
 * @package Synera\Core
 * @author Synera Ekipa
 */

namespace Synera\Core;

class GlavnaAplikacija
{
    private UpravljalecPodatkovneBaze $upravljalecPodatkovneBaze;
    private UpravljalecUporabnikov $upravljalecUporabnikov;
    private KnjiznicaSimbolov $knjiznicaSimbolov;
    
    public function __construct()
    {
        $this->inicializirajAplikacijo();
    }
    
    private function inicializirajAplikacijo(): void
    {
        $this->upravljalecPodatkovneBaze = new UpravljalecPodatkovneBaze();
        $this->upravljalecUporabnikov = new UpravljalecUporabnikov();
        $this->knjiznicaSimbolov = new KnjiznicaSimbolov();
    }
    
    public function zaženi(): void
    {
        $this->obdelajZahtevek();
    }
    
    private function obdelajZahtevek(): void
    {
        // Glavna logika aplikacije
    }
}