<?php
/**
 * Razred za delo z natalnimi kartami
 * Izračuni in analize natalne astrologije
 */
namespace Stelaris\Modeli;

class NatalnaKarta {
    private \Stelaris\Jedro\ApiOdjemalec $apiOdjemalec;
    private \Stelaris\Jedro\PodatkovnaBaza $baza;
    
    public function __construct() {
        $this->apiOdjemalec = new \Stelaris\Jedro\ApiOdjemalec();
        $this->baza = new \Stelaris\Jedro\PodatkovnaBaza();
    }
    
    public function ustvari(\DateTime $datumRojstva, string $krajRojstva): array {
        $položaji = $this->apiOdjemalec->pridobiPoložajePlanetov($datumRojstva, $krajRojstva);
        $aspekti = $this->analizirajAspekte($položaji);
        
        return [
            'položaji' => $položaji,
            'aspekti' => $aspekti,
            'sončno_znamenje' => $this->dolociSončnoZnamenje($datumRojstva)
        ];
    }
    
    private function analizirajAspekte(array $položaji): array {
        $aspekti = [];
        // Logika za analizo aspektov
        return $aspekti;
    }
    
    private function dolociSončnoZnamenje(\DateTime $datum): string {
        $mesec = (int)$datum->format('n');
        $dan = (int)$datum->format('j');
        
        $znamenja = [
            ['21-03', '20-04', 'Oven'],
            ['21-04', '20-05', 'Bik'],
            ['21-05', '20-06', 'Dvojčka'],
            ['21-06', '22-07', 'Rak'],
            ['23-07', '22-08', 'Lev'],
            ['23-08', '22-09', 'Devica'],
            ['23-09', '22-10', 'Tehtnica'],
            ['23-10', '22-11', 'Skorpijon'],
            ['23-11', '21-12', 'Strelec'],
            ['22-12', '20-01', 'Kozorog'],
            ['21-01', '19-02', 'Vodnar'],
            ['20-02', '20-03', 'Ribe']
        ];
        
        foreach ($znamenja as $z) {
            list($zacetek, $konec, $ime) = $z;
            if ($this->jeVDatumu($mesec, $dan, $zacetek, $konec)) {
                return $ime;
            }
        }
        return 'Oven';
    }
    
    private function jeVDatumu(int $mesec, int $dan, string $zacetek, string $konec): bool {
        list($z_dan, $z_mesec) = explode('-', $zacetek);
        list($k_dan, $k_mesec) = explode('-', $konec);
        return ($mesec == $z_mesec && $dan >= $z_dan) || ($mesec == $k_mesec && $dan <= $k_dan);
    }
}