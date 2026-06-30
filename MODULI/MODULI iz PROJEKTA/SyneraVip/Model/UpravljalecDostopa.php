<?php

declare(strict_types=1);

/**
 * Upravljalec dostopov in dovoljenj
 * 
 * @package Synera\Core
 */

namespace Synera\Core;

class UpravljalecDostopa
{
    private array $pravilaDostopa = [];
    
    public function __construct()
    {
        $this->inicializirajPravilaDostopa();
    }
    
    private function inicializirajPravilaDostopa(): void
    {
        $this->pravilaDostopa = [
            \Synera\Model\StatusUporabnika::S0_GOST->value => [
                'knjiznica_simbolov' => ['ogled_javnih'],
                'mantre' => ['poslusanje_vzorcev'],
                'rituali' => ['ogled_osnovnih']
            ],
            \Synera\Model\StatusUporabnika::S1_REGISTRIRAN_OSNOVNI->value => [
                'knjiznica_simbolov' => ['ogled_vseh', 'osnovno_iskanje'],
                'mantre' => ['poslusanje_vseh'],
                'sigili' => ['ustvarjanje_osnovnih']
            ],
            \Synera\Model\StatusUporabnika::S5_ADMIN->value => [
                'vse' => ['dostop_do_celega_sistema', 'upravljanje_uporabnikov']
            ]
        ];
    }
    
    public function preveriDostop(
        \Synera\Model\Uporabnik $uporabnik,
        string $vir,
        string $akcija
    ): bool {
        $statusUporabnika = $uporabnik->pridobiStatus()->value;
        $pravilaUporabnika = $this->pravilaDostopa[$statusUporabnika] ?? [];
        
        if ($statusUporabnika === \Synera\Model\StatusUporabnika::S5_ADMIN->value) {
            return true;
        }
        
        if (isset($pravilaUporabnika[$vir])) {
            return in_array($akcija, $pravilaUporabnika[$vir]);
        }
        
        return false;
    }
}