<?php
/**
 * Glavni API krmilnik za Stelaris astrologijo
 * Obdelava vseh API zahtevkov
 */
namespace Stelaris\Krmilniki;

class ApiKrmilnik {
    public function obdelajZahtevo(): void {
        $dejanje = $_GET['dejanje'] ?? '';
        
        switch ($dejanje) {
            case 'horoskop':
                $this->vrniHoroskop();
                break;
            case 'natalna-karta':
                $this->ustvariNatalnoKarto();
                break;
            default:
                $this->vrniNapako('Neznano dejanje');
        }
    }
    
    private function vrniHoroskop(): void {
        $znamenje = $_GET['znamenje'] ?? 'Oven';
        $generator = new \Stelaris\Modeli\GeneratorAI();
        $horoskop = $generator->generirajDnevniHoroskop($znamenje);
        
        $this->vrniUspeh([
            'znamenje' => $znamenje,
            'datum' => date('Y-m-d'),
            'horoskop' => $horoskop
        ]);
    }
    
    private function ustvariNatalnoKarto(): void {
        $datum = $_POST['datum_rojstva'] ?? '';
        $kraj = $_POST['kraj_rojstva'] ?? '';
        
        if (empty($datum) || empty($kraj)) {
            $this->vrniNapako('Manjkajo podatki');
            return;
        }
        
        try {
            $natalnaKarta = new \Stelaris\Modeli\NatalnaKarta();
            $karta = $natalnaKarta->ustvari(new \DateTime($datum), $kraj);
            
            $this->vrniUspeh([
                'natalna_karta' => $karta,
                'analiza' => (new \Stelaris\Modeli\GeneratorAI())->generirajNatalnoAnalizo($karta)
            ]);
        } catch (\Exception $e) {
            $this->vrniNapako($e->getMessage());
        }
    }
    
    private function vrniUspeh(array $podatki): void {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'uspeh', 'podatki' => $podatki]);
    }
    
    private function vrniNapako(string $sporocilo): void {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['status' => 'napaka', 'sporocilo' => $sporocilo]);
    }
}