<?php

declare(strict_types=1);

/**
 * Model Merkaba zvezde
 * 
 * @package Synera\Model
 */

namespace Synera\Model;

class MerkabaZvezda extends SvetiGeometrijskiVzorec
{
    private array $poljaTetraedrov;
    private float $frekvencaRotacije;
    
    public function __construct()
    {
        parent::__construct('Merkaba', 'Duhovno telo svetlobe');
        $this->inicializirajPoljaTetraedrov();
    }
    
    private function inicializirajPoljaTetraedrov(): void
    {
        $this->poljaTetraedrov = [
            'moski_tetraeder' => [
                'smer' => 'v_smeri_ure',
                'element' => 'ogenj',
                'primarna_frekvenca' => 528
            ],
            'zenski_tetraeder' => [
                'smer' => 'proti_uri',
                'element' => 'voda',
                'primarna_frekvenca' => 432
            ]
        ];
    }
    
    public function izracunajEnergetskiVzorec(): array
    {
        return [
            'merkaba_polje' => $this->izracunajMerkabaPolje(),
            'aktivacija_telesa_svetlobe' => $this->izracunajAktivacijoTelesaSvetlobe()
        ];
    }
    
    public function generirajAktivacijskoZaporedje(array $energijaUporabnika): array
    {
        return [
            'faza_1' => $this->aktivirajMoskiTetraeder($energijaUporabnika),
            'faza_2' => $this->aktivirajZenskiTetraeder($energijaUporabnika),
            'faza_3' => $this->sinhronizirajRotacijo($energijaUporabnika)
        ];
    }
    
    private function aktivirajMoskiTetraeder(array $energijaUporabnika): array
    {
        return [
            'korak' => 'Aktivacija moskega tetraedra',
            'trajanje' => 300,
            'frekvence' => [528, 396]
        ];
    }
}