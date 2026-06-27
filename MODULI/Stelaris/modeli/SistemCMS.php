<?php
namespace Stelaris\Modeli;

class SistemCMS {
    private \Stelaris\Jedro\PodatkovnaBaza $baza;
    
    public function __construct() {
        $this->baza = new \Stelaris\Jedro\PodatkovnaBaza();
    }
    
    public function pridobiDnevniHoroskop(string $znamenje): array {
        return $this->baza->poisci('horoskopi', [
            'znamenje' => $znamenje,
            'datum' => date('Y-m-d')
        ]);
    }
    
    public function shraniClanek(string $naslov, string $vsebina, array $oznake): int {
        return $this->baza->vstavi('clanki', [
            'naslov' => $naslov,
            'vsebina' => $vsebina,
            'oznake' => json_encode($oznake),
            'datum' => date('Y-m-d H:i:s')
        ]);
    }
}