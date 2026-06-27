<?php
/**
 * ---------------------------------------------------------
 * POT: MODULI/moduli_elementi.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Elementalni sistem za kategorizacijo modulov
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Elementalni sistem - 5 elementov
$elementi = [
    'VODA' => [
        'simbol' => '💧',
        'barva' => '#2196f3',
        'opis' => 'Intuicija, čustva, tekočnost, prilagodljivost',
        'atributi' => ['mokrost', 'tekočina', 'globina', 'čustva']
    ],
    
    'ZRAK' => [
        'simbol' => '🌬️',
        'barva' => '#00bcd4',
        'opis' => 'Misel, komunikacija, svoboda, sprememba',
        'atributi' => ['misel', 'komunikacija', 'svoboda', 'sprememba']
    ],
    
    'ETER' => [
        'simbol' => '✨',
        'barva' => '#9c27b0',
        'opis' => 'Duhovnost, energija, povezanost, vibracija',
        'atributi' => ['duhovnost', 'energija', 'vibracija', 'povezanost']
    ],
    
    'ZEMLJA' => [
        'simbol' => '🌍',
        'barva' => '#4caf50',
        'opis' => 'Stabilnost, rast, materialnost, telo',
        'atributi' => ['stabilnost', 'rast', 'materialnost', 'telo']
    ],
    
    'OGENJ' => [
        'simbol' => '🔥',
        'barva' => '#ff5722',
        'opis' => 'Prenova, strast, moč, transformacija',
        'atributi' => ['prenova', 'strast', 'moč', 'transformacija']
    ]
];

// Kategorizacija vseh modulov po elementih
$moduli_po_elementih = [
    'VODA' => [
        'Lunaris' => [
            'ime' => 'Lunaris',
            'opis' => 'Lunarni cikel, mesečne energije, vpliv na čustva',
            'ikona' => '🌙',
            'barva' => '#2196f3'
        ],
        'Energetica' => [
            'ime' => 'Energetica',
            'opis' => 'Čakre, energijski tokovi, vodna energija',
            'ikona' => '⚡',
            'barva' => '#00bcd4'
        ],
        'Meditara' => [
            'ime' => 'Meditara',
            'opis' => 'Meditacije, notranji mir, čustvena uravnoteženost',
            'ikona' => '🧘',
            'barva' => '#2196f3'
        ],
        'Somnaris' => [
            'ime' => 'Somnaris',
            'opis' => 'Snovenje, podzavest, intuicija',
            'ikona' => '🌙',
            'barva' => '#673ab7'
        ],
        'Sonaris' => [
            'ime' => 'Sonaris',
            'opis' => 'Zvočni svet, zvoki, vibracije',
            'ikona' => '🎵',
            'barva' => '#2196f3'
        ],
        'VibraMystica' => [
            'ime' => 'VibraMystica',
            'opis' => 'Zvočna mistika, frekvence, binaural beats',
            'ikona' => '🎵',
            'barva' => '#9c27b0'
        ],
        'Sreiki' => [
            'ime' => 'Sreiki',
            'opis' => 'Reiki, energetsko zdravljenje, čakralna terapija',
            'ikona' => '💚',
            'barva' => '#4caf50'
        ],
        'Sshiatsu' => [
            'ime' => 'Sshiatsu',
            'opis' => 'Japonska prstna terapija, energijski tokovi',
            'ikona' => '💆',
            'barva' => '#2196f3'
        ]
    ],
    
    'ZRAK' => [
        'Kabbaloria' => [
            'ime' => 'Kabbaloria',
            'opis' => 'Kabala, Drevo življenja, sefiroti',
            'ikona' => '🌳',
            'barva' => '#3f51b5'
        ],
        'CodexDamiris' => [
            'ime' => 'Codex Damiris',
            'opis' => 'Knjiga modrosti, duhovna učenja',
            'ikona' => '📖',
            'barva' => '#4caf50'
        ],
        'CodexVerba' => [
            'ime' => 'Codex Verba',
            'opis' => 'Besedna moč, mantre, izreki',
            'ikona' => '📜',
            'barva' => '#00bcd4'
        ],
        'Oracle' => [
            'ime' => 'Oracle',
            'opis' => 'Vedeževanje, napovedi, komunikacija z višjim jazom',
            'ikona' => '🔮',
            'barva' => '#9c27b0'
        ],
        'OraculumVisionis' => [
            'ime' => 'Oraculum Visionis',
            'opis' => 'Vizije, prerokbe, duhovna komunikacija',
            'ikona' => '👁️',
            'barva' => '#00bcd4'
        ],
        'Oneirotica' => [
            'ime' => 'Oneirotica',
            'opis' => 'Snovenje, interpretacija sanj',
            'ikona' => '💭',
            'barva' => '#9c27b0'
        ],
        'Mythologica' => [
            'ime' => 'Mythologica',
            'opis' => 'Mitoloske zgodbe, arhetipi',
            'ikona' => '🏛️',
            'barva' => '#00bcd4'
        ],
        'Devorum' => [
            'ime' => 'Devorum',
            'opis' => 'Enciklopedija mitov in arhetipov',
            'ikona' => '🏛️',
            'barva' => '#ff5722'
        ],
        'LiberUmbrae' => [
            'ime' => 'Liber Umbrae',
            'opis' => 'Knjiga senc, duhovna zaščita',
            'ikona' => '📕',
            'barva' => '#00bcd4'
        ]
    ],
    
    'ETER' => [
        'Aetheris' => [
            'ime' => 'Aetheris',
            'opis' => 'Eterična energija, frekvence, vibracije',
            'ikona' => '🌟',
            'barva' => '#9c27b0'
        ],
        'QuantumMystica' => [
            'ime' => 'QuantumMystica',
            'opis' => 'Kvantna mistika, večdimenzionalnost',
            'ikona' => '⚛️',
            'barva' => '#9c27b0'
        ],
        'AuroraMystica' => [
            'ime' => 'Aurora Mystica',
            'opis' => 'Zora, novi začetki, čista energija',
            'ikona' => '🌅',
            'barva' => '#9c27b0'
        ],
        'SchronoSync' => [
            'ime' => 'SchronoSync',
            'opis' => 'Časovna sinhronizacija, temporalna energija',
            'ikona' => '⏰',
            'barva' => '#9c27b0'
        ],
        'SenzorNasa' => [
            'ime' => 'Senzor Nasa',
            'opis' => 'Zaznavanje energij, vibracij',
            'ikona' => '📡',
            'barva' => '#9c27b0'
        ],
        'Sephirotica' => [
            'ime' => 'Sephirotica',
            'opis' => 'Drevo življenja, sefirotske poti',
            'ikona' => '🌳',
            'barva' => '#3f51b5'
        ],
        'Angelarium' => [
            'ime' => 'Angelarium',
            'opis' => 'Angeli, arhangeli, nebesna bitja',
            'ikona' => '👼',
            'barva' => '#9c27b0'
        ],
        'Seraphica' => [
            'ime' => 'Seraphica',
            'opis' => 'Serafi, višji angeli, božanska svetloba',
            'ikona' => '✨',
            'barva' => '#9c27b0'
        ]
    ],
    
    'ZEMLJA' => [
        'Runaris' => [
            'ime' => 'Runaris',
            'opis' => 'Runska mitologija, Futhark, germanska tradicija',
            'ikona' => 'ᚠ',
            'barva' => '#795548'
        ],
        'Lapidaria' => [
            'ime' => 'Lapidaria',
            'opis' => 'Kristalna enciklopedija, moč kristalov',
            'ikona' => '💎',
            'barva' => '#00bcd4'
        ],
        'Herbarica' => [
            'ime' => 'Herbarica',
            'opis' => 'Zelišča, rastline, naravno zdravljenje',
            'ikona' => '🌿',
            'barva' => '#4caf50'
        ],
        'BotanicaSacra' => [
            'ime' => 'Botanica Sacra',
            'opis' => 'Sveti rastline, duhovna botanika',
            'ikona' => '🌱',
            'barva' => '#4caf50'
        ],
        'GeometricaSacra' => [
            'ime' => 'Geometrica Sacra',
            'opis' => 'Sveti geometrijski vzorci, sakralna geometrija',
            'ikona' => '📐',
            'barva' => '#795548'
        ],
        'AlchymiaAurea' => [
            'ime' => 'Alchymia Aurea',
            'opis' => 'Alkimija, transmutacija, zlato',
            'ikona' => '⚗️',
            'barva' => '#ff9800'
        ],
        'Transmutaria' => [
            'ime' => 'Transmutaria',
            'opis' => 'Transmutacija energije, pretvorba',
            'ikona' => '🔄',
            'barva' => '#4caf50'
        ],
        'CorpusMysticum' => [
            'ime' => 'Corpus Mysticum',
            'opis' => 'Mistično telo, duhovno telo',
            'ikona' => '🧘',
            'barva' => '#4caf50'
        ],
        'MedicinaOrientalis' => [
            'ime' => 'Medicina Orientalis',
            'opis' => 'Vzhodna medicina, kitajska medicina',
            'ikona' => '🏥',
            'barva' => '#4caf50'
        ],
        'QiVitalis' => [
            'ime' => 'QiVitalis',
            'opis' => 'Qi energija, vitalnost, življenjska sila',
            'ikona' => '💪',
            'barva' => '#4caf50'
        ],
        'Pranaymica' => [
            'ime' => 'Pranaymica',
            'opis' => 'Prana, dihanje, življenjska energija',
            'ikona' => '🌬️',
            'barva' => '#4caf50'
        ],
        'SfenShui' => [
            'ime' => 'SfenShui',
            'opis' => 'Feng Shui, harmonija prostora',
            'ikona' => '🏠',
            'barva' => '#4caf50'
        ],
        'Sbazi' => [
            'ime' => 'Sbazi',
            'opis' => 'Bazi, kitajska astrologija',
            'ikona' => '☯️',
            'barva' => '#4caf50'
        ],
        'Sbajixing' => [
            'ime' => 'Sbajixing',
            'opis' => 'Zi Wei Dou Shu, cesarska astrologija',
            'ikona' => '⭐',
            'barva' => '#4caf50'
        ],
        'Syijing' => [
            'ime' => 'Syijing',
            'opis' => 'I Ching, knjiga sprememb',
            'ikona' => '☯️',
            'barva' => '#4caf50'
        ],
        'SwuXing' => [
            'ime' => 'SwuXing',
            'opis' => 'Wu Xing, pet elementov',
            'ikona' => '🔄',
            'barva' => '#4caf50'
        ],
        'Jyotir' => [
            'ime' => 'Jyotir',
            'opis' => 'Vedska astrologija, jyotish',
            'ikona' => '🌟',
            'barva' => '#4caf50'
        ],
        'Sreiki' => [
            'ime' => 'Sreiki',
            'opis' => 'Reiki, japonsko energetsko zdravljenje',
            'ikona' => '💚',
            'barva' => '#4caf50'
        ],
        'Sshiatsu' => [
            'ime' => 'Sshiatsu',
            'opis' => 'Shiatsu, prstna terapija',
            'ikona' => '💆',
            'barva' => '#4caf50'
        ],
        'Ssekki' => [
            'ime' => 'Ssekki',
            'opis' => 'Seimei Kinen, japonska geomancija',
            'ikona' => '📍',
            'barva' => '#4caf50'
        ],
        'Sshenlong' => [
            'ime' => 'Sshenlong',
            'opis' => 'Shen Long, duhovni zmaj',
            'ikona' => '🐉',
            'barva' => '#4caf50'
        ],
        'Skanpo' => [
            'ime' => 'Skanpo',
            'opis' => 'Shingon, japanska mistika',
            'ikona' => '☸️',
            'barva' => '#4caf50'
        ],
        'Skijou' => [
            'ime' => 'Skijou',
            'opis' => 'Shugendo, japonska gorska asketa',
            'ikona' => '⛰️',
            'barva' => '#4caf50'
        ],
        'Sliuren' => [
            'ime' => 'Sliuren',
            'opis' => 'Shorinji Kempo, duhovna borilna veščina',
            'ikona' => '🥋',
            'barva' => '#4caf50'
        ],
        'Smakoto' => [
            'ime' => 'Smakoto',
            'opis' => 'Shakyo, kopiranje sutr',
            'ikona' => '✍️',
            'barva' => '#4caf50'
        ],
        'Smushin' => [
            'ime' => 'Smushin',
            'opis' => 'Mushin, brez uma, zen stanje',
            'ikona' => '🧠',
            'barva' => '#4caf50'
        ],
        'Sneidan' => [
            'ime' => 'Sneidan',
            'opis' => 'Neidan, notranja alkimija',
            'ikona' => '⚗️',
            'barva' => '#4caf50'
        ],
        'SqiMapper' => [
            'ime' => 'SqiMapper',
            'opis' => 'Qi zemljevid, energetska zemljevida',
            'ikona' => '🗺️',
            'barva' => '#4caf50'
        ],
        'SauraMetrics' => [
            'ime' => 'SauraMetrics',
            'opis' => 'Sau, energetska merjenja',
            'ikona' => '📊',
            'barva' => '#4caf50'
        ],
        'Chakrarium' => [
            'ime' => 'Chakrarium',
            'opis' => 'Čakre, energetski centri',
            'ikona' => '🔮',
            'barva' => '#4caf50'
        ],
        'Pranaymica' => [
            'ime' => 'Pranaymica',
            'opis' => 'Prana, dihanje, življenjska sila',
            'ikona' => '🌬️',
            'barva' => '#4caf50'
        ]
    ],
    
    'OGENJ' => [
        'AlchymiaAurea' => [
            'ime' => 'Alchymia Aurea',
            'opis' => 'Alkimija, transformacija, ognjena transformacija',
            'ikona' => '⚗️',
            'barva' => '#ff9800'
        ],
        'Transmutaria' => [
            'ime' => 'Transmutaria',
            'opis' => 'Transmutacija, ognjena prenova',
            'ikona' => '🔄',
            'barva' => '#ff5722'
        ],
        'SolarniPojavi' => [
            'ime' => 'Solarni Pojavi',
            'opis' => 'Sončne energije, solarne pojave',
            'ikona' => '☀️',
            'barva' => '#ff9800'
        ],
        'AuroraMystica' => [
            'ime' => 'Aurora Mystica',
            'opis' => 'Zora, ognjena zora',
            'ikona' => '🌅',
            'barva' => '#ff5722'
        ],
        'Shamanica' => [
            'ime' => 'Shamanica',
            'opis' => 'Šamanizem, ognjni šamani',
            'ikona' => '🔥',
            'barva' => '#ff5722'
        ],
        'Animaris' => [
            'ime' => 'Animaris',
            'opis' => 'Živalski duhovi, ognjena strast',
            'ikona' => '🐾',
            'barva' => '#ff5722'
        ]
    ]
];

// Funkcija za pridobitev elementa modula
function modul_pridobi_element(string $ime_modula): ?string {
    global $moduli_po_elementih;
    
    foreach ($moduli_po_elementih as $element => $moduli) {
        if (isset($moduli[$ime_modula])) {
            return $element;
        }
    }
    
    return null; // Neuvrščen
}

// Funkcija za pridobitev vseh modulov v elementu
function elementi_pridobi_module(string $element): array {
    global $moduli_po_elementih;
    return $moduli_po_elementih[$element] ?? [];
}

// Funkcija za štetje modulov po elementih
function elementi_statistika(): array {
    global $moduli_po_elementih;
    
    $statistika = [];
    foreach ($moduli_po_elementih as $element => $moduli) {
        $statistika[$element] = count($moduli);
    }
    
    return $statistika;
}

// Izvoz za uporabo v drugih datotekah
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'moduli_elementi.php') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'elementi' => $elementi,
        'moduli' => $moduli_po_elementih,
        'statistika' => elementi_statistika()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}