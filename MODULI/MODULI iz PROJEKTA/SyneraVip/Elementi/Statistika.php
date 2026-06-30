<?php
declare(strict_types=1);

/**
 * Statistika.php - Element za prikaz statističnih podatkov
 * Elementi/ mapa
 */

class Statistika {
    
    public static function prikazi(): string {
        $statistika = SyneraFunkcije::pridobiStatistiko();
        $povezave = SyneraFunkcije::preveriPovezave();
        
        $html = '<div class="element-statistika">';
        $html .= '<h3 style="color: #4FC3F7; margin-bottom: 2rem; text-align: center;">📈 Statistika Sistemov</h3>';
        
        // Glavna statistika
        $html .= '<div class="statistika-okvir">';
        $html .= '<div class="statistika-vrednosti">';
        
        $vrednosti = [
            '🔄' => ['Zagonov', $statistika['zagonov']],
            '🔮' => ['Analiz', $statistika['analiz']],
            '🛡️' => ['Sigilov', $statistika['sigilov']],
            '👥' => ['Uporabnikov', $statistika['uporabnikov']],
            '💻' => ['Aktivnih sej', $statistika['aktivnih_sej']],
            '⚡' => ['Povprečni odziv', $statistika['povprecni_odziv']]
        ];
        
        foreach ($vrednosti as $ikona => $podatki) {
            $html .= "
            <div class='statistika-element'>
                <div class='statistika-ikona'>{$ikona}</div>
                <div class='statistika-podatki'>
                    <div class='statistika-vrednost'>{$podatki[1]}</div>
                    <div class='statistika-opis'>{$podatki[0]}</div>
                </div>
            </div>";
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Povezave
        $html .= '<div class="povezave-okvir" style="margin-top: 2rem;">';
        $html .= '<h4 style="color: #4FC3F7; margin-bottom: 1rem;">🌐 Stanje Povezav</h4>';
        $html .= '<div class="povezave-lista">';
        
        foreach ($povezave as $ime => $podatki) {
            $statusBarva = $podatki['status'] === '🟢' ? '#4CAF50' : '#FF9800';
            $html .= "
            <div class='povezava-vrstica'>
                <span class='povezava-status' style='color: {$statusBarva};'>{$podatki['status']}</span>
                <span class='povezava-ime'>" . ucfirst(str_replace('_', ' ', $ime)) . "</span>
                <span class='povezava-opis'>{$podatki['opis']}</span>
                <span class='povezava-odziv'>{$podatki['odziv']}</span>
            </div>";
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Zadnja aktivnost
        $html .= '<div class="zadnja-aktivnost" style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 10px; text-align: center;">';
        $html .= '<small style="opacity: 0.7;">Zadnja aktivnost: ' . $statistika['zadnja_aktivnost'] . '</small>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        // CSS stili
        $html .= "
        <style>
            .statistika-okvir {
                background: rgba(255,255,255,0.05);
                padding: 1.5rem;
                border-radius: 15px;
                border: 1px solid rgba(255,255,255,0.1);
            }
            
            .statistika-vrednosti {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }
            
            .statistika-element {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1rem;
                background: rgba(255,255,255,0.08);
                border-radius: 10px;
                transition: transform 0.3s;
            }
            
            .statistika-element:hover {
                transform: translateY(-2px);
                background: rgba(255,255,255,0.12);
            }
            
            .statistika-ikona {
                font-size: 2rem;
            }
            
            .statistika-vrednost {
                font-size: 1.5rem;
                font-weight: bold;
                color: #4FC3F7;
            }
            
            .statistika-opis {
                opacity: 0.8;
                font-size: 0.9rem;
            }
            
            .povezave-lista {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .povezava-vrstica {
                display: grid;
                grid-template-columns: 40px 1fr 2fr 80px;
                gap: 1rem;
                align-items: center;
                padding: 0.8rem;
                background: rgba(255,255,255,0.05);
                border-radius: 8px;
            }
            
            .povezava-status {
                font-weight: bold;
            }
            
            .povezava-ime {
                font-weight: 500;
            }
            
            .povezava-opis {
                opacity: 0.8;
                font-size: 0.9rem;
            }
            
            .povezava-odziv {
                text-align: right;
                font-family: monospace;
                color: #4FC3F7;
            }
        </style>";
        
        return $html;
    }
    
    public static function pridobiPodatke(): array {
        return [
            'statistika' => SyneraFunkcije::pridobiStatistiko(),
            'povezave' => SyneraFunkcije::preveriPovezave(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
?>