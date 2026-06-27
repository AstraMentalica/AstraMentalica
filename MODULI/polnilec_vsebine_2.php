<?php
/**
 * ============================================================
 * POT: MODULI/polnilec_vsebine_2.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODULI
 *
 * 📰 NAMEN:
 *     Drugi prehod - napolni preostale module z vsebino
 *     (tiste, ki niso v elementnih svetovih)
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 * ============================================================
 */

declare(strict_types=1);

echo "=== POLNILEC VSEBINE 2 - PREOSTALI MODULI ===\n\n";

// Elementni moduli (že obdelani)
$elementni = ['Lunaris','Energetica','Meditara','Somnaris','Sonaris','VibraMystica',
    'Kabbaloria','CodexDamiris','CodexVerba','Oracle','OraculumVisionis','Oneirotica',
    'Mythologica','Devorum','LiberUmbrae','Aetheris','QuantumMystica','AuroraMystica',
    'SchronoSync','SenzorNasa','Sephirotica','Angelarium','Seraphica','Runaris',
    'Lapidaria','Herbarica','BotanicaSacra','GeometricaSacra','AlchymiaAurea',
    'Transmutaria','CorpusMysticum','MedicinaOrientalis','QiVitalis','Pranaymica',
    'SfenShui','Sbazi','Sbajixing','Syijing','SwuXing','SolarniPojavi','Shamanica','Animaris'];

$preskoci = ['Modul_Bridge','SVETOVI','TemplateModul','Azija'];

// Vsebinske predloge za preostale module
$vsebine = [
    'AegypticaArcana' => ['tip' => 'enciklopedija', 'opis' => 'Staroegipčanska mistika, bogovi in skrivnosti piramid', 'kljucne' => ['egipt', 'piramide', 'bogovi', 'mistika']],
    'Aeternum' => ['tip' => 'enciklopedija', 'opis' => 'Večnost, nesmrtnost duše in večni cikli', 'kljucne' => ['večnost', 'nesmrtnost', 'cikli', 'duša']],
    'BotanicSacra' => ['tip' => 'enciklopedija', 'opis' => 'Sakralna botanika in svete rastline', 'kljucne' => ['rastline', 'sakralno', 'sveto', 'botanika']],
    'Chakrarium' => ['tip' => 'interaktivni', 'opis' => 'Interaktivni zemljevid čaker in energijskih centrov', 'kljucne' => ['čakre', 'energija', 'centri', 'interaktivno']],
    'Codex' => ['tip' => 'knjiga', 'opis' => 'Splošna zbirka modrosti in znanja', 'kljucne' => ['koda', 'modrost', 'znanje', 'zbirka']],
    'CodexAntiqua' => ['tip' => 'knjiga', 'opis' => 'Starodavna besedila in rokopisi', 'kljucne' => ['starodavno', 'besedila', 'rokopisi', 'zgodovina']],
    'Crystallum' => ['tip' => 'enciklopedija', 'opis' => 'Kristali in njihove energijske lastnosti', 'kljucne' => ['kristali', 'energija', 'lastnosti', 'zdravljenje']],
    'Daemontica' => ['tip' => 'enciklopedija', 'opis' => 'Demonologija in duhovna bitja', 'kljucne' => ['demoni', 'bitja', 'duhovno', 'zaščita']],
    'Djotis' => ['tip' => 'divinacija', 'opis' => 'Vedska astrologija in jyotish', 'kljucne' => ['vedska', 'astrologija', 'jyotish', 'zvezde']],
    'Jyotir' => ['tip' => 'divinacija', 'opis' => 'Svetlobna astrologija in kozmični vplivi', 'kljucne' => ['svetloba', 'astrologija', 'kozmos', 'vplivi']],
    'Labyrinthus' => ['tip' => 'orodje', 'opis' => 'Labirinti in mandale za meditacijo', 'kljucne' => ['labirint', 'mandala', 'meditacija', 'vzorci']],
    'Mystaia' => ['tip' => 'enciklopedija', 'opis' => 'Mistična učenja in skrivne modrosti', 'kljucne' => ['mistika', 'učenja', 'modrost', 'skrivno']],
    'Mystic' => ['tip' => 'enciklopedija', 'opis' => 'Misticizem in duhovne prakse', 'kljucne' => ['misticizem', 'duhovnost', 'prakse', 'kontemplacija']],
    'Mystica' => ['tip' => 'enciklopedija', 'opis' => 'Mistična tradicija in ezoterično znanje', 'kljucne' => ['mistika', 'ezoterika', 'tradicija', 'znanje']],
    'MysticaMesoAmericana' => ['tip' => 'enciklopedija', 'opis' => 'Srednjeameriška mistika in duhovnost', 'kljucne' => ['maje', 'azteki', 'mistika', 'amerika']],
    'Nasa' => ['tip' => 'enciklopedija', 'opis' => 'Vesoljske misije in kozmična znanost', 'kljucne' => ['vesolje', 'nasa', 'misije', 'znanost']],
    'NordicaMystica' => ['tip' => 'enciklopedija', 'opis' => 'Nordijska mitologija in magija', 'kljucne' => ['nordijsko', 'mitologija', 'vikingi', 'magija']],
    'NumerariumCosmicum' => ['tip' => 'divinacija', 'opis' => 'Kozmična numerologija in številske vibracije', 'kljucne' => ['numerologija', 'števila', 'kozmos', 'vibracije']],
    'Numyra' => ['tip' => 'divinacija', 'opis' => 'Numerološka analiza in napovedi', 'kljucne' => ['numerologija', 'analiza', 'napovedi', 'števila']],
    'Occultum' => ['tip' => 'enciklopedija', 'opis' => 'Okultne znanosti in skrivne prakse', 'kljucne' => ['okultno', 'znanost', 'prakse', 'skrivno']],
    'Orakleum' => ['tip' => 'divinacija', 'opis' => 'Orakelj sistem za vedeževanje', 'kljucne' => ['orakelj', 'vedeževanje', 'sistem', 'napovedi']],
    'SauraMetrics' => ['tip' => 'orodje', 'opis' => 'Energetska merjenja in aura analiza', 'kljucne' => ['aura', 'merjenje', 'energija', 'analiza']],
    'Shanami' => ['tip' => 'praksa', 'opis' => 'Šamanske poti in duhovna potovanja', 'kljucne' => ['šaman', 'potovanja', 'duhovno', 'prakse']],
    'Sigillaris' => ['tip' => 'orodje', 'opis' => 'Sigili in simboli za magične namene', 'kljucne' => ['sigili', 'simboli', 'magija', 'zaščita']],
    'Skami' => ['tip' => 'praksa', 'opis' => 'Kami - japonska duhovna bitja in šintoizem', 'kljucne' => ['kami', 'japonsko', 'šinto', 'duhovi']],
    'Skanpo' => ['tip' => 'praksa', 'opis' => 'Shingon budistična praksa in mistika', 'kljucne' => ['shingon', 'budizem', 'japonsko', 'mistika']],
    'Skijou' => ['tip' => 'praksa', 'opis' => 'Shugendo - japonska gorska asketska tradicija', 'kljucne' => ['shugendo', 'japonsko', 'asket', 'gore']],
    'Sliuren' => ['tip' => 'praksa', 'opis' => 'Shorinji Kempo - duhovna borilna veščina', 'kljucne' => ['kempo', 'borilno', 'duhovno', 'japonsko']],
    'Smakoto' => ['tip' => 'praksa', 'opis' => 'Shakyo - kopiranje budističnih sutr', 'kljucne' => ['shakyo', 'sutre', 'budizem', 'meditacija']],
    'Smushin' => ['tip' => 'praksa', 'opis' => 'Mushin - stanje brez uma v zen praksi', 'kljucne' => ['mushin', 'zen', 'um', 'praznina']],
    'Sneidan' => ['tip' => 'praksa', 'opis' => 'Neidan - notranja alkimija in energetska praksa', 'kljucne' => ['neidan', 'alkimija', 'notranje', 'energija']],
    'SqiMapper' => ['tip' => 'orodje', 'opis' => 'Qi zemljevid in energetska kartografija', 'kljucne' => ['qi', 'zemljevid', 'energija', 'kartografija']],
    'Sreiki' => ['tip' => 'praksa', 'opis' => 'Reiki energetsko zdravljenje in praksa', 'kljucne' => ['reiki', 'zdravljenje', 'energija', 'japonsko']],
    'Ssekki' => ['tip' => 'divinacija', 'opis' => 'Seimei Kinen - japonska geomancija', 'kljucne' => ['seimei', 'geomancija', 'japonsko', 'divinacija']],
    'Sshenlong' => ['tip' => 'enciklopedija', 'opis' => 'Shen Long - kitajski duhovni zmaj', 'kljucne' => ['zmaj', 'kitajsko', 'duhovno', 'shen']],
    'Sshiatsu' => ['tip' => 'praksa', 'opis' => 'Shiatsu - japonska prstna terapija', 'kljucne' => ['shiatsu', 'terapija', 'japonsko', 'masaža']],
    'Stelar' => ['tip' => 'enciklopedija', 'opis' => 'Zvezdna energija in astralna potovanja', 'kljucne' => ['zvezde', 'astral', 'energija', 'potovanja']],
    'Stelaris' => ['tip' => 'enciklopedija', 'opis' => 'Zvezdna karta in kozmična navigacija', 'kljucne' => ['zvezde', 'karta', 'kozmos', 'navigacija']],
    'Sunmei' => ['tip' => 'divinacija', 'opis' => 'Unmei - japonska usodna astrologija', 'kljucne' => ['unmei', 'usoda', 'japonsko', 'astrologija']],
    'Swabi' => ['tip' => 'praksa', 'opis' => 'Wabi-sabi - lepota nepopolnosti', 'kljucne' => ['wabi', 'sabi', 'japonsko', 'estetika']],
    'Synera' => ['tip' => 'orodje', 'opis' => 'Sinergija energij in integracija praks', 'kljucne' => ['sinergija', 'energija', 'integracija', 'prakse']],
    'SyneraVip' => ['tip' => 'orodje', 'opis' => 'Napredna sinergija in VIP energetske prakse', 'kljucne' => ['sinergija', 'vip', 'napredno', 'energija']],
    'Szazen' => ['tip' => 'praksa', 'opis' => 'Zazen - zen meditacija v sedenju', 'kljucne' => ['zazen', 'zen', 'meditacija', 'sedenje']],
    'SziWei' => ['tip' => 'divinacija', 'opis' => 'Zi Wei Dou Shu - kitajska cesarska astrologija', 'kljucne' => ['ziwei', 'astrologija', 'kitajsko', 'cesarsko']],
    'SzodiacApi' => ['tip' => 'divinacija', 'opis' => 'Zodiakalna astrologija in horoskopi', 'kljucne' => ['zodiak', 'astrologija', 'horoskop', 'zvezde']],
    'aetheris2' => ['tip' => 'enciklopedija', 'opis' => 'Napredna eterična energija in vibracije', 'kljucne' => ['eter', 'energija', 'napredno', 'vibracije']],
    'Modul_Mystaia' => ['tip' => 'enciklopedija', 'opis' => 'Mystaia modul - mistična platforma', 'kljucne' => ['mystaia', 'platforma', 'mistika', 'modul']],
    'Modul_Orakleum' => ['tip' => 'divinacija', 'opis' => 'Orakleum modul - napovedni sistem', 'kljucne' => ['orakleum', 'napovedi', 'sistem', 'modul']],
    'Mystaia_modul' => ['tip' => 'enciklopedija', 'opis' => 'Mystaia modularna platforma', 'kljucne' => ['mystaia', 'modularno', 'platforma', 'sistem']],
];

$rezultati = ['napolnjeni' => [], 'preskoceni' => [], 'napake' => []];

foreach ($vsebine as $ime_modula => $info) {
    $pot = __DIR__ . '/' . $ime_modula;
    
    echo "🔍 $ime_modula... ";
    
    if (!is_dir($pot)) {
        echo "❌ mapa ne obstaja\n";
        $rezultati['napake'][] = "$ime_modula (mapa ne obstaja)";
        continue;
    }
    
    // Preveri ali že ima vsebino
    $modul_php = $pot . '/modul.php';
    if (file_exists($modul_php)) {
        $vsebina = file_get_contents($modul_php);
        if (strpos($vsebina, 'Avtomatsko generirano') === false) {
            echo "✅ že ima vsebino\n";
            $rezultati['preskoceni'][] = $ime_modula;
            continue;
        }
    }
    
    $id = strtolower(preg_replace('/[^a-z0-9]/', '', $ime_modula));
    $tip = $info['tip'];
    $opis = $info['opis'];
    $kljucne = $info['kljucne'];
    
    // Ustvari vsebinske datoteke
    file_put_contents($pot . "/modul_{$id}_funkcije.php", generiraj_funkcije($id, $ime_modula, $tip, $opis, $kljucne));
    file_put_contents($pot . "/modul_{$id}_jsonbaza.php", generiraj_bazo($id, $ime_modula, $tip, $kljucne));
    file_put_contents($pot . "/modul_{$id}_podatki.php", generiraj_podatke($id, $ime_modula, $kljucne));
    file_put_contents($pot . "/modul_{$id}_api.php", generiraj_api($id, $ime_modula, $tip));
    
    echo "📝 napolnjen ✅\n";
    $rezultati['napolnjeni'][] = $ime_modula;
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "POVZETEK:\n\n";
echo "📝 Napolnjeni: " . count($rezultati['napolnjeni']) . "\n";
echo "✅ Preskočeni: " . count($rezultati['preskoceni']) . "\n";
echo "❌ Napake: " . count($rezultati['napake']) . "\n\n";

if (!empty($rezultati['napolnjeni'])) {
    echo "Napolnjeni moduli:\n";
    foreach ($rezultati['napolnjeni'] as $m) echo "  - $m\n";
}

// ============================================================
// GENERATORJI
// ============================================================

function generiraj_funkcije($id, $ime, $tip, $opis, $kljucne): string {
    $class = 'Modul' . ucfirst($id) . 'Funkcije';
    $kljucne_json = json_encode(array_slice($kljucne, 0, 4), JSON_UNESCAPED_UNICODE);
    
    return <<<PHP
<?php
/**
 * MODUL: {$ime}
 * FUNKCIJE: modul_{$id}_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: {$opis}
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
    
    public function pridobiInformacije(): array { return \$this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => '{$ime}',
            'sporocilo' => 'Dobrodošli v modulu {$ime}!',
            'tip' => '{$tip}',
            'opis' => '{$opis}',
            'status' => 'pripravljen'
        ];
    }
    
    public function izvediAkcijo(string \$akcija, array \$parametri = []): array {
        return match(\$akcija) {
            'info' => \$this->pridobiInformacije(),
            'domov' => \$this->pridobiDomov(),
            default => ['napaka' => "Neznana akcija: \$akcija"]
        };
    }
    
    public static function pridobiStatic(): array {
        return ['ime' => '{$ime}', 'id' => '{$id}', 'opis' => '{$opis}'];
    }
}
PHP;
}

function generiraj_bazo($id, $ime, $tip, $kljucne): string {
    $class = 'Modul' . ucfirst($id) . 'Baza';
    
    return <<<PHP
<?php
/**
 * MODUL: {$ime}
 * JSON BAZA: modul_{$id}_jsonbaza.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: {$tip}
 */

declare(strict_types=1);

class {$class} {
    private string \$potBaze;
    private array \$podatki;
    
    public function __construct() {
        \$this->potBaze = __DIR__ . '/podatki/baza.json';
        \$this->podatki = \$this->nalozi();
    }
    
    private function nalozi(): array {
        if (file_exists(\$this->potBaze)) {
            \$json = json_decode(file_get_contents(\$this->potBaze), true);
            if (\$json) return \$json;
        }
        return \$this->privzeto();
    }
    
    private function privzeto(): array {
        return [
            'ime' => '{$ime}', 'id' => '{$id}', 'verzija' => '1.0.0',
            'tip' => '{$tip}', 'vnosov' => 0,
            'ustvarjeno' => date('Y-m-d H:i:s'),
            'nazadnje_posodobljeno' => date('Y-m-d H:i:s')
        ];
    }
    
    public function shrani(): bool {
        \$mapa = dirname(\$this->potBaze);
        if (!is_dir(\$mapa)) mkdir(\$mapa, 0755, true);
        \$this->podatki['nazadnje_posodobljeno'] = date('Y-m-d H:i:s');
        return file_put_contents(\$this->potBaze, json_encode(\$this->podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
    
    public function pridobiVse(): array { return \$this->podatki; }
    public function pridobi(string \$kljuc, \$privzeto = null) { return \$this->podatki[\$kljuc] ?? \$privzeto; }
}
PHP;
}

function generiraj_podatke($id, $ime, $kljucne): string {
    $kljucne_json = json_encode($kljucne, JSON_UNESCAPED_UNICODE);
    
    return <<<PHP
<?php
/**
 * MODUL: {$ime}
 * PODATKI: modul_{$id}_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_{$id}_IME', '{$ime}');
define('MODUL_{$id}_VERZIJA', '1.0.0');

function modul_{$id}_pridobi_podatke(): array {
    return [
        'ime' => '{$ime}',
        'id' => '{$id}',
        'verzija' => '1.0.0',
        'kljucne_besede' => {$kljucne_json},
        'opis' => '{$ime} modul'
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
 * TIP: {$tip}
 */

declare(strict_types=1);

function modul_{$id}_api_izvedi(string \$akcija, array \$parametri = []): array {
    \$dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array(\$akcija, \$dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: \$akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => '{$ime}', 'id' => '{$id}', 'akcija' => \$akcija, 'cas' => time()];
}
PHP;
}