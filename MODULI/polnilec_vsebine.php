<?php
/**
 * ============================================================
 * POT: MODULI/polnilec_vsebine.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODULI
 *
 * 📰 NAMEN:
 *     Polnilec vsebine za vse module
 *     Ustvari modul_*_funkcije.php, modul_*_api.php,
 *     modul_*_jsonbaza.php, modul_*_podatki.php
 *     z realno vsebino glede na element in namen modula
 *
 * 🔧 DELOVANJE:
 *     1. Skenira module
 *     2. Če ima že pravo vsebino (npr. class, funkcije) → preskoči
 *     3. Če ima samo auto-generiran wrapper → napolni
 *     4. Vsebino prilagodi glede na element in tip modula
 *
 * 🌊 Elementi in moduli:
 *     VODA:   čustva, intuicija, luna, voda, zvok, energija
 *     ZRAK:   misel, komunikacija, znanje, koda, vedeževanje
 *     ETER:   duhovnost, kvantna, vesolje, čas, angeli
 *     ZEMLJA: narava, kristali, rastline, telo, kitajska medicina
 *     OGENJ:  transformacija, sonce, alkimija, šamanizem
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

echo "=== POLNILEC VSEBINE ZA MODULE ===\n\n";

// Elementalna kategorizacija z vsebinskimi predlogami
$elementi = [
    'VODA' => [
        'barva' => '#2196f3',
        'ikona' => '🌊',
        'atributi' => ['intuicija', 'čustva', 'tekočnost', 'globina', 'energija'],
        'moduli' => [
            'Lunaris' => ['tip' => 'enciklopedija', 'opis' => 'Lunarni cikli, faze lune in njihov vpliv na čustva in energijo', 'kljucne_besede' => ['luna', 'cikli', 'faze', 'plima', 'čustva']],
            'Energetica' => ['tip' => 'interaktivni', 'opis' => 'Interaktivni zemljevid čaker, energijski tokovi in vodna energija', 'kljucne_besede' => ['čakre', 'energija', 'tokovi', 'vodna']],
            'Meditara' => ['tip' => 'praksa', 'opis' => 'Vodene meditacije za čustveno uravnoteženost in notranji mir', 'kljucne_besede' => ['meditacija', 'mir', 'čustva', 'vodene']],
            'Somnaris' => ['tip' => 'praksa', 'opis' => 'Raziskovanje sanj, podzavesti in intuitivnega znanja', 'kljucne_besede' => ['sanje', 'podzavest', 'intuicija', 'snovenje']],
            'Sonaris' => ['tip' => 'zbiralec', 'opis' => 'Zvočna pokrajina, terapevtski zvoki in vodna harmonija', 'kljucne_besede' => ['zvok', 'terapija', 'harmonija', 'frekvence']],
            'VibraMystica' => ['tip' => 'interaktivni', 'opis' => 'Zvočna mistika, frekvence, binaural beats in mantre', 'kljucne_besede' => ['frekvence', 'mantre', 'binaural', 'zvok']],
        ]
    ],
    'ZRAK' => [
        'barva' => '#00bcd4',
        'ikona' => '💨',
        'atributi' => ['misel', 'komunikacija', 'svoboda', 'znanje', 'modrost'],
        'moduli' => [
            'Kabbaloria' => ['tip' => 'enciklopedija', 'opis' => 'Kabalistična modrost, Drevo življenja in sefirotske poti', 'kljucne_besede' => ['kabala', 'sefiroti', 'drevo', 'modrost']],
            'CodexDamiris' => ['tip' => 'knjiga', 'opis' => 'Digitalna knjiga modrosti z več poglavji in duhovnimi učenji', 'kljucne_besede' => ['knjiga', 'modrost', 'učenje', 'duhovnost']],
            'CodexVerba' => ['tip' => 'knjiga', 'opis' => 'Zbirka svetih besedil, manter in duhovnih izrekov', 'kljucne_besede' => ['besedila', 'mantre', 'izreki', 'sveto']],
            'Oracle' => ['tip' => 'divinacija', 'opis' => 'Sistemsko vedeževanje in napovedi z AI podporo', 'kljucne_besede' => ['oracle', 'napovedi', 'vedeževanje', 'ai']],
            'OraculumVisionis' => ['tip' => 'divinacija', 'opis' => 'Vizije in prerokbe skozi meditativne tehnike', 'kljucne_besede' => ['vizije', 'prerokbe', 'meditacija', 'jasnovidnost']],
            'Oneirotica' => ['tip' => 'praksa', 'opis' => 'Interpretacija sanj in lucidno snovenje', 'kljucne_besede' => ['sanje', 'interpretacija', 'lucidno', 'snovenje']],
            'Mythologica' => ['tip' => 'enciklopedija', 'opis' => 'Zbirka mitov in legend iz celega sveta', 'kljucne_besede' => ['miti', 'legende', 'zgodbe', 'arhetipi']],
            'Devorum' => ['tip' => 'enciklopedija', 'opis' => 'Enciklopedija božanstev, bogov in duhovnih bitij', 'kljucne_besede' => ['bogovi', 'božanstva', 'bitja', 'mitologija']],
            'LiberUmbrae' => ['tip' => 'knjiga', 'opis' => 'Knjiga senc - duhovna zaščita in delo s senco', 'kljucne_besede' => ['sence', 'zaščita', 'duhovnost', 'transformacija']],
        ]
    ],
    'ETER' => [
        'barva' => '#9c27b0',
        'ikona' => '🌟',
        'atributi' => ['duhovnost', 'energija', 'vibracija', 'povezanost', 'kozmos'],
        'moduli' => [
            'Aetheris' => ['tip' => 'enciklopedija', 'opis' => 'Eterična energija, subtilna telesa in vibracijske frekvence', 'kljucne_besede' => ['eter', 'energija', 'vibracija', 'subtilno']],
            'QuantumMystica' => ['tip' => 'enciklopedija', 'opis' => 'Kvantna mistika in večdimenzionalna resničnost', 'kljucne_besede' => ['kvantno', 'dimenzije', 'resničnost', 'mistik']],
            'AuroraMystica' => ['tip' => 'vizualni', 'opis' => 'Aurore, nebesni pojavi in kozmična energija', 'kljucne_besede' => ['aurora', 'nebo', 'svetloba', 'kozmos']],
            'SchronoSync' => ['tip' => 'orodje', 'opis' => 'Časovna sinhronizacija in temporealna energija', 'kljucne_besede' => ['čas', 'sinhronizacija', 'energija', 'temporealno']],
            'SenzorNasa' => ['tip' => 'orodje', 'opis' => 'Zaznavanje energij, vibracij in subtilnih polj', 'kljucne_besede' => ['zaznavanje', 'energija', 'polja', 'vibracije']],
            'Sephirotica' => ['tip' => 'enciklopedija', 'opis' => 'Sefirotska pot Drevesa življenja v praksi', 'kljucne_besede' => ['sefiroti', 'drevo', 'poti', 'kabala']],
            'Angelarium' => ['tip' => 'enciklopedija', 'opis' => 'Nebeška hierarhija angelov in arhangelov', 'kljucne_besede' => ['angeli', 'arhangeli', 'nebo', 'hierarhija']],
            'Seraphica' => ['tip' => 'enciklopedija', 'opis' => 'Serafska energija in božanska svetloba', 'kljucne_besede' => ['serafi', 'svetloba', 'božansko', 'energija']],
        ]
    ],
    'ZEMLJA' => [
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'atributi' => ['stabilnost', 'rast', 'materialnost', 'telo', 'narava'],
        'moduli' => [
            'Runaris' => ['tip' => 'enciklopedija', 'opis' => 'Runska mitologija, Futhark abeceda in interpretacija run', 'kljucne_besede' => ['rune', 'futhark', 'mitologija', 'germansko']],
            'Lapidaria' => ['tip' => 'enciklopedija', 'opis' => 'Kristalna enciklopedija z močmi in lastnostmi kamnov', 'kljucne_besede' => ['kristali', 'kamni', 'energija', 'zdravljenje']],
            'Herbarica' => ['tip' => 'enciklopedija', 'opis' => 'Zeliščna enciklopedija za naravno zdravljenje', 'kljucne_besede' => ['zelišča', 'rastline', 'zdravljenje', 'naravno']],
            'BotanicaSacra' => ['tip' => 'enciklopedija', 'opis' => 'Sveti rastline in duhovna botanika', 'kljucne_besede' => ['rastline', 'sveto', 'botanika', 'duhovno']],
            'GeometricaSacra' => ['tip' => 'vizualni', 'opis' => 'Sakralna geometrija in sveti vzorci', 'kljucne_besede' => ['geometrija', 'sakralno', 'vzorci', 'sveto']],
            'AlchymiaAurea' => ['tip' => 'praksa', 'opis' => 'Alkimija in transmutacija snovi in duha', 'kljucne_besede' => ['alkimija', 'transmutacija', 'zlato', 'filozofija']],
            'Transmutaria' => ['tip' => 'praksa', 'opis' => 'Transmutacija energije in notranja preobrazba', 'kljucne_besede' => ['transmutacija', 'energija', 'preobrazba', 'notranje']],
            'CorpusMysticum' => ['tip' => 'enciklopedija', 'opis' => 'Mistično telo in duhovna anatomija', 'kljucne_besede' => ['telo', 'duhovno', 'anatomija', 'mistik']],
            'MedicinaOrientalis' => ['tip' => 'enciklopedija', 'opis' => 'Tradicionalna kitajska medicina in zdravljenje', 'kljucne_besede' => ['kitajska', 'medicina', 'akupunktura', 'zelišča']],
            'QiVitalis' => ['tip' => 'praksa', 'opis' => 'Qi energija, vitalnost in življenjska sila', 'kljucne_besede' => ['qi', 'energija', 'vitalnost', 'življenje']],
            'Pranaymica' => ['tip' => 'praksa', 'opis' => 'Prana, dihalne tehnike in življenjska energija', 'kljucne_besede' => ['prana', 'dihanje', 'energija', 'tehnike']],
            'SfenShui' => ['tip' => 'orodje', 'opis' => 'Feng Shui za harmonizacijo prostora in energij', 'kljucne_besede' => ['fengshui', 'prostor', 'energija', 'harmonija']],
            'Sbazi' => ['tip' => 'divinacija', 'opis' => 'Bazi kitajska astrologija in analiza', 'kljucne_besede' => ['bazi', 'astrologija', 'kitajsko', 'analiza']],
            'Sbajixing' => ['tip' => 'divinacija', 'opis' => 'Zi Wei Dou Shu - cesarska astrologija', 'kljucne_besede' => ['ziwei', 'astrologija', 'cesarsko', 'doušu']],
            'Syijing' => ['tip' => 'divinacija', 'opis' => 'I Ching, Knjiga sprememb in vedeževanje', 'kljucne_besede' => ['iching', 'spremembe', 'vedeževanje', 'kitajsko']],
            'SwuXing' => ['tip' => 'enciklopedija', 'opis' => 'Wu Xing - pet kitajskih elementov in njihov cikel', 'kljucne_besede' => ['wuxing', 'elementi', 'cikel', 'kitajsko']],
        ]
    ],
    'OGENJ' => [
        'barva' => '#ff5722',
        'ikona' => '🔥',
        'atributi' => ['prenova', 'strast', 'moč', 'transformacija', 'pogum'],
        'moduli' => [
            'AlchymiaAurea' => ['tip' => 'praksa', 'opis' => 'Ognjena alkimija in transmutacija duha', 'kljucne_besede' => ['alkimija', 'ogenj', 'transmutacija', 'zlato']],
            'Transmutaria' => ['tip' => 'praksa', 'opis' => 'Transformacija skozi ognjeno energijo', 'kljucne_besede' => ['transformacija', 'ogenj', 'energija', 'prenova']],
            'SolarniPojavi' => ['tip' => 'enciklopedija', 'opis' => 'Sončevi pojavi, sončna energija in kozmični vpliv', 'kljucne_besede' => ['sonce', 'solar', 'energija', 'pojavi']],
            'Shamanica' => ['tip' => 'praksa', 'opis' => 'Šamanske prakse, ognjeni obredi in duhovna potovanja', 'kljucne_besede' => ['šaman', 'ogenj', 'obredi', 'duhovno']],
            'Animaris' => ['tip' => 'enciklopedija', 'opis' => 'Živalski duhovi, totemske živali in ognjena strast', 'kljucne_besede' => ['živali', 'duhovi', 'totemi', 'strast']],
        ]
    ]
];

$rezultati = [
    'napolnjeni' => [],
    'preskoceni' => [],
    'napake' => []
];

foreach ($elementi as $element => $podatki) {
    foreach ($podatki['moduli'] as $ime_modula => $info) {
        $pot = __DIR__ . '/' . $ime_modula;
        
        echo "🔍 $ime_modula ($element)... ";
        
        if (!is_dir($pot)) {
            echo "❌ mapa ne obstaja\n";
            $rezultati['napake'][] = "$ime_modula (mapa ne obstaja)";
            continue;
        }
        
        // Preveri ali modul že ima realno vsebino (ne samo auto-generiran wrapper)
        $modul_php = $pot . '/modul.php';
        $ima_vsebino = false;
        
        if (file_exists($modul_php)) {
            $vsebina = file_get_contents($modul_php);
            // Preveri ali ima specifične funkcije - če je točno match auto-generiranega, potrebuje vsebino
            if (strpos($vsebina, 'Avtomatsko generirano') !== false) {
                $ima_vsebino = false;
            } else {
                $ima_vsebino = true;
            }
        }
        
        // Preveri obstoječe datoteke z vsebino
        $obstojece_datoteke = glob($pot . '/modul_*.php');
        $ima_prave_vsebine = false;
        foreach ($obstojece_datoteke as $f) {
            $ime = basename($f);
            if ($ime !== 'modul.php' && !strpos($ime, '(2)')) {
                $vseb = file_get_contents($f);
                if (strlen($vseb) > 200) {
                    $ima_prave_vsebine = true;
                    break;
                }
            }
        }
        
        if ($ima_vsebino || $ima_prave_vsebine) {
            echo "✅ že ima vsebino\n";
            $rezultati['preskoceni'][] = $ime_modula;
            continue;
        }
        
        // Ustvari vsebino
        $id = strtolower(preg_replace('/[^a-z0-9]/', '', $ime_modula));
        $tip = $info['tip'];
        $opis = $info['opis'];
        $kljucne = $info['kljucne_besede'];
        $barva = $podatki['barva'];
        $ikona = $podatki['ikona'];
        
        // 1. Ustvari modul_funkcije.php
        $funkcije_content = generiraj_funkcije($id, $ime_modula, $tip, $opis, $kljucne);
        file_put_contents($pot . "/modul_{$id}_funkcije.php", $funkcije_content);
        
        // 2. Ustvari modul_jsonbaza.php
        $baza_content = generiraj_bazo($id, $ime_modula, $tip, $kljucne);
        file_put_contents($pot . "/modul_{$id}_jsonbaza.php", $baza_content);
        
        // 3. Ustvari modul_podatki.php
        $podatki_content = generiraj_podatke($id, $ime_modula, $element, $kljucne, $barva, $ikona);
        file_put_contents($pot . "/modul_{$id}_podatki.php", $podatki_content);
        
        // 4. Ustvari API endpoint
        $api_content = generiraj_api($id, $ime_modula, $tip);
        file_put_contents($pot . "/modul_{$id}_api.php", $api_content);
        
        echo "📝 napolnjen ✅\n";
        $rezultati['napolnjeni'][] = $ime_modula;
    }
}

// Povzetek
echo "\n" . str_repeat("=", 60) . "\n";
echo "POVZETEK:\n\n";
echo "📝 Napolnjeni: " . count($rezultati['napolnjeni']) . "\n";
echo "✅ Preskočeni (že imajo vsebino): " . count($rezultati['preskoceni']) . "\n";
echo "❌ Napake: " . count($rezultati['napake']) . "\n\n";

if (!empty($rezultati['napolnjeni'])) {
    echo "Napolnjeni moduli:\n";
    foreach ($rezultati['napolnjeni'] as $m) echo "  - $m\n";
    echo "\n";
}

echo "Skupaj elementnih modulov: " . (count($rezultati['napolnjeni']) + count($rezultati['preskoceni'])) . "\n";

// ============================================================
// GENERATORJI VSEBINE
// ============================================================

function generiraj_funkcije($id, $ime, $tip, $opis, $kljucne): string {
    $class = 'Modul' . ucfirst($id) . 'Funkcije';
    $kljucne_json = json_encode(array_slice($kljucne, 0, 4), JSON_UNESCAPED_UNICODE);
    $tip_ucfirst = ucfirst($tip);
    
    return <<<PHP
<?php
/**
 * MODUL: {$ime}
 * FUNKCIJE: modul_{$id}_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     {$opis}
 *
 * TIP: {$tip}
 * KLJUČNE BESEDE: {$kljucne_json}
 */

declare(strict_types=1);

class {$class} {
    private array \$podatki;
    
    public function __construct() {
        \$this->podatki = [
            'ime' => '{$ime}',
            'id' => '{$id}',
            'tip' => '{$tip}',
            'verzija' => '1.0.0',
            'opis' => '{$opis}',
            'aktiviran' => true
        ];
    }
    
    /**
     * Pridobi osnovne informacije o modulu
     */
    public function pridobiInformacije(): array {
        return \$this->podatki;
    }
    
    /**
     * Pridobi vsebino za domov stran
     */
    public function pridobiDomov(): array {
        return [
            'naslov' => '{$ime}',
            'sporocilo' => 'Dobrodošli v modulu {$ime}!',
            'tip' => '{$tip}',
            'opis' => '{$opis}',
            'status' => 'pripravljen'
        ];
    }
    
    /**
     * Izvedi akcijo modula
     */
    public function izvediAkcijo(string \$akcija, array \$parametri = []): array {
        return match(\$akcija) {
            'info' => \$this->pridobiInformacije(),
            'domov' => \$this->pridobiDomov(),
            default => ['napaka' => "Neznana akcija: \$akcija"]
        };
    }
    
    /**
     * Pridobi statične podatke modula
     */
    public static function pridobiStatic(): array {
        return [
            'ime' => '{$ime}',
            'id' => '{$id}',
            'opis' => '{$opis}'
        ];
    }
}
PHP;
}

function generiraj_bazo($id, $ime, $tip, $kljucne): string {
    $class = 'Modul' . ucfirst($id) . 'Baza';
    $items = array_map(function($k) { return "'$k' => []"; }, $kljucne);
    $items_str = implode(",\n        ", $items);
    
    return <<<PHP
<?php
/**
 * MODUL: {$ime}
 * JSON BAZA: modul_{$id}_jsonbaza.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     JSON podatkovna baza za modul {$ime}
 *     Tip: {$tip}
 */

declare(strict_types=1);

class {$class} {
    private string \$potBaze;
    private array \$podatki;
    
    public function __construct() {
        \$this->potBaze = __DIR__ . '/podatki/baza.json';
        \$this->podatki = \$this->nalozi();
    }
    
    /**
     * Naloži podatke iz JSON baze
     */
    private function nalozi(): array {
        if (file_exists(\$this->potBaze)) {
            \$vsebina = file_get_contents(\$this->potBaze);
            \$json = json_decode(\$vsebina, true);
            if (\$json) {
                return \$json;
            }
        }
        return \$this->privzeto();
    }
    
    /**
     * Privzeti podatki
     */
    private function privzeto(): array {
        return [
            'ime' => '{$ime}',
            'id' => '{$id}',
            'verzija' => '1.0.0',
            'tip' => '{$tip}',
            'vnosov' => 0,
            'ustvarjeno' => date('Y-m-d H:i:s'),
            'nazadnje_posodobljeno' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Shrani podatke
     */
    public function shrani(): bool {
        \$mapa = dirname(\$this->potBaze);
        if (!is_dir(\$mapa)) {
            mkdir(\$mapa, 0755, true);
        }
        \$this->podatki['nazadnje_posodobljeno'] = date('Y-m-d H:i:s');
        return file_put_contents(\$this->potBaze, json_encode(\$this->podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
    
    /**
     * Pridobi vse podatke
     */
    public function pridobiVse(): array {
        return \$this->podatki;
    }
    
    /**
     * Pridobi specifičen podatek
     */
    public function pridobi(string \$kljuc, \$privzeto = null) {
        return \$this->podatki[\$kljuc] ?? \$privzeto;
    }
}
PHP;
}

function generiraj_podatke($id, $ime, $element, $kljucne, $barva, $ikona): string {
    $kljucne_json = json_encode($kljucne, JSON_UNESCAPED_UNICODE);
    
    return <<<PHP
<?php
/**
 * MODUL: {$ime}
 * PODATKI: modul_{$id}_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula {$ime}
 *     Element: {$element}
 *     Barva: {$barva}
 */

declare(strict_types=1);

define('MODUL_{$id}_IME', '{$ime}');
define('MODUL_{$id}_VERZIJA', '1.0.0');
define('MODUL_{$id}_ELEMENT', '{$element}');
define('MODUL_{$id}_BARVA', '{$barva}');
define('MODUL_{$id}_IKONA', '{$ikona}');

/**
 * Pridobi vse podatke modula
 */
function modul_{$id}_pridobi_podatke(): array {
    return [
        'ime' => '{$ime}',
        'id' => '{$id}',
        'element' => '{$element}',
        'barva' => '{$barva}',
        'ikona' => '{$ikona}',
        'verzija' => '1.0.0',
        'kljucne_besede' => {$kljucne_json},
        'opis' => '{$ime} modul v svetu {$element}'
    ];
}
PHP;
}

function generiraj_api($id, $ime, $tip): string {
    return <<<PHP
<?php
/**
 * MODUL: {$ime}
 * API: modul_{$id}_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     API endpoint za modul {$ime}
 *     Tip: {$tip}
 */

declare(strict_types=1);

/**
 * API handler za modul {$ime}
 *
 * @param string \$akcija Akcija za izvedbo
 * @param array \$parametri Parametri akcije
 * @return array Rezultat
 */
function modul_{$id}_api_izvedi(string \$akcija, array \$parametri = []): array {
    \$dovoljene_akcije = ['info', 'domov', 'isci', 'pridobi'];
    
    if (!in_array(\$akcija, \$dovoljene_akcije)) {
        return [
            'status' => 'napaka',
            'sporocilo' => "Neznana akcija: \$akcija",
            'koda' => 400
        ];
    }
    
    return [
        'status' => 'uspeh',
        'modul' => '{$ime}',
        'id' => '{$id}',
        'akcija' => \$akcija,
        'parametri' => \$parametri,
        'cas' => time()
    ];
}
PHP;
}