<?php
/**
 * ============================================================
 * POT: GLOBALNO/vmesnik/elementi/zemljevi.php
 * 📅 VERZIJA: v1.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (vmesnik)
 *
 * 📰 NAMEN:
 *     Zemljevi (svetovni zemljevid) – vizualni sistem za
 *     navigacijo in preklop med moduli.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - zemljevi_prikazi(array $moduli, string $aktivniModul): void
 *
 * 📡 ODVISNOSTI:
 *     - GLOBALNO/vmesnik/css/spremenljivke.css
 *     - GLOBALNO/vmesnik/css/osnova.css
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez direktnih poizvedb v bazo
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🏷️ OZNAKE:
 *     zemljevi, navigacija, moduli, vmesnik
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

/**
 * Prikaže zemljeve (svetovni zemljevid) za navigacijo med moduli.
 *
 * @param array  $moduli        Seznam modulov [['id' => 'Modul_A', 'ime' => 'Ime', 'ikona' => '🎯', 'barva' => '#e8c84a', 'dežela' => 'evropa'], ...]
 * @param string $aktivniModul  ID trenutno aktivnega modula
 * @param string $naZemljevih   URL za preklop (npr. '?svet=MODULI&modul=')
 */
function zemljevi_prikazi(array $moduli, string $aktivniModul = '', string $naZemljevih = '?svet=MODULI&modul='): void
{
    if (empty($moduli)) {
        return;
    }
    
    // Razporedi module v mrežo (4 stolpce)
    $stolpci = 4;
    
    echo '<div class="zemljevi-okvir" id="zemljevi">';
    echo '    <h2 class="zemljevi-naslov">🌍 Svetovni zemljevid</h2>';
    echo '    <p class="zemljevi-podnaslov">Izberi modul za raziskovanje</p>';
    
    // Združi module po deželah
    $modulePoDezelah = [];
    foreach ($moduli as $modul) {
        $dežela = $modul['dežela'] ?? 'specijal';
        if (!isset($modulePoDezelah[$dežela])) {
            $modulePoDezelah[$dežela] = [];
        }
        $modulePoDezelah[$dežela][] = $modul;
    }
    
    // Prikaži vsako deželo z njenimi moduli
    foreach ($modulePoDezelah as $deželaId => $moduliDezele) {
        // Pridobi ime dežele
        $imeDezele = ucfirst($deželaId);
        $ikonaDezele = '🌍';
        
        // Preveri ali je to auth/sistem skupina
        if ($deželaId === 'sistem') {
            $imeDezele = '🔐 Sistem';
            $ikonaDezele = '🔐';
        }
        
        echo '<div class="zemljevi-dezela">';
        echo '    <h3 class="zemljevi-dezela-naslov">' . $ikonaDezele . ' ' . $imeDezele . '</h3>';
        echo '    <div class="zemljevi-mreza">';
        
        // Razporedi module v vrstice
        $vrstice = array_chunk($moduliDezele, $stolpci);
        foreach ($vrstice as $vrstica) {
            foreach ($vrstica as $modul) {
                $id = htmlspecialchars($modul['id'] ?? '');
                $ime = htmlspecialchars($modul['ime'] ?? $id);
                $ikona = htmlspecialchars($modul['ikona'] ?? '📦');
                $barva = htmlspecialchars($modul['barva'] ?? '#e8c84a');
                $opis = htmlspecialchars($modul['opis'] ?? '');
                $aktiven = ($id === $aktivniModul) ? ' zemljevi-kartica-aktivna' : '';
                
                // Uporabi custom URL če je podan, sicer privzeti
                $url = $modul['url'] ?? ($naZemljevih . urlencode($id));
                
                echo '<a href="' . $url . '" class="zemljevi-kartica' . $aktiven . '" ';
                echo 'style="--zemljevi-barva: ' . $barva . ';" ';
                echo 'title="' . $opis . '">';
                echo '    <div class="zemljevi-ikona">' . $ikona . '</div>';
                echo '    <div class="zemljevi-ime">' . $ime . '</div>';
                echo '    <div class="zemljevi-povezava-indikator"></div>';
                echo '</a>';
            }
        }
        
        echo '    </div>';
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Pridobi seznam vseh modulov iz MODULI/ mape.
 *
 * @return array Seznam modulov
 */
function zemljevi_pridobi_module(): array
{
    $moduli = [];
    
    // 1. Auth strani (vedno na vrhu)
    $authModuli = [
        [
            'id' => 'prijava',
            'ime' => 'Prijava',
            'ikona' => '🔑',
            'barva' => '#4caf50',
            'opis' => 'Prijava v sistem',
            'url' => '?svet=UPORABNIKI&pot=prijava',
            'dežela' => 'sistem',
        ],
        [
            'id' => 'registracija',
            'ime' => 'Registracija',
            'ikona' => '📝',
            'barva' => '#5c9be0',
            'opis' => 'Ustvari nov račun',
            'url' => '?svet=UPORABNIKI&pot=registracija',
            'dežela' => 'sistem',
        ],
    ];
    
    $moduli = array_merge($moduli, $authModuli);
    
    // 2. Uporabniške strani
    $userModuli = [
        [
            'id' => 'profil',
            'ime' => 'Moj profil',
            'ikona' => '👤',
            'barva' => '#9c6fe4',
            'opis' => 'Urejanje profila',
            'url' => '?svet=UPORABNIKI&pot=profil',
            'dežela' => 'sistem',
        ],
        [
            'id' => 'nastavitve',
            'ime' => 'Nastavitve',
            'ikona' => '⚙️',
            'barva' => '#ff9800',
            'opis' => 'Nastavitve sistema',
            'url' => '?svet=UPORABNIKI&pot=nastavitve',
            'dežela' => 'sistem',
        ],
        [
            'id' => 'pozabljeno_geslo',
            'ime' => 'Pozabljeno geslo',
            'ikona' => '🔒',
            'barva' => '#f44336',
            'opis' => 'Ponastavitev gesla',
            'url' => '?svet=UPORABNIKI&pot=pozabljeno_geslo',
            'dežela' => 'sistem',
        ],
        [
            'id' => 'ponastavi_geslo',
            'ime' => 'Ponastavi geslo',
            'ikona' => '🔓',
            'barva' => '#4caf50',
            'opis' => 'Nova gesla',
            'url' => '?svet=UPORABNIKI&pot=ponastavi_geslo',
            'dežela' => 'sistem',
        ],
    ];
    
    $moduli = array_merge($moduli, $userModuli);
    
    // 3. Moduli iz MODULI/ mape z deželami
    $moduliPot = defined('MODULI_POT') ? MODULI_POT : __DIR__ . '/../../../../MODULI';
    
    // Mapa modulov po deželah (regijah)
    $dežele = [
        // EVROPA
        'evropa' => [
            'ime' => '🏰 Evropa',
            'ikona' => '🏰',
            'moduli' => ['Aeternum', 'Angelarium', 'Kabbaloria', 'Runaris', 'CodexAntiqua', 'CodexDamiris', 'Labyrinthus', 'Lapidaria', 'Mythologica', 'NordicaMystica', 'Sephirotica', 'Seraphica', 'Tarot'],
        ],
        
        // AFRIKA
        'afrika' => [
            'ime' => '🏜️ Afrika',
            'ikona' => '🏜️',
            'moduli' => ['AegypticaArcana', 'BotanicaSacra', 'Herbarica', 'MedicinaOrientalis'],
        ],
        
        // AZIJA
        'azija' => [
            'ime' => '🐉 Azija',
            'ikona' => '🐉',
            'moduli' => ['Azija', 'Jyotir', 'Sbajixing', 'Sbazi', 'Shanami', 'Shenlong', 'SqiMapper', 'Sreiki', 'Ssekki', 'Sshenlong', 'Sshiatsu', 'SwuXing', 'Syijing', 'SzodiacApi', 'SfenShui'],
        ],
        
        // AMERIKA
        'amerika' => [
            'ime' => '🗿 Amerika',
            'ikona' => '🗿',
            'moduli' => ['MysticaMesoAmericana', 'CodexVerba', 'Oneirotica'],
        ],
        
        // OKCITANIJA - Čarobni svet
        'okcitanija' => [
            'ime' => '🔮 Čarobni svet',
            'ikona' => '🔮',
            'moduli' => ['Aetheris', 'aetheris2', 'AlchymiaAurea', 'Celestara', 'Chakrarium', 'CorpusMysticum', 'CosmicaScientia', 'Crystallum', 'Daemontica', 'Devorum', 'Djotis', 'Energetica', 'GeometricaSacra', 'Hieroglyphicus', 'Lunaris', 'Meditara', 'Mystic', 'Mystica', 'NumerariumCosmicum', 'Numyra', 'Occultum', 'Oracle', 'OraculumVisionis', 'Orakleum', 'Pranaymica', 'QiVitalis', 'QuantumMystica', 'SauraMetrics', 'SchronoSync', 'SenzorNasa', 'Shamanica', 'Sigillaris', 'Skami', 'Skanpo', 'Skijou', 'Sliuren', 'Smakoto', 'Smushin', 'Sneidan', 'SolarniPojavi', 'Somnaris', 'Sonaris', 'Stelar', 'Stelaris', 'Sunmei', 'Swabi', 'Szazen', 'SziWei', 'Transmutaria', 'UmbraeCodex', 'ViaAnimae', 'VibraMystica'],
        ],
        
        // ZNANSTVENI SVET
        'znanstveni' => [
            'ime' => '🔬 Znanstveni svet',
            'ikona' => '🔬',
            'moduli' => ['Animaris', 'BotanicSacra', 'Codex', 'SynchronoSync', 'TemplateModul'],
        ],
        
        // DUHOVNI SVET
        'duhovni' => [
            'ime' => '✨ Duhovni svet',
            'ikona' => '✨',
            'moduli' => ['AuroraMystica', 'LiberUmbrae', 'Modul_Bridge', 'Modul_Mystaia', 'Modul_Orakleum', 'Mystaia', 'Mystaia_modul'],
        ],
        
        // VESOLJE
        'vesolje' => [
            'ime' => '🌌 Vesolje',
            'ikona' => '🌌',
            'moduli' => ['Nasa'],
        ],
        
        // SPEciJAL
        'specijal' => [
            'ime' => '⭐ Specijal',
            'ikona' => '⭐',
            'moduli' => [],
        ],
    ];
    
    if (is_dir($moduliPot)) {
        $mape = scandir($moduliPot);
        
        foreach ($mape as $mapa) {
            if ($mapa === '.' || $mapa === '..' || $mapa === 'desktop.ini') {
                continue;
            }
            
            $polnaPot = $moduliPot . '/' . $mapa;
            if (!is_dir($polnaPot)) {
                continue;
            }
            
            // Preveri, ali ima modul modul.php
            $imaModul = file_exists($polnaPot . '/modul.php') || 
                        file_exists($polnaPot . '/index.php') ||
                        file_exists($polnaPot . '/manifest.json');
            
            if ($imaModul) {
                // Pridobi barvo iz ikone ali uporabi privzeto
                $barva = '#e8c84a'; // Privzeto zlata
                
                // Preveri za custom ikono/barvo
                $manifestPot = $polnaPot . '/manifest.json';
                if (file_exists($manifestPot)) {
                    $manifest = json_decode(file_get_contents($manifestPot), true);
                    if (isset($manifest['barva'])) {
                        $barva = $manifest['barva'];
                    }
                }
                
                // Poišči deželo za ta modul
                $dežela = 'specijal';
                foreach ($dežele as $k => $v) {
                    if (in_array($mapa, $v['moduli'])) {
                        $dežela = $k;
                        break;
                    }
                }
                
                $moduli[] = [
                    'id' => $mapa,
                    'ime' => ucfirst(str_replace(['_', '-'], ' ', $mapa)),
                    'ikona' => '📦',
                    'barva' => $barva,
                    'opis' => 'Modul: ' . ucfirst(str_replace(['_', '-'], ' ', $mapa)),
                    'dežela' => $dežela,
                ];
            }
        }
    }
    
    // Razporedi: auth na vrhu, nato po deželah, nato po abecedi
    usort($moduli, function($a, $b) {
        $prioritete = [
            'prijava' => 1,
            'registracija' => 2,
            'profil' => 3,
            'nastavitve' => 4,
            'pozabljeno_geslo' => 5,
            'ponastavi_geslo' => 6,
        ];
        $pa = $prioritete[$a['id']] ?? 99;
        $pb = $prioritete[$b['id']] ?? 99;
        
        if ($pa !== $pb) {
            return $pa - $pb;
        }
        
        // Nato po deželah
        $deželaRed = ['evropa' => 1, 'afrika' => 2, 'azija' => 3, 'amerika' => 4, 'okcitanija' => 5, 'znanstveni' => 6, 'duhovni' => 7, 'vesolje' => 8, 'sistem' => 9, 'specijal' => 10];
        $da = $deželaRed[$a['dežela'] ?? 'specijal'] ?? 99;
        $db = $deželaRed[$b['dežela'] ?? 'specijal'] ?? 99;
        
        if ($da !== $db) {
            return $da - $db;
        }
        
        // Nato po abecedi
        return strcmp($a['ime'], $b['ime']);
    });
    
    return $moduli;
}
