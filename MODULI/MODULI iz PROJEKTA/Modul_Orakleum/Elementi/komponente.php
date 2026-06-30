<?php
/**
 * Orakleum Komponente
 * Datoteka: Elementi/komponente.php
 * Namen: Ponovno uporabljive komponente za Orakleum modul
 */

// Preveri direktni dostop
si_preveri_direktni_dostop();

/**
 * Klasa za upravljanje s komponentami
 */
class OrakleumKomponente {
    
    /**
     * HTML komponenta za prikaz kartice
     */
    public static function renderKarta($karta, $pozicija = null, $velikost = 'normalna') {
        $velikost_class = self::getVelikostClass($velikost);
        $pozicija_html = $pozicija ? '<div class="karta-pozicija">' . htmlspecialchars($pozicija) . '</div>' : '';
        
        return '
        <div class="oracle-karta ' . $velikost_class . '">
            ' . $pozicija_html . '
            <div class="karta-simbol">' . htmlspecialchars($karta['simbol']) . '</div>
            <div class="karta-ime">' . htmlspecialchars($karta['ime']) . '</div>
            <div class="karta-opis">' . htmlspecialchars($karta['opis']) . '</div>
            ' . self::renderKartaMetapodatki($karta) . '
        </div>';
    }
    
    /**
     * HTML komponenta za orakelj
     */
    public static function renderOrakelj($orakelj, $stil = 'klasičen') {
        $stil_class = 'orakelj-' . $stil;
        
        $kartice_html = '';
        foreach ($orakelj['kartice'] as $kartica_info) {
            $kartice_html .= self::renderKarta($kartica_info['karta'], $kartica_info['pozicija'], 'majhna');
        }
        
        return '
        <div class="oracle-orakelj ' . $stil_class . '">
            <div class="orakelj-header">
                <h3 class="orakjel-naslov">' . htmlspecialchars($orakelj['tip']) . '</h3>
                <div class="orakelj-meta">
                    <span class="orakelj-stevilo-kart">' . count($orakelj['kartice']) . ' kart</span>
                    <span class="orakelj-cas">' . $orakelj['cas'] . '</span>
                </div>
            </div>
            <div class="orakelj-kartice">
                ' . $kartice_html . '
            </div>
            ' . self::renderOrakeljSkupnaInterpretacija($orakjel) . '
        </div>';
    }
    
    /**
     * HTML komponenta za interpretacijo
     */
    public static function renderInterpretacija($interpretacija, $detaljno = true) {
        $detajli_html = '';
        if ($detaljno) {
            $detajli_html = '
            <div class="interpretacija-detajli">
                <div class="interpretacija-pozicija">
                    <strong>Pozicija:</strong> ' . htmlspecialchars($interpretacija['pozicija']) . '
                </div>
                <div class="interpretacija-element">
                    <strong>Element:</strong> ' . htmlspecialchars($interpretacija['karta']['element']) . '
                </div>
                <div class="interpretacija-energija">
                    <strong>Energija:</strong> ' . htmlspecialchars($interpretacija['karta']['energija']) . '
                </div>
            </div>';
        }
        
        $napotki_html = '';
        if (!empty($interpretacija['napotki'])) {
            $napotki_html = '<div class="interpretacija-napotki"><h4>🔮 Napotki:</h4><ul>';
            foreach ($interpretacija['napotki'] as $napotek) {
                $napotki_html .= '<li>' . htmlspecialchars($napotek) . '</li>';
            }
            $napotki_html .= '</ul></div>';
        }
        
        return '
        <div class="oracle-interpretacija">
            <div class="interpretacija-karta">
                ' . self::renderKarta($interpretacija['karta'], $interpretacija['pozicija'], 'majhna') . '
            </div>
            <div class="interpretacija-vsebina">
                <div class="interpretacija-text">
                    ' . nl2br(htmlspecialchars($interpretacija['glavna_interpretacija'])) . '
                </div>
                ' . $detajli_html . '
                ' . $napotki_html . '
            </div>
        </div>';
    }
    
    /**
     * HTML komponenta za navigacijo
     */
    public static function renderNavigacija($trenutna_stran, $mozne_strani) {
        $nav_items = '';
        foreach ($mozne_strani as $stran => $podatki) {
            $active_class = ($stran === $trenutna_stran) ? ' active' : '';
            $nav_items .= '<li class="nav-item' . $active_class . '"><a href="?stran=' . urlencode($stran) . '" class="nav-link">' . htmlspecialchars($podatki['ime']) . '</a></li>';
        }
        
        return '
        <nav class="oracle-navigacija">
            <ul class="nav-list">
                ' . $nav_items . '
            </ul>
        </nav>';
    }
    
    /**
     * HTML komponenta za iskanje
     */
    public static function renderIskanje($trenutno_iskanje = '') {
        return '
        <div class="oracle-iskalnjak">
            <input type="text" 
                   class="iskalnjak-input" 
                   placeholder="Iščite karte..." 
                   value="' . htmlspecialchars($trenutno_iskanje) . '"
                   id="oracle-search">
            <button class="iskalnjak-gumb" onclick="oracleIskanje()">🔍</button>
        </div>';
    }
    
    /**
     * HTML komponenta za statistike
     */
    public static function renderStatistike($statistike, $tip = 'kartice') {
        switch ($tip) {
            case 'kartice':
                return self::renderStatistikeKart($statistike);
            case 'orakelji':
                return self::renderStatistikeOrakelji($statistike);
            default:
                return self::renderStatistikeSplosne($statistike);
        }
    }
    
    /**
     * JavaScript komponente
     */
    public static function getJavaScript() {
        return '
        <script>
        // Oracle komponente JavaScript
        
        function oracleIskanje() {
            const iskalni_pojem = document.getElementById("oracle-search").value;
            // Implementiraj logiko iskanja
            console.log("Iskanje:", iskalni_pojem);
        }
        
        function vleciKartoAPI(pozicija) {
            fetch("/api/oracle/vleci", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    pozicija: pozicija,
                    vprasanje: document.getElementById("vprasanje").value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.uspeh) {
                    prikaziKarto(data.karta);
                } else {
                    prikaziNapako(data.napaka);
                }
            })
            .catch(error => {
                console.error("Napaka:", error);
                prikaziNapako("Napaka pri komunikaciji");
            });
        }
        
        function prikaziKarto(karta) {
            const container = document.getElementById("rezultat");
            container.innerHTML = `
                <div class="oracle-karta prikaz">
                    <div class="karta-simbol">${karta.simbol}</div>
                    <div class="karta-ime">${karta.ime}</div>
                    <div class="karta-opis">${karta.opis}</div>
                </div>
            `;
            container.style.display = "block";
        }
        
        function prikaziNapako(sporocilo) {
            const container = document.getElementById("rezultat");
            container.innerHTML = `
                <div class="oracle-napaka">
                    ❌ ${sporocilo}
                </div>
            `;
            container.style.display = "block";
        }
        
        // CSS stili za komponente
        </script>';
    }
    
    /**
     * Pomožne metode
     */
    private static function getVelikostClass($velikost) {
        $velikosti = [
            'majhna' => 'velikost-majhna',
            'normalna' => 'velikost-normalna',
            'velika' => 'velikost-velika'
        ];
        
        return $velikosti[$velikost] ?? 'velikost-normalna';
    }
    
    private static function renderKartaMetapodatki($karta) {
        $metapodatki = [];
        
        if (isset($karta['element'])) {
            $metapodatki[] = '<span class="meta-element">' . htmlspecialchars($karta['element']) . '</span>';
        }
        
        if (isset($karta['energija'])) {
            $metapodatki[] = '<span class="meta-energija">' . htmlspecialchars($karta['energija']) . '</span>';
        }
        
        if (isset($karta['frekvenca'])) {
            $metapodatki[] = '<span class="meta-frekvenca">' . $karta['frekvenca'] . ' Hz</span>';
        }
        
        return empty($metapodatki) ? '' : '<div class="karta-metapodatki">' . implode(' ', $metapodatki) . '</div>';
    }
    
    private static function renderOrakeljSkupnaInterpretacija($orakelj) {
        if (!isset($orakelj['skupna_interpretacija'])) {
            return '';
        }
        
        return '
        <div class="orakelj-skupna-interpretacija">
            <h4>Celotna Interpretacija</h4>
            <p>' . nl2br(htmlspecialchars($orakelj['skupna_interpretacija']['skupaj_pomens'])) . '</p>
        </div>';
    }
    
    private static function renderStatistikeKart($statistike) {
        $html = '<div class="oracle-statistike-kart">';
        $html .= '<h4>📊 Statistike Kart</h4>';
        
        if (isset($statistike['najbolj_vlecena'])) {
            $html .= '<div class="statistika-item">';
            $html .= '<strong>Najbolj vlečena:</strong> ' . htmlspecialchars($statistike['najbolj_vlecena']);
            $html .= '</div>';
        }
        
        if (isset($statistike['skupno_vlecenj'])) {
            $html .= '<div class="statistika-item">';
            $html .= '<strong>Skupaj vlecenj:</strong> ' . $statistike['skupno_vlecenj'];
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }
    
    private static function renderStatistikeOrakelji($statistike) {
        $html = '<div class="oracle-statistike-orakelji">';
        $html .= '<h4>🔮 Statistike Orakljev</h4>';
        
        if (isset($statistike['tipi_orakljev'])) {
            foreach ($statistike['tipi_orakljev'] as $tip => $stevilo) {
                $html .= '<div class="statistika-item">';
                $html .= '<strong>' . htmlspecialchars($tip) . ':</strong> ' . $stevilo;
                $html .= '</div>';
            }
        }
        
        $html .= '</div>';
        return $html;
    }
    
    private static function renderStatistikeSplosne($statistike) {
        $html = '<div class="oracle-statistike-splosne">';
        $html .= '<h4>📈 Splošne Statistike</h4>';
        
        foreach ($statistike as $kljuc => $vrednost) {
            $html .= '<div class="statistika-item">';
            $html .= '<strong>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $kljuc))) . ':</strong> ' . $vrednost;
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }
}

/**
 * CSS stili za komponente
 */
function oracle_getKomponenteCSS() {
    return '
    <style>
    /* Oracle komponente CSS */
    .oracle-karta {
        background: linear-gradient(135deg, #f5f5dc, #e6d3a3);
        border: 2px solid #8B4513;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .oracle-karta:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .velikost-majhna { max-width: 200px; padding: 15px; }
    .velikost-normalna { max-width: 300px; padding: 20px; }
    .velikost-velika { max-width: 400px; padding: 25px; }
    
    .karta-simbol { font-size: 3em; margin-bottom: 10px; }
    .velikost-majhna .karta-simbol { font-size: 2em; }
    .velikost-velika .karta-simbol { font-size: 4em; }
    
    .karta-ime {
        font-size: 1.2em;
        font-weight: bold;
        color: #8B4513;
        margin-bottom: 10px;
    }
    
    .karta-opis {
        color: #654321;
        line-height: 1.5;
        margin-bottom: 10px;
    }
    
    .karta-pozicija {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(139, 69, 19, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8em;
    }
    
    .karta-metapodatki {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }
    
    .meta-element, .meta-energija, .meta-frekvenca {
        background: rgba(139, 69, 19, 0.1);
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8em;
        color: #8B4513;
    }
    
    .oracle-orakelj {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        padding: 25px;
        margin: 20px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .orakelj-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #8B4513;
        padding-bottom: 15px;
    }
    
    .orakjel-naslov {
        color: #8B4513;
        margin: 0 0 10px 0;
    }
    
    .orakelj-kartice {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .oracle-interpretacija {
        background: rgba(245, 245, 220, 0.8);
        border-radius: 12px;
        padding: 20px;
        margin: 15px 0;
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    
    .interpretacija-karta {
        flex-shrink: 0;
    }
    
    .interpretacija-vsebina {
        flex: 1;
    }
    
    .interpretacija-text {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #8B4513;
        margin-bottom: 15px;
    }
    
    .interpretacija-detajli {
        background: rgba(139, 69, 19, 0.05);
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .interpretacija-napotki {
        background: rgba(212, 175, 55, 0.1);
        padding: 15px;
        border-radius: 8px;
    }
    
    .interpretacija-napotki ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .oracle-navigacija {
        background: #f5f5dc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .nav-list {
        list-style: none;
        display: flex;
        gap: 15px;
        margin: 0;
        padding: 0;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .nav-item.active .nav-link {
        background: #8B4513;
        color: white;
    }
    
    .nav-link {
        text-decoration: none;
        color: #8B4513;
        padding: 8px 16px;
        border-radius: 20px;
        border: 2px solid #8B4513;
        transition: all 0.3s ease;
        display: block;
    }
    
    .nav-link:hover {
        background: #8B4513;
        color: white;
    }
    
    .oracle-iskalnjak {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: white;
        padding: 10px;
        border-radius: 8px;
        border: 2px solid #8B4513;
    }
    
    .iskalnjak-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 16px;
        padding: 5px;
    }
    
    .iskalnjak-gumb {
        background: #8B4513;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
    }
    
    .iskalnjak-gumb:hover {
        background: #A0522D;
    }
    
    .oracle-statistike-kart,
    .oracle-statistike-orakelji,
    .oracle-statistike-splosne {
        background: rgba(245, 245, 220, 0.8);
        border-radius: 12px;
        padding: 20px;
        margin: 15px 0;
    }
    
    .statistika-item {
        padding: 8px 0;
        border-bottom: 1px solid rgba(139, 69, 19, 0.2);
    }
    
    .statistika-item:last-child {
        border-bottom: none;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .oracle-interpretacija {
            flex-direction: column;
            text-align: center;
        }
        
        .nav-list {
            flex-direction: column;
            align-items: center;
        }
        
        .orakelj-kartice {
            grid-template-columns: 1fr;
        }
    }
    </style>';
}

?>