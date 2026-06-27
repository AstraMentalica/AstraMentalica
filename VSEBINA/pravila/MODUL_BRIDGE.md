---

# 2. POPRAVIM `MODUL_BRIDGE.md`

```markdown
================================================================================
MODULI/Modul_Bridge/ – ORKESTRATOR ZA RAZVOJ MODULOV
================================================================================

📌 NAMEN:
Modul_Bridge omogoča razvoj modulov BREZ celotnega ASTRAMENTALICA sistema.
- Če sistem obstaja → uporabi sistemske funkcije (jedro/sistemske_funkcije.php)
- Če sistem NE obstaja → uporabi mini sistem (embed/mini_sistem.php)
- Omogoča testiranje modulov z različnimi vlogami
- Generira nove module in stebelne datoteke
- Pakira module za prodajo


================================================================================
📁 CELOTNA STRUKTURA
================================================================================

MODULI/Modul_Bridge/
│
├── 📋 index.php                         # Glavni orkestrator (vstopna točka)
│
├── 🎯 orkestrator/                      # UPRAVLJANJE MODULOV
│   ├── upravljalec.php                  # Seznam, aktivacija, deaktivacija
│   ├── testnik.php                      # Testiranje modulov
│   └── pakirnik.php                     # Pakiranje za prodajo (ZIP)
│
├── 🔌 jedro/                            # POVEZAVA NA PRAVI SISTEM
│   ├── sistem_preveri.php               # Preveri ali sistem obstaja
│   └── sistemske_funkcije.php           # Klici v pravi sistem (če obstaja)
│
├── 📦 embed/                            # MINI SISTEM (samostojen – brez ASTRAMENTALICE)
│   ├── mini_sistem.php                  # Jedro mini sistema (bootstrap)
│   ├── mini_konstante.php               # Mini konstante (poti, vloge)
│   ├── mini_vloge.php                   # Mini RBAC (gost/uporabnik/admin)
│   ├── mini_seja.php                    # Mini session (PHP $_SESSION)
│   ├── mini_cache.php                   # Mini cache (v seji, ne v PODATKI)
│   ├── mini_baza.php                    # Demo baza (JSON v seji)
│   └── mini_izhod.php                   # HTML render (glava, noga, postavitev)
│
├── 🏭 generator/                        # GENERIRANJE NOVIH MODULOV
│   ├── generiraj_modul.php              # Ustvari nov modul (mapa + osnovne datoteke)
│   └── generiraj_stebelno.php           # Stebelna datoteka (uporabi embed – samostojen modul)
│
├── 🧪 demo/                             # TESTIRANJE IN DEMONSTRACIJA
│   └── vloge_demo.php                   # Testiranje vlog (preklop in prikaz dostopa)
│
└── 📦 stebelne/                         # GENERIRANI STEBELNI MODULI
    └── (sem se shranijo stebelni moduli – vsak v svoji mapi)

Opomba: dejanski vstop je `index.php`.
Bridge logika je v `jedro/sistemske_funkcije.php` in fallback v `embed/mini_sistem.php`.


================================================================================
📋 DATOTEKA: embed/mini_sistem.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_sistem.php
 * POT: MODULI/Modul_Bridge/embed/mini_sistem.php
 * ============================================================
 *
 * 📦 NAMEN:
 *     Jedro mini sistema – uporabi se, ko ASTRAMENTALICA sistem NE OBSTAJA.
 *     Omogoča popolnoma samostojno delovanje Bridge-a.
 *
 * 🔧 ODVISNOSTI:
 *     - mini_konstante.php
 *     - mini_vloge.php
 *     - mini_seja.php
 *     - mini_cache.php
 *     - mini_baza.php
 *     - mini_izhod.php
 *
 * ============================================================
 */

require_once __DIR__ . '/mini_konstante.php';
require_once __DIR__ . '/mini_vloge.php';
require_once __DIR__ . '/mini_seja.php';
require_once __DIR__ . '/mini_cache.php';
require_once __DIR__ . '/mini_baza.php';
require_once __DIR__ . '/mini_izhod.php';

// Inicializacija mini sistema
function mini_inicijalizacija(): void {
    mini_seja_zacni();

    // Privzeta vloga, če ni nastavljena
    if (!mini_je_prijavljen()) {
        mini_prijavi_gosta();
    }
}

// ============================
// PRIDOBI VSE MODULE
// ============================

/**
 * Pridobi vse module iz MODULI/ mape.
 * Nova struktura: MODULI/{ImeModula}/podatki/manifest.json
 * BREZ kategorij.
 */
function mini_moduli_pridobi_vse(): array {
    $moduli = [];

    // Išči module direktno v MODULI/*/podatki/manifest.json
    foreach (glob(MINI_MODULI . '/*', GLOB_ONLYDIR) as $pot_modula) {
        $ime = basename($pot_modula);

        // Preskoči Modul_Bridge
        if ($ime === 'Modul_Bridge') {
            continue;
        }

        // Preveri manifest v podatki/ mapi
        $manifest_pot = $pot_modula . '/podatki/manifest.json';
        if (!file_exists($manifest_pot)) {
            continue;
        }

        $manifest = json_decode(file_get_contents($manifest_pot), true);
        if (!$manifest) {
            continue;
        }

        // Preveri minimalne podatke
        if (!isset($manifest['modul']['id'])) {
            continue;
        }

        $moduli[] = [
            'ime' => $ime,
            'pot' => $pot_modula,
            'manifest' => $manifest,
            'aktiviran' => $manifest['modul']['aktiviran'] ?? true,
            'minimalna_vloga' => $manifest['dostop']['minimalna_vloga'] ?? 'gost'
        ];
    }

    return $moduli;
}

// Bridge funkcije za mini sistem
function bridge_inicijalizacija(): void {
    mini_inicijalizacija();
}

function bridge_prikazi_pregled(): void {
    $moduli = mini_moduli_pridobi_vse();
    mini_izhod_glava('Pregled modulov');
    include __DIR__ . '/../prikaz/pregled.php';
    mini_izhod_noga();
}

function bridge_prikazi_testnik(): void {
    $moduli = mini_moduli_pridobi_vse();
    $trenutni = mini_pridobi_uporabnika();
    include __DIR__ . '/../prikaz/testnik.php';
}

function bridge_prikazi_generator(): void {
    mini_izhod_glava('Generator modulov');
    include __DIR__ . '/../prikaz/generator.php';
    mini_izhod_noga();
}

function bridge_pakiraj_modul(): void {
    // ... logika pakiranja
}


================================================================================
📋 DATOTEKA: generator/generiraj_modul.php
================================================================================

<?php
/**
 * ============================================================
 * GENERATOR: generiraj_modul.php
 * POT: MODULI/Modul_Bridge/generator/generiraj_modul.php
 * ============================================================
 *
 * 📦 NAMEN:
 *     Ustvari nov modul z NOVO strukturo (brez kategorij, s podatki/).
 *
 * 🔧 UPORABA:
 *     POST zahteva z parametri: ime, opis, minimalna_vloga
 *
 * ============================================================
 */

function generiraj_modul(string $ime, string $opis, string $minimalna_vloga = 'gost'): array {
    // Normalizacija imena
    $ime_clean = preg_replace('/[^a-zA-Z0-9]/', '', $ime);
    $id = strtolower($ime_clean);

    // Pot modula: MODULI/{ImeModula}/
    $pot_modula = MINI_MODULI . '/' . $ime_clean . '/';

    // Preveri ali že obstaja
    if (file_exists($pot_modula)) {
        return ['uspeh' => false, 'napaka' => 'Modul že obstaja: ' . $ime_clean];
    }

    // Ustvari mape
    if (!mkdir($pot_modula, 0755, true)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape'];
    }
    if (!mkdir($pot_modula . 'podatki/', 0755, true)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti podatki/ mape'];
    }

    // ============================
    // 1. manifest.json
    // ============================
    $manifest = [
        '_id' => $id,
        '_verzija' => '1.0.0',

        'modul' => [
            'ime' => $ime,
            'ime' => $ime_clean,
            'tip' => 'zbiralec',
            'nivo' => 1,
            'verzija' => '1.0.0',
            'aktiviran' => true,
            'vstopna' => 'modul.php',
            'opis' => $opis,
            'status' => 'razvoj',
            'demo' => false,
            'zacasen' => false
        ],

        'dostop' => [
            'minimalna_vloga' => $minimalna_vloga,
            'plan' => 'osnova',
            'javno_vidno' => false,
            'placljivo' => false,
            'otroski' => false,
            'vidnost' => 'javni',
            'dovoljenja' => ['branje']
        ],

        'cache' => [
            'omogocen' => true,
            'ttl' => 3600
        ],

        'ui' => [
            'ima_prikaz' => true,
            'ikona' => '✨',
            'barva' => '#818cf8',
            'kategorija' => 'splošno',
            'dovoljene_postavitve' => ['standard'],
            'tags' => [],
            'jeziki' => ['sl']
        ],

        'izvajanje' => [
            'tip' => 'ui',
            'api_only' => false,
            'interval' => null,
            'ob_zagonu' => false,
            'prioriteta' => 100,
            'bootstrap' => null
        ],

        'migracije' => [
            'obstajajo' => false,
            'zadnja' => null
        ],

        'integriteta' => [
            'checksum' => null
        ],

        'log' => [
            'omogocen' => true,
            'nivo' => 'info'
        ],

        'cas' => [
            'ustvarjen' => date('c'),
            'posodobljen' => date('c'),
            'zadnji_zagon' => null
        ]
    ];

    file_put_contents(
        $pot_modula . 'podatki/manifest.json',
        json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    // ============================
    // 2. api.json
    // ============================
    $api = [
        '_id' => $id,
        '_verzija' => '1.0.0',

        'kanali' => ['api'],

        'vstop' => [
            'web' => 'modul.php'
        ],

        'javne_metode' => [
            'info'
        ],

        'http_poti' => [
            '/' . $id . '/info'
        ]
    ];

    file_put_contents(
        $pot_modula . 'podatki/api.json',
        json_encode($api, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    // ============================
    // 3. izhod.json
    // ============================
    $izhod = [
        '_id' => $id,
        '_verzija' => '1.0.0',

        'vhod' => [
            'potrebuje' => [],
            'opcijsko' => [],
            'vir' => 'uporabnik',
            'validacija' => null,
            'omejitve' => [
                'max_velikost' => null
            ]
        ],

        'izhod' => [
            'format' => 'json',
            'pise_v' => []
        ],

        'odvisnosti' => [
            'bere_iz' => [],
            'prepovedani_moduli' => [],
            'ne_pozna' => 'vse_ostalo',
            'kompatibilnost' => [
                'min_sistem' => '2.0.0',
                'max_sistem' => null
            ]
        ],

        'cache' => [
            'omogocen' => true,
            'ttl' => 3600,
            'strategija' => 'casovna',
            'cisti_ob_zagonu' => false
        ],

        'ui' => [
            'varuh' => null,
            'duhec' => null
        ],

        'dogodki' => [
            'poslusa' => [],
            'oddaja' => []
        ]
    ];

    file_put_contents(
        $pot_modula . 'podatki/izhod.json',
        json_encode($izhod, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    // ============================
    // 4. modul.php
    // ============================
    $modul_php = '<?php
/**
 * ============================================================
 * POT: MODULI/' . $ime_clean . '/modul.php
 * 📅 VERZIJA: 1.0.0
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Glavna logika modula ' . $ime_clean . '.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_' . $id . '_akcija(string $akcija, array $podatki): array
 *
 * 🚫 PREPOVEDI:
 *     - Brez HTML
 *     - Brez require_once na sistemske poti
 *     - Brez __DIR__ za izhod iz lastne mape
 *     - Brez $_POST, $_GET, $_SESSION direktno
 *     - Brez globalnih spremenljivk
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */
declare(strict_types=1);

function modul_' . $id . '_akcija(string $akcija, array $podatki = []): array {
    // Preveri vlogo preko Bridge-a
    if (!Modul_Bridge::vloga_preveri(\'' . $minimalna_vloga . '\')) {
        return [\'napaka\' => \'Nimaš dostopa\'];
    }

    // Dobi uporabnika preko Bridge-a
    $uporabnik = Modul_Bridge::uporabnik_pridobi();

    return match($akcija) {
        \'info\'    => [
            \'ime\' => \'' . $ime_clean . '\',
            \'verzija\' => \'1.0.0\',
            \'opis\' => \'' . addslashes($opis) . '\',
            \'uporabnik\' => $uporabnik
        ],
        default   => [\'napaka\' => \'Neznana akcija: \' . $akcija]
    };
}

### Prikazno ime in ID
- `id` je tehnični identifikator modula in naj bo v lowercase brez presledkov.
- `ime` je dejansko ime modula za prikaz uporabniku.
- Če sta enaka, je to dovoljeno.
';

    file_put_contents($pot_modula . 'modul.php', $modul_php);

    // ============================
    // 5. modul.md (dokumentacija)
    // ============================
    $modul_md = '# ' . $ime_clean . '

**ID:** ' . $id . '
**Verzija:** 1.0.0
**Tip:** zbiralec
**Status:** razvoj

---

## Opis

' . $opis . '

---

## Dostop

- **Minimalna vloga:** ' . $minimalna_vloga . '
- **Plan:** osnova

---

## Uporaba

```bash
curl http://example.com/' . $id . '/info
Changelog
1.0.0 (' . date('Y-m-d') . ')
Prva izdaja
';

file_put_contents(
p
o
t
m
o
d
u
l
a
.
′
p
o
d
a
t
k
i
/
m
o
d
u
l
.
m
d
′
,
pot 
m
​
 odula. 
′
 podatki/modul.md 
′
 ,modul_md);

return ['uspeh' => true, 'pot' => 
p
o
t
m
o
d
u
l
a
,
′
i
m
e
′
=
>
pot 
m
​
 odula, 
′
 ime 
′
 =>ime_clean];
}

================================================================================
📋 DATOTEKA: jedro/sistemske_funkcije.php
================================================================================

<?php /** * ============================================================ * JEDRO: sistemske_funkcije.php * POT: MODULI/Modul_Bridge/jedro/sistemske_funkcije.php * ============================================================ * * 📦 NAMEN: * Klici v pravi ASTRAMENTALICA sistem (če obstaja). * Bridge uporablja sistemske funkcije namesto mini sistema. * * ============================================================ */ function bridge_inicijalizacija(): void { // Uporabi pravo ASTRAMENTALICA sejo if (function_exists('seja_zacni')) { seja_zacni(); } } /** * Pridobi vse module iz MODULI/ mape. * Nova struktura: MODULI/{ImeModula}/podatki/manifest.json * BREZ kategorij. */ function _bridge_moduli_iz_map(): array { $moduli = []; // Išči module direktno v MODULI/*/podatki/manifest.json foreach (glob(POT_MODULI . '/*', GLOB_ONLYDIR) as $pot_modula) { $ime = basename($pot_modula); // Preskoči Modul_Bridge if ($ime === 'Modul_Bridge') { continue; } $manifest_pot = $pot_modula . '/podatki/manifest.json'; if (!file_exists($manifest_pot)) { continue; } $manifest = json_decode(file_get_contents($manifest_pot), true); if (!$manifest || !isset($manifest['modul']['id'])) { continue; } $moduli[] = [ 'ime' => $ime, 'pot' => $pot_modula, 'manifest' => $manifest, 'aktiviran' => $manifest['modul']['aktiviran'] ?? true ]; } return $moduli; } function bridge_prikazi_pregled(): void { $moduli = _bridge_moduli_iz_map(); require_once POT_GLOBALNO . '/render/glava.php'; include __DIR__ . '/../prikaz/pregled.php'; require_once POT_GLOBALNO . '/render/noga.php'; } function bridge_prikazi_testnik(): void { $moduli = _bridge_moduli_iz_map(); $trenutni = ['vloga' => trenutna_vloga()]; require_once POT_GLOBALNO . '/render/glava.php'; include __DIR__ . '/../prikaz/testnik.php'; require_once POT_GLOBALNO . '/render/noga.php'; } function bridge_prikazi_generator(): void { require_once POT_GLOBALNO . '/render/glava.php'; include __DIR__ . '/../prikaz/generator.php'; require_once POT_GLOBALNO . '/render/noga.php'; } ================================================================================ 📋 DATOTEKA: orkestrator/upravljalec.php ================================================================================ <?php /** * ============================================================ * ORKESTRATOR: upravljalec.php * POT: MODULI/Modul_Bridge/orkestrator/upravljalec.php * ============================================================ * * 📦 NAMEN: * Upravljanje modulov (seznam, aktivacija, deaktivacija). * * ============================================================ */ /** * Pridobi vse module iz MODULI/ mape. * Nova struktura: MODULI/{ImeModula}/podatki/manifest.json * BREZ kategorij. */ function upravljalec_moduli_pridobi_vse(): array { $moduli = []; foreach (glob(MINI_MODULI . '/*', GLOB_ONLYDIR) as $pot_modula) { $ime = basename($pot_modula); if ($ime === 'Modul_Bridge') { continue; } $manifest_pot = $pot_modula . '/podatki/manifest.json'; if (!file_exists($manifest_pot)) { continue; } $manifest = json_decode(file_get_contents($manifest_pot), true); if (!$manifest || !isset($manifest['modul']['id'])) { continue; } $moduli[] = [ 'ime' => $ime, 'pot' => $pot_modula, 'manifest' => $manifest, 'aktiviran' => $manifest['modul']['aktiviran'] ?? false, 'minimalna_vloga' => $manifest['dostop']['minimalna_vloga'] ?? 'gost' ]; } return $moduli; } function upravljalec_aktiviraj(string $ime): bool { $pot = MINI_MODULI . '/' . $ime . '/podatki/manifest.json'; if (!file_exists($pot)) { return false; } $manifest = json_decode(file_get_contents($pot), true); if (!$manifest) { return false; } $manifest['modul']['aktiviran'] = true; return file_put_contents($pot, json_encode($manifest, JSON_PRETTY_PRINT)) !== false; } function upravljalec_deaktiviraj(string $ime): bool { $pot = MINI_MODULI . '/' . $ime . '/podatki/manifest.json'; if (!file_exists($pot)) { return false; } $manifest = json_decode(file_get_contents($pot), true); if (!$manifest) { return false; } $manifest['modul']['aktiviran'] = false; return file_put_contents($pot, json_encode($manifest, JSON_PRETTY_PRINT)) !== false; } ================================================================================ 📋 DATOTEKA: orkestrator/pakirnik.php ================================================================================ <?php /** * ============================================================ * ORKESTRATOR: pakirnik.php * POT: MODULI/Modul_Bridge/orkestrator/pakirnik.php * ============================================================ * * 📦 NAMEN: * Pakiranje modulov za prodajo (ZIP). * * ============================================================ */ function pakirnik_modul_pakiraj(string $ime): array { $pot_modula = MINI_MODULI . '/' . $ime . '/'; if (!file_exists($pot_modula)) { return ['uspeh' => false, 'napaka' => 'Modul ne obstaja']; } // Preveri ali ima podatki/manifest.json if (!file_exists($pot_modula . 'podatki/manifest.json')) { return ['uspeh' => false, 'napaka' => 'Manjka podatki/manifest.json']; } // Ustvari ZIP $zip_pot = MINI_BRIDGE . '/stebelne/' . $ime . '.zip'; $zip = new ZipArchive(); if ($zip->open($zip_pot, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) { return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti ZIP']; } // Dodaj celotno mapo modula $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($pot_modula), RecursiveIteratorIterator::LEAVES_ONLY ); foreach ($files as $name => $file) { if (!$file->isDir()) { $filePath = $file->getRealPath(); $relativePath = substr($filePath, strlen(MINI_MODULI)); $zip->addFile($filePath, $relativePath); } } $zip->close(); return ['uspeh' => true, 'pot' => $zip_pot]; } ================================================================================ 📋 DATOTEKA: prikaz/pregled.php (odlomek – brez kategorij) ================================================================================ <div class="card"> <h2>📦 Pregled modulov</h2> <?php if (empty($moduli)): ?> <p>Ni modulov.</p> <?php else: ?> <?php foreach ($moduli as $m): ?> <div class="modul-item"> <strong><?= htmlspecialchars($m['ime']) ?></strong> <span class="badge <?= $m['aktiviran'] ? 'badge-aktiviran' : 'badge-neaktiviran' ?>"> <?= $m['aktiviran'] ? '✅ Aktiviran' : '❌ Neaktiven' ?> </span> <br> <?= htmlspecialchars($m['manifest']['modul']['opis'] ?? '') ?> <br> <small>Vloga: <?= htmlspecialchars($m['minimalna_vloga'] ?? 'gost') ?></small> <br> <a href="?akcija=testnik&modul=<?= urlencode($m['ime']) ?>" class="btn btn-small">🧪 Test</a> </div> <?php endforeach; ?> <?php endif; ?> </div>
================================================================================
📋 DATOTEKA: prikaz/testnik.php (odlomek – brez kategorij)
================================================================================

<div class="card"> <h2>🧪 Testnik vlog</h2> <div class="current-vloga"> <strong>🔑 Trenutna vloga:</strong> <?php if ($trenutni['vloga'] >= 60): ?> 👑 Administrator <?php elseif ($trenutni['vloga'] >= 10): ?> 👤 Uporabnik <?php else: ?> 🚪 Gost <?php endif; ?> </div> <div class="vloga-buttons"> <a href="?vloga=0" class="vloga-btn">🚪 Gost</a> <a href="?vloga=10" class="vloga-btn">👤 Uporabnik</a> <a href="?vloga=60" class="vloga-btn">👑 Admin</a> </div> </div><div class="card"> <h2>📦 Moduli</h2> <?php if (empty($moduli)): ?> <p>Ni modulov.</p> <?php else: ?> <?php foreach ($moduli as $m): ?> <div class="modul-item"> <strong><?= htmlspecialchars($m['ime']) ?></strong> <br> <?php $minVloga = $m['minimalna_vloga'] ?? 'gost'; $minVlogaInt = match($minVloga) { 'admin' => 60, 'S5' => 60, 'S4' => 50, 'S3' => 40, 'S2' => 30, 'S1' => 20, 'S0' => 10, default => 0 }; $dostop = $trenutni['vloga'] >= $minVlogaInt; ?> <span>Zahtevana vloga: <?= htmlspecialchars($minVloga) ?></span> | <?php if ($dostop): ?> <span class="dostop-da">✅ Dovoljen</span> <?php else: ?> <span class="dostop-ne">❌ Zavrnjen</span> <?php endif; ?> </div> <?php endforeach; ?> <?php endif; ?> </div>
================================================================================
📋 DATOTEKA: prikaz/generator.php (odlomek – brez kategorij)
================================================================================

<div class="card"> <h2>🏭 Generiraj nov modul</h2> <form method="POST" action="?akcija=generiraj"> <div class="form-group"> <label for="ime">Ime modula:</label> <input type="text" id="ime" name="ime" required placeholder="Npr. MojModul" pattern="[A-Za-z0-9]+"> <small>Samo črke in številke, brez presledkov.</small> </div> <div class="form-group"> <label for="opis">Opis:</label> <textarea id="opis" name="opis" rows="3" required placeholder="Kratek opis modula..."></textarea> </div> <div class="form-group"> <label for="minimalna_vloga">Minimalna vloga:</label> <select id="minimalna_vloga" name="minimalna_vloga"> <option value="gost">🚪 Gost</option> <option value="S0">S0</option> <option value="S1">S1</option> <option value="S2">S2</option> <option value="S3">S3</option> <option value="S4">S4</option> <option value="S5">S5</option> <option value="admin">👑 Admin</option> </select> </div>
<button type="submit" class="btn">🚀 Generiraj modul</button>

</form> <?php if (isset($_POST['ime'])): ?> <?php $rezultat = generiraj_modul($_POST['ime'], $_POST['opis'], $_POST['minimalna_vloga'] ?? 'gost'); ?> <?php if ($rezultat['uspeh']): ?> <div class="success"> ✅ Modul <strong><?= htmlspecialchars($rezultat['ime']) ?></strong> je bil ustvarjen! <br> Pot: <code><?= htmlspecialchars($rezultat['pot']) ?></code> </div> <?php else: ?> <div class="error"> ❌ <?= htmlspecialchars($rezultat['napaka']) ?> </div> <?php endif; ?> <?php endif; ?> </div>
================================================================================
✅ POVZETEK – KDAJ SE KAJ UPORABI
================================================================================

Komponenta	Kdaj se uporabi	Odvisnosti
jedro/sistem_preveri.php	Vedno (najprej) – preveri sistem	Nobenih
jedro/sistemske_funkcije.php	Ko sistem OBSTAJA	SISTEM/kernel/
embed/mini_sistem.php	Ko sistema NI (demo, stebelne)	SAMO mini_* (vse v embed/)
generator/generiraj_modul.php	Ko uporabnik želi nov modul	mini_sistem ali sistemski
generator/generiraj_stebelno.php	Ko uporabnik želi samostojen modul	embed/mini_sistem.php
demo/vloge_demo.php	Ko uporabnik želi testirati dostop	embed/mini_sistem.php
================================================================================
KONEC MODUL BRIDGE STRUKTURE
================================================================================

text

---

## ✅ PREGLED POPRAVKOV

| Datoteka | Kar sem popravil |
|----------|------------------|
| `MODULI.md` | Celotna nova struktura, brez kategorij, s `podatki/` mapo, kanonični manifesti |
| `MODUL_BRIDGE.md` | Vse datoteke usklajene z novo strukturo |

### Ključne spremembe v `MODUL_BRIDGE.md`:

1. **`mini_sistem.php`** → `mini_moduli_pridobi_vse()` išče module direktno v `MODULI/*/podatki/manifest.json`
2. **`generiraj_modul.php`** → ustvarja NOVO strukturo (brez kategorij, s `podatki/`)
3. **`sistemske_funkcije.php`** → `_bridge_moduli_iz_map()` išče direktno v `MODULI/*/podatki/manifest.json`
4. **`upravljalec.php`** → išče module direktno v `MODULI/*/podatki/manifest.json`
5. **`pakirnik.php`** → preverja `podatki/manifest.json`
6. **`pregled.php`** → odstranjeno združevanje po kategorijah
7. **`testnik.php`** → odstranjene kategorije
8. **`generator.php`** → odstranjena izbira kategorij

---

## 🕊️ KONČNO STANJE

- **Brez kategorij** (NEBO, ZEMLJA, SIMBOLI ... so ukinjeni)
- **Nova struktura**: `MODULI/{ImeModula}/podatki/manifest.json`
- **Kanonični manifesti** (iz `zaklenjena struktura manifestov.txt`)
- **Modul pozna samo**: svojo mapo in Modul_Bridge
- **Vse sledi stare strukture odstranjene**

> **To je to. Specifikacija je zaklenjena. Lahko jo uporabljaš za vedno.** 🕊️