<?php

declare(strict_types=1);

/**
 * Generator sigilov
 * 
 * @package Synera\Core
 */

namespace Synera\Core;

class GeneratorSigilov
{
    private array $energetskiVzorci = [];
    private array $predlogeSimbolov = [];
    
    public function generirajOsebniSigil(
        string $namen,
        array $energetskiPodpisUporabnika,
        string $slog = 'moderen'
    ): OsebniSigil {
        $kodiranNamen = $this->kodirajNamen($namen);
        $energetskaMatrika = $this->ustvariEnergetskoMatriko($kodiranNamen, $energetskiPodpisUporabnika);
        $geometrijaSigila = $this->generirajGeometrijo($energetskaMatrika, $slog);
        
        return new OsebniSigil(
            $namen,
            $geometrijaSigila,
            $energetskaMatrika,
            $this->izracunajAktivacijskeFrekvence($energetskaMatrika)
        );
    }
    
    private function kodirajNamen(string $namen): array
    {
        $besede = explode(' ', strtolower($namen));
        $kodirano = [];
        
        foreach ($besede as $beseda) {
            $kodirano[] = [
                'numerologija' => $this->izracunajNumerologijoBesede($beseda),
                'frekvencni_vzorec' => $this->besedaVFrekvenco($beseda),
                'simbolicne_asociacije' => $this->poisciSimbolicneAsociacije($beseda)
            ];
        }
        
        return $kodirano;
    }
    
    private function izracunajNumerologijoBesede(string $beseda): int
    {
        return strlen($beseda);
    }
}