<?php
/**
 * ============================================================
 * POT: MODULI/SVETOVI/svetovi_handler.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODULI / SVETOVI
 *
 * 📰 NAMEN:
 *     Glavni handler za Elementalne Svetove
 *     Usmerja zahteve na pravi svet in prikaže vsebino
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - svetovi_prikazi(string $svet): void
 *     - svetovi_pridobi_module(string $svet): array
 *     - svetovi_pridobi_informacije(string $svet): array
 *
 * 📡 ODVISNOSTI:
 *     - MODULI/moduli_elementi.php
 *
 * 📌 STATUS:
 *     Aktivno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

// Elementalni svetovi
$SVETOVI = [
    'VODA' => [
        'id' => 'VODA',
        'ime' => 'Svet Vode',
        'simbol' => '💧',
        'ikona' => '🌊',
        'barva' => '#2196f3',
        'barva_temna' => '#0d47a1',
        'opis' => 'Svet intuicije, čustev in tekočnosti. Vodna energija prinaša mir, prilagodljivost in notranjo globino.',
        'atributi' => ['Intuicija', 'Čustva', 'Tekočnost', 'Prilagodljivost', 'Mir'],
        'moduli' => ['Lunaris', 'Energetica', 'Meditara', 'Somnaris', 'Sonaris', 'VibraMystica', 'Sreiki', 'Sshiatsu'],
        'ozadje' => 'linear-gradient(135deg, #0d47a1 0%, #2196f3 50%, #bbdefb 100%)'
    ],
    'ZRAK' => [
        'id' => 'ZRAK',
        'ime' => 'Svet Zraka',
        'simbol' => '🌬️',
        'ikona' => '💨',
        'barva' => '#00bcd4',
        'barva_temna' => '#006064',
        'opis' => 'Svet misli, komunikacije in svobode. Zračna energija prinaša jasnost, ustvarjalnost in duhovno povezanost.',
        'atributi' => ['Misel', 'Komunikacija', 'Svoboda', 'Sprememba', 'Ustvarjalnost'],
        'moduli' => ['Kabbaloria', 'CodexDamiris', 'CodexVerba', 'Oracle', 'OraculumVisionis', 'Oneirotica', 'Mythologica', 'Devorum', 'LiberUmbrae'],
        'ozadje' => 'linear-gradient(135deg, #006064 0%, #00bcd4 50%, #b2ebf2 100%)'
    ],
    'ETER' => [
        'id' => 'ETER',
        'ime' => 'Svet Eterja',
        'simbol' => '✨',
        'ikona' => '🌟',
        'barva' => '#9c27b0',
        'barva_temna' => '#4a148c',
        'opis' => 'Svet duhovnosti, energije in vibracij. Eterična energija povezuje vse stvari v kozmično mrežo.',
        'atributi' => ['Duhovnost', 'Energija', 'Vibracija', 'Povezanost', 'Kozmos'],
        'moduli' => ['Aetheris', 'QuantumMystica', 'AuroraMystica', 'SchronoSync', 'SenzorNasa', 'Sephirotica', 'Angelarium', 'Seraphica'],
        'ozadje' => 'linear-gradient(135deg, #4a148c 0%, #9c27b0 50%, #ce93d8 100%)'
    ],
    'ZEMLJA' => [
        'id' => 'ZEMLJA',
        'ime' => 'Svet Zemlje',
        'simbol' => '🌍',
        'ikona' => '🌿',
        'barva' => '#4caf50',
        'barva_temna' => '#1b5e20',
        'opis' => 'Svet stabilnosti, rasti in materialnosti. Zemeljska energija prinaša trdnost, obilje in povezanost z naravo.',
        'atributi' => ['Stabilnost', 'Rast', 'Materialnost', 'Telo', 'Narava'],
        'moduli' => ['Runaris', 'Lapidaria', 'Herbarica', 'BotanicaSacra', 'GeometricaSacra', 'AlchymiaAurea', 'Transmutaria', 'CorpusMysticum', 'MedicinaOrientalis', 'QiVitalis', 'Pranaymica', 'SfenShui', 'Sbazi', 'Sbajixing', 'Syijing', 'SwuXing', 'Jyotir', 'Sreiki', 'Sshiatsu', 'Ssekki', 'Sshenlong', 'Skanpo', 'Skijou', 'Sliuren', 'Smakoto', 'Smushin', 'Sneidan', 'SqiMapper', 'SauraMetrics', 'Chakrarium'],
        'ozadje' => 'linear-gradient(135deg, #1b5e20 0%, #4caf50 50%, #a5d6a7 100%)'
    ],
    'OGENJ' => [
        'id' => 'OGENJ',
        'ime' => 'Svet Ognja',
        'simbol' => '🔥',
        'ikona' => '🔥',
        'barva' => '#ff5722',
        'barva_temna' => '#bf360c',
        'opis' => 'Svet prenove, strasti in transformacije. Ognjena energija prinaša moč, pogum in notranjo preobrazbo.',
        'atributi' => ['Prenova', 'Strast', 'Moč', 'Transformacija', 'Pogum'],
        'moduli' => ['AlchymiaAurea', 'Transmutaria', 'SolarniPojavi', 'AuroraMystica', 'Shamanica', 'Animaris'],
        'ozadje' => 'linear-gradient(135deg, #bf360c 0%, #ff5722 50%, #ffab91 100%)'
    ]
];

/**
 * Prikaži svet
 */
function svetovi_prikazi(string $svet): void {
    global $SVETOVI;
    
    $svet = strtoupper($svet);
    
    if (!isset($SVETOVI[$svet])) {
        svetovi_prikazi_404($svet);
        return;
    }
    
    $podatki = $SVETOVI[$svet];
    include __DIR__ . '/prikaz_sveta.php';
}

/**
 * Pridobi module za svet
 */
function svetovi_pridobi_module(string $svet): array {
    global $SVETOVI;
    $svet = strtoupper($svet);
    return $SVETOVI[$svet]['moduli'] ?? [];
}

/**
 * Pridobi informacije o svetu
 */
function svetovi_pridobi_informacije(string $svet): ?array {
    global $SVETOVI;
    $svet = strtoupper($svet);
    return $SVETOVI[$svet] ?? null;
}

/**
 * Pridobi vse svetove
 */
function svetovi_pridobi_vse(): array {
    global $SVETOVI;
    return $SVETOVI;
}

/**
 * Prikaži 404 za neznan svet
 */
function svetovi_prikazi_404(string $svet): void {
    header('HTTP/1.0 404 Not Found');
    ?>
    <!DOCTYPE html>
    <html lang="sl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Svet ni najden | AstraMentalica</title>
        <style>
            body {
                font-family: 'Segoe UI', system-ui, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
                padding: 2rem;
            }
            .container {
                background: rgba(255,255,255,0.1);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 1px solid rgba(255,255,255,0.2);
                padding: 3rem;
                text-align: center;
                color: #fff;
                max-width: 500px;
            }
            h1 { font-size: 3rem; margin-bottom: 1rem; }
            p { font-size: 1.1rem; opacity: 0.8; }
            a { color: #fff; text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🌌</h1>
            <h2>Svet "<?= htmlspecialchars($svet) ?>" ne obstaja</h2>
            <p>Ta svet še ni bil ustvarjen. Razišči obstoječe svetove.</p>
            <p><a href="?svet=VODA">💧 Voda</a> | <a href="?svet=ZRAK">🌬️ Zrak</a> | <a href="?svet=ETER">✨ Eter</a> | <a href="?svet=ZEMLJA">🌍 Zemlja</a> | <a href="?svet=OGENJ">🔥 Ogenj</a></p>
        </div>
    </body>
    </html>
    <?php
}

// Če se kliče direktno
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'svetovi_handler.php') {
    $svet = $_GET['svet'] ?? 'VODA';
    svetovi_prikazi($svet);
}