<?php

declare(strict_types=1);

/**
 * Glavna knjižnica simbolov
 * 
 * @package Synera\Core
 */

namespace Synera\Core;

class KnjiznicaSimbolov
{
    private array $simboli = [];
    private AnalizatorFrekvenc $analizatorFrekvenc;
    
    public function __construct()
    {
        $this->analizatorFrekvenc = new AnalizatorFrekvenc();
        $this->naloziVseSimbole();
    }
    
    public function naloziVseSimbole(): void
    {
        $this->naloziRune();
        $this->naloziMantre();
        $this->naloziSvetoGeometrijo();
        $this->naloziKristale();
    }
    
    private function naloziRune(): void
    {
        $this->simboli['rune'] = [
            new \Synera\Model\Runa(
                'Fehu',
                'Prva runa, bogastvo in moc',
                'Starejsi_Futhark',
                [
                    'element' => 'ogenj',
                    'frekvenca' => 396,
                    'pomen' => 'Posvetno bogastvo, energija, kreativna moc'
                ]
            ),
            new \Synera\Model\Runa(
                'Uruz',
                'Divji bik, primalna moc',
                'Starejsi_Futhark',
                [
                    'element' => 'zemlja',
                    'frekvenca' => 417,
                    'pomen' => 'Zdravje, vitalnost, primalna moc'
                ]
            )
        ];
    }
    
    public function pridobiVseRune(): array
    {
        return $this->simboli['rune'] ?? [];
    }
}