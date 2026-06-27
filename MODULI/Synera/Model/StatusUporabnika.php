<?php

declare(strict_types=1);

/**
 * Enum za status uporabnika
 * 
 * @package Synera\Model
 */

namespace Synera\Model;

enum StatusUporabnika: int
{
    case S0_GOST = 0;
    case S1_REGISTRIRAN_OSNOVNI = 1;
    case S2_POTRJEN_UPORABNIK = 2;
    case S3_NAPREDNI_UPORABNIK = 3;
    case S4_VIP = 4;
    case S5_ADMIN = 5;
    
    public function lahkoNadgradiNa(StatusUporabnika $novStatus): bool
    {
        return $novStatus->value > $this->value;
    }
    
    public function pridobiRavenDostopa(): int
    {
        return $this->value;
    }
}