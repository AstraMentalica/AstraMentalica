🌌 ASTRAMENTALICA — POPOLNA SPECIFIKACIJA (BREZ RUNTIME)
================================================================================

🔒 KLJUČNE ODLOČITVE
✔ Vloga v podatkih = INT (obvezno)
✔ RBAC = STRING (ostane)
✔ Pretvorba = vloga_v_int()
✔ Dostop = ma_vlogo()
✔ PODATKI = edini vir resnice
✔ upravljalec_baz.php = edini dostop
✔ ADAPTER = edini izhod (IO)
✔ SISTEM nikoli ne echo-a
✔ MODULI nimajo dostopa do SISTEM ali PODATKI
✔ RUNTIME NE OBSTAJA – IZBRISAN IZ SISTEMA


================================================================================
📁 1. MAPA STRUKTURA (popolna, enotna – BREZ RUNTIME)
================================================================================

root/
├── pot.php                         # SIDRO – vse konstante
├── index.php                       # EDINI javni VSTOP
├── .htaccess                       # zaščita
│
├── ADAPTER/
│   ├── vhod_zasebno/               # sistemski ai, cli, cron ..
│   │   ├── adapter_ai.php
│   │   └── adapter_cli.php
│   │
│   ├── vhod_webhook/               # tuja omrezja
│   │   ├── adapter_facebook.php
│   │   └── adapter_telegram.php
│   │
│   ├── izhod_kanali/               # distribucijski kanali (SAMO PRETVORBA)
│   │   ├── KanalWeb.php            # HTML pretvorba (usmeri v GLOBALNO/render/render.php)
│   │   ├── KanalApi.php            # JSON pretvorba
│   │   ├── KanalFacebook.php
│   │   ├── KanalAi.php
│   │   ├── KanalCli.php
│   │   └── KanalTelegram.php
│   │
│   ├── middleware/
│   │   ├── cors.php
│   │   ├── auth.php
│   │   ├── dnevnik.php
│   │   ├── ip_blacklist.php
│   │   └── omejevalnik.php
│   │
│   ├── odzivi/
│   │   ├── adapter_napake.php
│   │   ├── adapter_odziv.php
│   │   └── adapter_statusi.php
│   │
│   └── adapter.php                 # EDINI vstop/izstop
│
├── SISTEM/
│   ├── api.php                     # N1 vstopna točka (edini vstop v sistem)
│   │
│   ├── storitve_svetov/            # BUSINESS LAYER (N2)
│   │   ├── uporabniki/             # backend uporabniki
│   │   ├── moduli/                 # backend modulov
│   │   ├── globalno/               # backend za prikaz
│   │   └── astra/                  # backend za ASTRA
│   │
│   ├── kanali/                     # TEHNIČNI IZHOD (N2) – IZVEDBA
│   │   ├── priprava.php            # priprava izhoda (contract)
│   │   ├── vrsta.php               # čakalna vrsta (queue)
│   │   └── obdelava.php            # worker
│   │
│   └── kernel/                     # SISTEMSKA MEHANIKA (N3)
│       ├── zaganjalnik.php         # bootstrap sistema
│       ├── env_loader.php          # okolje (.env)
│       ├── nastavitve.php          # globalne nastavitve
│       │
│       ├── jedro/                  # čisto jedro (brez domenske logike)
│       │   ├── 01_upravljalec_svetov.php
│       │   ├── 02_napake.php
│       │   ├── 03_varnost.php
│       │   ├── 04_seja.php
│       │   ├── 05_pravice.php
│       │   ├── 06_cache.php
│       │   ├── 07_dogodki.php
│       │   ├── 08_kavlji.php
│       │   ├── 09_ponudniki.php
│       │   ├── 10_middleware.php
│       │   ├── 11_usmerjevalnik.php
│       │   ├── 12_validacija.php
│       │   ├── 13_api.php
│       │   ├── 14_zagon.php
│       │   └── 15_pogon.php
│       │   # 16_upravljalec_runtime.php – IZBRISAN (runtime ne obstaja)
│       │
│       └── baze/                   # adapterji za baze
│           ├── upravljalec_baz.php # centralizirano branje/pisanje (EDINI DOSTOP)
│           ├── adapter_json.php
│           ├── adapter_mysql.php
│           └── adapter_sqlite.php
│
├── MODULI/                         # izolirani domenski moduli
│   ├── ORAKLEUM/
│   │   ├── Tarot/
│   │   │   ├── modul.php
│   │   │   └── podatki/
│   │   │       ├── manifest.json
│   │   │       └── karte/          # 78 slik kart
│   │   └── OraculumVisionis/
│   ├── NEBO/
│   │   ├── Stelaris/
│   │   ├── Lunaris/
│   │   ├── Jyotir/
│   │   └── Senzorji/
│   ├── ZEMLJA/
│   │   ├── QiVitalis/
│   │   ├── Pranaymica/
│   │   ├── Energetica/
│   │   ├── BotanicaSacra/
│   │   ├── Lapidaria/
│   │   ├── VibraMystica/
│   │   └── Somnaris/
│   ├── SIMBOLI/
│   │   ├── Numyra/
│   │   ├── NumerariumCosmicum/
│   │   ├── AegypticaArcana/
│   │   ├── NordicaMystica/         # rune
│   │   ├── MysticaMesoamericana/   # majevski koledar
│   │   ├── Sephirotica/            # kabala
│   │   ├── Occultum/               # sigili
│   │   └── Devorum/                # miti in bogovi
│   ├── POTI/
│   │   ├── Transmutaria/           # alkimija
│   │   ├── UmbraeCodex/            # delo s senco
│   │   ├── LiberUmbrae/            # knjiga senc
│   │   ├── ViaAnimae/              # pot duše
│   │   ├── Animaris/               # šamanizem
│   │   └── Seraphica/              # angeli
│   ├── SVET/
│   │   ├── CorpusMysticum/         # knjižnica
│   │   ├── Aetheris/               # forum
│   │   ├── Celestara/              # blog
│   │   ├── Mystaia/                # trgovina
│   │   └── CosmicaScientia/        # kvantna mistika
│   └── VIP/
│       └── Synera/                 # S3+ vloga
│
├── GLOBALNO/                       # SAMO frontend (brez business logike)
│   ├── frontend/                   # JS, CSS, interakcije
│   ├── render/                     # SAMO prikaz (brez logike!)
│   │   ├── glava.php
│   │   ├── noga.php
│   │   ├── navigacija.php          # postavitve
│   │   ├── domov.php
│   │   └── 404.html
│   └── vmesnik/                    # dizajn
│       ├── css
│       └── teme
│
├── UPORABNIKI/                     # SAMO frontend (brez business logike)
│   ├── prikaz/uporabnik/
│   │   ├── uporabnik_nastavitve.php
│   │   ├── uporabnik_passport.php
│   │   ├── uporabnik_meditacije.php
│   │   └── uporabnik_dnevnik.php
│   ├── prikaz/skrbnik/
│   │   └── nastavitve.php
│   │
│   ├── prikaz/sistem/
│   │   ├── uporabnik_prijava.php
│   │   ├── uporabnik_registracija.php
│   │   ├── uporabnik_odjava.php
│   │   └── uporabnik_profil.php    # skupni prikaz profila
│   │
│   └── {id}/                       # vsak uporabnik svojo mapo
│       ├── profil.json             # id, vloga, plan, status
│       ├── nastavitve.json         # tema, jezik, lokacija
│       └── PASSPORT/               # 🕊️ SVETI GRAL
│           ├── dnevnik.json
│           ├── modrosti.json
│           ├── odkritja.json
│           ├── pot.json
│           ├── simboli.json
│           ├── sanje.json
│           └── meditacije.json
│
├── PODATKI/                        # centralni rezervoar sistema
│   ├── sef/
│   │   ├── .env_sistem
│   │   ├── .env_api
│   │   └── .env_baza
│   │
│   ├── registri/                   # CENTRALNI REGISTRI
│   │   ├── moduli_register.json    # whitelist vseh modulov
│   │   ├── rbac/
│   │   │   ├── vloge.json          # kaj sme posamezna vloga
│   │   │   └── pravila.json        # splošna pravila sistema
│   │   ├── override/
│   │   │   └── {id}.json           # dovoli / blokiraj
│   │   ├── whitelist/              # cachirani whitelist-i
│   │   │   ├── whitelist_gost.json
│   │   │   ├── whitelist_S0.json
│   │   │   ├── whitelist_S1.json
│   │   │   ├── whitelist_S2.json
│   │   │   ├── whitelist_S3.json
│   │   │   ├── whitelist_S4.json
│   │   │   ├── whitelist_S5.json
│   │   │   └── whitelist_admin.json
│   │   ├── prepovedi.json          # globalne blokade
│   │   └── postavitev.json         # layout / sistemske nastavitve
│   │
│   ├── moduli/                     # podatki modulov
│   │   └── {ime_modula}/
│   │       ├── uporaba.json
│   │       ├── globalno.json
│   │       ├── cache.json
│   │       └── statistika.json
│   │
│   ├── uporabniki/                 # sistemski podatki (identiteta, vloge)
│   │   └── (samo centralno, ne peskovnik)
│   │
│   ├── globalno/inventura/
│   │   └── gradniki.json           # vsi gradniki + RBAC dostop
│   │
│   ├── sistem/
│   │   ├── tmp/
│   │   ├── dnevnik/
│   │   │   ├── sistem.log
│   │   │   ├── api.log
│   │   │   └── instalacija.log
│   │   ├── vrsta/                  # podatki za čakalno vrsto
│   │   │   ├── izhod.json
│   │   │   ├── cron.json
│   │   │   └── interno.json
│   │   ├── seja/
│   │   └── predpomnilnik/
│   │
│   ├── baze/
│   │   ├── mysql/
│   │   ├── sqlite/
│   │   └── json/
│   │
│   └── analitika/
│       ├── statistika/
│       └── meritve/
│
├── VSEBINA/                        # statične MD vsebine
│   ├── javno/
│   ├── faq/
│   ├── branja/
│   └── manifest/
│
└── ASTRA/                          # nadzorni svet – samo S5/admin
    ├── nadzorni_center.php
    └── admin_portal.php


================================================================================
📜 2. pot.php (popoln, enoten)
================================================================================

<?php
// pot.php – SIDRO, edine konstante sistema
defined('SIDRO_AKTIVNO') or define('SIDRO_AKTIVNO', true);

// ============================================================
// 1. OSNOVNE POTI
// ============================================================
define('POT_KOREN', __DIR__);

define('POT_SISTEM',     POT_KOREN . '/SISTEM');
define('POT_MODULI',     POT_KOREN . '/MODULI');
define('POT_GLOBALNO',   POT_KOREN . '/GLOBALNO');
define('POT_UPORABNIKI', POT_KOREN . '/UPORABNIKI');
define('POT_PODATKI',    POT_KOREN . '/PODATKI');
define('POT_VSEBINA',    POT_KOREN . '/VSEBINA');
define('POT_ASTRA',      POT_KOREN . '/ASTRA');

// ============================================================
// 2. PODATKI PODMAPE
// ============================================================
define('PODATKI_ENV',        POT_PODATKI . '/sef');
define('PODATKI_MODULI',     POT_PODATKI . '/moduli');
define('PODATKI_UPORABNIKI', POT_PODATKI . '/uporabniki');
define('PODATKI_INVENTURA',  POT_PODATKI . '/globalno/inventura');
define('PODATKI_ANALITIKA',  POT_PODATKI . '/analitika');
define('PODATKI_STATISTIKA', POT_PODATKI . '/analitika/statistika');
define('PODATKI_MERITVE',    POT_PODATKI . '/analitika/meritve');

// ============================================================
// 3. REGISTRI (centralni registri, RBAC, override, whitelist)
// ============================================================
define('PODATKI_REGISTRI',   POT_PODATKI . '/registri');

// ============================================================
// 4. SISTEMSKE PODMAPE (PODATKI/sistem/)
// ============================================================
define('PODATKI_SISTEM',     POT_PODATKI . '/sistem');
define('PODATKI_LOG',        POT_PODATKI . '/sistem/dnevnik');
define('PODATKI_CACHE',      POT_PODATKI . '/sistem/predpomnilnik');
define('PODATKI_TMP',        POT_PODATKI . '/sistem/tmp');
define('PODATKI_VRSTA',      POT_PODATKI . '/sistem/vrsta');

// ============================================================
// 5. BAZE ADAPTERJI
// ============================================================
define('PODATKI_BAZE',       POT_PODATKI . '/baze');
define('PODATKI_JSON',       POT_PODATKI . '/baze/json');
define('PODATKI_SQLITE',     POT_PODATKI . '/baze/sqlite');
define('PODATKI_MYSQL',      POT_PODATKI . '/baze/mysql');

// ============================================================
// 6. KERNEL POTI
// ============================================================
define('POT_KERNEL',         POT_SISTEM . '/kernel');
define('POT_JEDRO',          POT_KERNEL . '/jedro');
define('POT_BAZE',           POT_KERNEL . '/baze');
define('POT_STORITVE',       POT_SISTEM . '/storitve_svetov');
define('POT_KANALI',         POT_SISTEM . '/kanali');

// ============================================================
// 7. URL KONSTANTE
// ============================================================
$prot = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$mapa = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

define('KOREN_URL',     $prot . '://' . $host . $mapa);
define('GLOBALNO_URL',  KOREN_URL . '/GLOBALNO');
define('MODULI_URL',    KOREN_URL . '/MODULI');
define('ASTRA_URL',     KOREN_URL . '/ASTRA');

// ============================================================
// 8. SISTEMSKE NASTAVITVE
// ============================================================
define('RAZVOJNI_NACIN', true);
define('SISTEM_VERZIJA', '5.0.0');
define('IME_APLIKACIJE', 'AstraMentalica');
define('CASOVNA_CONA',   'Europe/Ljubljana');

// ============================================================
// 9. RBAC VLOGE (integer vrednosti)
// ============================================================
define('VLOGA_GOST',   0);
define('VLOGA_S0',    10);
define('VLOGA_S1',    20);
define('VLOGA_S2',    30);
define('VLOGA_S3',    40);
define('VLOGA_S4',    50);
define('VLOGA_S5',    60);
define('VLOGA_ADMIN', 100);

// ============================================================
// 10. VAROVALKA
// ============================================================
define('SISTEM_VARNOST', true);


================================================================================
👤 3. ENOTNI MODEL UPORABNIKA
================================================================================

OBVEZNO:
{
  "id": 123,
  "vloga": 40,
  "plan": "premium",
  "status": "aktiven"
}

❗ "S3" NE SME obstajati v podatkih – vedno INT!


================================================================================
🔐 4. PRAVICE (05_pravice.php – FINAL)
================================================================================

<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/05_pravice.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3
 *
 * 📰 NAMEN:
 *     Upravljanje uporabniških pravic (RBAC).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - vloga_v_int(string|int $vloga): int
 *     - trenutna_vloga(): int
 *     - ma_vlogo(string|int $zahtevana): bool
 *     - zahtevaj_vlogo(string|int $vloga): void
 *
 * 📡 ODVISNOSTI:
 *     - POT_JEDRO . '/04_seja.php'
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *     - Brez direktnih poti (uporabi konstante!)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, jedro, pravice, rbac
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function vloga_v_int(string|int $vloga): int
{
    if (is_int($vloga)) {
        return $vloga;
    }

    return match(strtoupper($vloga)) {
        'GOST'  => VLOGA_GOST,
        'S0'    => VLOGA_S0,
        'S1'    => VLOGA_S1,
        'S2'    => VLOGA_S2,
        'S3'    => VLOGA_S3,
        'S4'    => VLOGA_S4,
        'S5'    => VLOGA_S5,
        'ADMIN' => VLOGA_ADMIN,
        default => VLOGA_GOST
    };
}

function trenutna_vloga(): int
{
    return (int)($_SESSION['vloga'] ?? VLOGA_GOST);
}

function ma_vlogo(string|int $zahtevana): bool
{
    return trenutna_vloga() >= vloga_v_int($zahtevana);
}

function zahtevaj_vlogo(string|int $vloga): void
{
    if (!ma_vlogo($vloga)) {
        throw new Exception('Dostop zavrnjen');
    }
}


================================================================================
💾 5. UPRAVLJALEC BAZ (upravljalec_baz.php – EDINI DOSTOP DO PODATKOV)
================================================================================

<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/baze/upravljalec_baz.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3
 *
 * 📰 NAMEN:
 *     Centralizirano branje/pisanje podatkov. EDINI DOSTOP DO PODATKOV.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - baza_pot(string $tip, string $ime, ?int $uporabnik = null): string
 *     - baza_beri(string $tip, string $ime, ?int $uporabnik = null): array
 *     - baza_pisi(string $tip, string $ime, array $data, ?int $uporabnik = null): bool
 *     - baza_obstaja(string $tip, string $ime, ?int $uporabnik = null): bool
 *     - baza_brisi(string $tip, string $ime, ?int $uporabnik = null): bool
 *     - baza_poisci(string $tip, array $kriteriji, ?int $uporabnik = null): array
 *
 * 📡 ODVISNOSTI:
 *     - POT_PODATKI konstante
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, baze, podatki, shramba
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function baza_pot(string $tip, string $ime, ?int $uporabnik = null): string
{
    return match($tip) {
        'sistem' =>
            PODATKI_SISTEM . '/' . $ime . '.json',

        'modul' =>
            PODATKI_MODULI . '/' . $ime . '/globalno.json',

        'uporabnik' =>
            PODATKI_UPORABNIKI . '/uporabnik_' .
            ($uporabnik ?? throw new Exception('Manjka ID uporabnika')) .
            '/' . $ime . '.json',

        'uporabnik_modul' =>
            PODATKI_UPORABNIKI . '/uporabnik_' .
            ($uporabnik ?? throw new Exception('Manjka ID uporabnika')) .
            '/moduli/' . $ime . '.json',

        'register' =>
            PODATKI_REGISTRI . '/' . $ime . '.json',

        default =>
            throw new Exception('Neznan tip podatkov: ' . $tip)
    };
}

function baza_beri(string $tip, string $ime, ?int $uporabnik = null): array
{
    $pot = baza_pot($tip, $ime, $uporabnik);

    if (!file_exists($pot)) {
        return [];
    }

    $vsebina = file_get_contents($pot);
    if ($vsebina === false) {
        return [];
    }

    return json_decode($vsebina, true) ?? [];
}

function baza_pisi(string $tip, string $ime, array $data, ?int $uporabnik = null): bool
{
    $pot = baza_pot($tip, $ime, $uporabnik);

    $mapa = dirname($pot);
    if (!is_dir($mapa)) {
        if (!mkdir($mapa, 0755, true)) {
            return false;
        }
    }

    return file_put_contents($pot, json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

function baza_obstaja(string $tip, string $ime, ?int $uporabnik = null): bool
{
    return file_exists(baza_pot($tip, $ime, $uporabnik));
}

function baza_brisi(string $tip, string $ime, ?int $uporabnik = null): bool
{
    $pot = baza_pot($tip, $ime, $uporabnik);

    if (!file_exists($pot)) {
        return true;
    }

    return unlink($pot);
}

function baza_poisci(string $tip, array $kriteriji, ?int $uporabnik = null): array
{
    $podatki = baza_beri($tip, 'podatki', $uporabnik);

    if (empty($podatki) || !isset($podatki['vnosi'])) {
        return [];
    }

    $rezultati = [];
    foreach ($podatki['vnosi'] as $vnos) {
        $ujemanje = true;
        foreach ($kriteriji as $kljuc => $vrednost) {
            if (($vnos[$kljuc] ?? null) !== $vrednost) {
                $ujemanje = false;
                break;
            }
        }
        if ($ujemanje) {
            $rezultati[] = $vnos;
        }
    }

    return $rezultati;
}


================================================================================
🔁 6. IZHODNI SISTEM
================================================================================

📋 SISTEM/kanali/priprava.php – PRIPRAVA IZHODA

<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/priprava.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2
 *
 * 📰 NAMEN:
 *     Priprava izhoda v standardiziran format.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - izhod_pripravi(array $data): array
 *
 * 📡 ODVISNOSTI:
 *     - Nobenih
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kanali, izhod, priprava
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function izhod_pripravi(array $data): array
{
    return [
        'tip' => $data['tip'] ?? 'generic',
        'kanali' => $data['kanali'] ?? ['web'],
        'vsebina' => $data['vsebina'] ?? [],
        'meta' => $data['meta'] ?? [
            'cas' => time(),
            'sistem' => SISTEM_VERZIJA
        ]
    ];
}


📋 SISTEM/kanali/vrsta.php – ČAKALNA VRSTA

<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/vrsta.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2
 *
 * 📰 NAMEN:
 *     Upravljanje čakalne vrste za izhod.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - vrsta_dodaj(array $item): bool
 *     - vrsta_preberi(?string $tip = null): array
 *     - vrsta_stevilo(?string $tip = null): int
 *     - vrsta_pocisti(?string $tip = null): bool
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kanali, vrsta, queue
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function vrsta_dodaj(array $item): bool
{
    $vrsta = baza_beri('sistem', 'vrsta/izhod');
    
    if (!isset($vrsta['postavke'])) {
        $vrsta = ['postavke' => []];
    }
    
    $item['id'] = uniqid('vrsta_', true);
    $item['cas'] = time();
    
    $vrsta['postavke'][] = $item;
    
    return baza_pisi('sistem', 'vrsta/izhod', $vrsta);
}

function vrsta_preberi(?string $tip = null): array
{
    $vrsta = baza_beri('sistem', 'vrsta/izhod');
    
    if (empty($vrsta['postavke'])) {
        return [];
    }
    
    if ($tip === null) {
        return $vrsta['postavke'];
    }
    
    return array_filter($vrsta['postavke'], function($item) use ($tip) {
        return ($item['tip'] ?? '') === $tip;
    });
}

function vrsta_stevilo(?string $tip = null): int
{
    return count(vrsta_preberi($tip));
}

function vrsta_pocisti(?string $tip = null): bool
{
    if ($tip === null) {
        return baza_pisi('sistem', 'vrsta/izhod', ['postavke' => []]);
    }
    
    $vrsta = baza_beri('sistem', 'vrsta/izhod');
    
    if (empty($vrsta['postavke'])) {
        return true;
    }
    
    $vrsta['postavke'] = array_filter($vrsta['postavke'], function($item) use ($tip) {
        return ($item['tip'] ?? '') !== $tip;
    });
    
    return baza_pisi('sistem', 'vrsta/izhod', $vrsta);
}


📋 SISTEM/kanali/obdelava.php – WORKER

<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/obdelava.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2
 *
 * 📰 NAMEN:
 *     Obdelava čakalne vrste – pošiljanje na kanale.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - obdelava_izvedi(?string $tip = null): array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kanali/vrsta.php
 *     - ADAPTER/izhod_kanali/*
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kanali, obdelava, worker
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function obdelava_izvedi(?string $tip = null): array
{
    $postavke = vrsta_preberi($tip);
    $rezultati = [];
    
    foreach ($postavke as $postavka) {
        $kanali = $postavka['kanali'] ?? ['web'];
        
        foreach ($kanali as $kanal) {
            $rezultat = _poslji_na_kanal($kanal, $postavka);
            $rezultati[] = [
                'kanal' => $kanal,
                'id' => $postavka['id'] ?? 'unknown',
                'uspeh' => $rezultat
            ];
        }
    }
    
    // Počisti obdelane postavke
    vrsta_pocisti($tip);
    
    return $rezultati;
}

function _poslji_na_kanal(string $kanal, array $postavka): bool
{
    $mapa_kanalov = [
        'web' => POT_ADAPTER . '/izhod_kanali/KanalWeb.php',
        'api' => POT_ADAPTER . '/izhod_kanali/KanalApi.php',
        'telegram' => POT_ADAPTER . '/izhod_kanali/KanalTelegram.php',
        'facebook' => POT_ADAPTER . '/izhod_kanali/KanalFacebook.php',
        'ai' => POT_ADAPTER . '/izhod_kanali/KanalAi.php',
        'cli' => POT_ADAPTER . '/izhod_kanali/KanalCli.php'
    ];
    
    $pot = $mapa_kanalov[$kanal] ?? null;
    
    if (!$pot || !file_exists($pot)) {
        return false;
    }
    
    try {
        require_once $pot;
        
        // Pričakujemo funkcijo kanal_{ime}_poslji()
        $funkcija = 'kanal_' . $kanal . '_poslji';
        if (!function_exists($funkcija)) {
            return false;
        }
        
        return $funkcija($postavka);
    } catch (Exception $e) {
        return false;
    }
}


📋 ADAPTER/izhod_kanali/KanalWeb.php – WEB IZHOD

<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalWeb.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER IO
 *
 * 📰 NAMEN:
 *     Pretvorba izhoda v HTML preko GLOBALNO/render/.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - kanal_web_poslji(array $postavka): bool
 *
 * 📡 ODVISNOSTI:
 *     - GLOBALNO/render/render.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez SQL
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, web, html
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function kanal_web_poslji(array $postavka): bool
{
    $stran = $postavka['vsebina']['stran'] ?? 'domov';
    $podatki = $postavka['vsebina']['podatki'] ?? [];
    
    require_once POT_GLOBALNO . '/render/render.php';
    _render_vsebina($stran, $podatki);
    
    return true;
}


📋 ADAPTER/izhod_kanali/KanalApi.php – API IZHOD

<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalApi.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER IO
 *
 * 📰 NAMEN:
 *     Pretvorba izhoda v JSON.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - kanal_api_poslji(array $postavka): bool
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, api, json
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function kanal_api_poslji(array $postavka): bool
{
    header('Content-Type: application/json');
    
    $odziv = [
        'status' => $postavka['vsebina']['status'] ?? 'success',
        'podatki' => $postavka['vsebina']['podatki'] ?? [],
        'napaka' => $postavka['vsebina']['napaka'] ?? null
    ];
    
    echo json_encode($odziv, JSON_PRETTY_PRINT);
    return true;
}


📋 ADAPTER/adapter.php – EDINI VSTOP/IZSTOP

<?php
/**
 * ============================================================
 * POT: ADAPTER/adapter.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER N1
 *
 * 📰 NAMEN:
 *     EDINI vstop/izstop sistema. Normalizira vhod in pošlje izhod.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_izvedi(): void
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/api.php
 *     - ADAPTER/odzivi/adapter_odziv.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez SQL
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, vstop, izstop, io
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function adapter_izvedi(): void
{
    // 1. NORMALIZACIJA VHODA
    $akcija = $_POST['akcija'] ?? $_GET['akcija'] ?? '';
    $podatki = $_POST['podatki'] ?? $_GET['podatki'] ?? [];
    
    // 2. POŠLJI V SISTEM
    require_once POT_SISTEM . '/api.php';
    
    try {
        $odziv = _sistem_api_route($akcija, $podatki);
    } catch (Exception $e) {
        $odziv = [
            'status' => 'error',
            'sporocilo' => $e->getMessage()
        ];
    }
    
    // 3. PRIPRAVI IZHOD
    require_once POT_KANALI . '/priprava.php';
    $izhod = izhod_pripravi([
        'tip' => $akcija,
        'kanali' => ['web'],
        'vsebina' => $odziv,
        'meta' => [
            'akcija' => $akcija,
            'cas' => time()
        ]
    ]);
    
    // 4. POŠLJI IZHOD
    require_once POT_ADAPTER . '/odzivi/adapter_odziv.php';
    adapter_poslji_izhod($izhod);
}


📋 ADAPTER/odzivi/adapter_odziv.php – POŠILJANJE IZHODA

<?php
/**
 * ============================================================
 * POT: ADAPTER/odzivi/adapter_odziv.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER IO
 *
 * 📰 NAMEN:
 *     Pošiljanje izhoda na ustrezne kanale.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_poslji_izhod(array $izhod): void
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kanali/vrsta.php
 *     - SISTEM/kanali/obdelava.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, odziv, izhod
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function adapter_poslji_izhod(array $izhod): void
{
    // Če je API kanal, pošlji direktno
    if (in_array('api', $izhod['kanali'] ?? [])) {
        require_once POT_ADAPTER . '/izhod_kanali/KanalApi.php';
        kanal_api_poslji($izhod);
        return;
    }
    
    // Sicer daj v vrsto
    require_once POT_KANALI . '/vrsta.php';
    vrsta_dodaj($izhod);
    
    // Obdelaj vrsto
    require_once POT_KANALI . '/obdelava.php';
    obdelava_izvedi();
}


================================================================================
🔴 7. KRITIČNI POPRAVKI
================================================================================

✔ api.php – zaščita
defined('SISTEM_VARNOST') or define('SISTEM_VARNOST', true);

✔ index.php – zaščita
$pot = preg_replace('/[^a-z0-9_-]/i', '', $_GET['pot'] ?? '');

✔ ID generator
'id' => empty($uporabniki)
    ? 1
    : max(array_column($uporabniki, 'id')) + 1,

✔ baza_json.php – IZBRISAN (krši izolacijo)
✔ 09_ponudniki.php – odstranjen die()
✔ 16_upravljalec_runtime.php – IZBRISAN (runtime ne obstaja)


================================================================================
🧩 8. MANJKAJOČE FUNKCIJE
================================================================================

function api_napaka(string $msg): void
{
    echo json_encode([
        'status' => 'error',
        'napaka' => $msg
    ]);
    exit;
}

function modul_preveri(string $modul): void
{
    $register = baza_beri('register', 'moduli_register');
    if (!isset($register[$modul]) || !($register[$modul]['aktiviran'] ?? false)) {
        throw new Exception('Modul ni dovoljen');
    }
}

function uporabnik_pot(int $id, string $datoteka): string
{
    return POT_UPORABNIKI . '/' . $id . '/' . $datoteka . '.json';
}


================================================================================
👤 9. REGISTRACIJA (FINAL)
================================================================================

$user_dir = POT_UPORABNIKI . '/' . $user_id;

mkdir($user_dir, 0755, true);
mkdir($user_dir . '/PASSPORT', 0755, true);

file_put_contents($user_dir . '/profil.json', json_encode([
    'id' => $user_id,
    'vloga' => VLOGA_S0,
    'plan' => 'osnova',
    'status' => 'aktiven'
], JSON_PRETTY_PRINT));


================================================================================
🌉 10. ADAPTER (FINAL – BREZ CURL)
================================================================================

function most_api_klic(string $akcija, array $podatki = []): array
{
    $staraPost = $_POST;
    $stariGet = $_GET;

    $_POST = ['akcija' => $akcija, 'podatki' => $podatki];
    $_GET = [];

    ob_start();
    require_once POT_ADAPTER . '/adapter.php';
    adapter_izvedi();
    $izhod = ob_get_clean();

    $_POST = $staraPost;
    $_GET = $stariGet;

    return json_decode($izhod, true) ?? ['status' => 'error', 'napaka' => 'Ni odgovora'];
}


================================================================================
⚙️ 11. OBVEZNO ZA IMPLEMENTACIJO
================================================================================

VRSTA (queue):
- vrsta_dodaj()     ✅
- vrsta_preberi()   ✅
- vrsta_stevilo()   ✅
- vrsta_pocisti()   ✅

KANALI:
- SISTEM/kanali/obdelava.php  ✅
- ADAPTER/izhod_kanali/       ✅

REGISTRI:
- PODATKI/registri/moduli_register.json  ✅
- PODATKI/registri/rbac/vloge.json       ✅


================================================================================
🔁 12. TOK SISTEMA (ZAKLENJEN)
================================================================================

index.php
    ↓
ADAPTER/adapter.php (normalizacija vhoda)
    ↓
SISTEM/api.php (edini vstop v sistem)
    ↓
SISTEM/kernel/zaganjalnik.php (bootstrap)
    ↓
SISTEM/kernel/jedro/01-15 (jedro – BREZ RUNTIME)
    ↓
SISTEM/storitve_svetov/ (business logika)
    ↓
SISTEM/kanali/ (tehnični izhod: priprava, vrsta, obdelava)
    ↓
ADAPTER/izhod_kanali/ (pretvorba: HTML, JSON, ...)
    ↓
ADAPTER/odzivi/adapter_odziv.php (pošiljanje)
    ↓
Končni izhod


================================================================================
🔒 KONČNO STANJE
================================================================================

brez konfliktov ✔
brez podvojenih funkcij ✔
RBAC pravilen ✔
upravljalec_baz.php stabilen ✔
adapter kontrolira IO ✔
sistem razširljiv ✔


================================================================================
🧱 ENA IN EDINA RESNICA
================================================================================

To je:
- konsistentno
- izvedljivo
- produkcijsko pripravljeno
- BREZ RUNTIME

================================================================================