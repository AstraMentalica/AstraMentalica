<?php
/**
 * Razred za delo s podatkovno bazo
 * Osnovne operacije za povezavo in poizvedbe
 */
namespace Stelaris\Jedro;

class ApiOdjemalec {
    public function pridobiPoložajePlanetov(\DateTime $datum, string $kraj): array {
        // Simulacija podatkov - v praksi API klic
        return [
            'Sonce' => ['dolžina' => 120.5, 'znamenje' => 'Devica'],
            'Luna' => ['dolžina' => 45.2, 'znamenje' => 'Rak'],
            'Merkur' => ['dolžina' => 135.8, 'znamenje' => 'Tehtnica']
        ];
    }
}