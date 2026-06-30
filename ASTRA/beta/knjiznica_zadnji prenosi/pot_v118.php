<?php
/**
 * ============================================================
 * POT: ROOT/pot.php
 * 📅 VERZIJA: v118 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: SIDRO (0)
 *
 * 📰 NAMEN:
 *     Absolutno sidro sistema – definira vse POT_* konstante.
 *     Edina datoteka v celotnem sistemu, ki sme klicati __DIR__.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (nobene – samo konstante)
 *
 * 📡 ODVISNOSTI:
 *     - (nič)
 *
 * 🚫 PREPOVEDI:
 *     - Brez logike
 *     - Brez require_once
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * ✅ DOVOLJENO:
 *     - define() konstante
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v118: POT_SEF resolucija razširjena – podpora za SERVER
 *             spremenljivko ASTRA_SEF_PATH (nastaviti v .htaccess
 *             ali php.ini, kaže izven public_html);
 *             dokumentiran varnostni model sef mape;
 *             dodan POT_SEF_PRIVZET za fallback debug opozorilo
 *     - v117: združitev pot.php in pot (2).php; dodan POT_AI;
 *             odstranjen ROOT alias; dodan declare(strict_types=1)
 *     - v116: uskladitev s Header Standard v116
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     sidro, poti, konstante
 *
 * ============================================================
 * VARNOSTNI MODEL – POT_SEF
 * ============================================================
 *
 * .env_api, .env_baza, .env_sistem MORAJO biti IZVEN public_html.
 *
 * Priporočena struktura strežnika:
 *
 *   /home/user/                        ← serverski home (izven weba)
 *   ├── sef/                           ← mapa s skrivnostmi
 *   │   ├── .env_api
 *   │   ├── .env_baza
 *   │   └── .env_sistem
 *   └── public_html/                   ← Apache document root
 *       ├── pot.php
 *       └── index.php
 *
 * Nastavitev poti (ena od možnosti):
 *
 *   A) .htaccess (Apache):
 *      SetEnv ASTRA_SEF_PATH /home/user/sef
 *
 *   B) php.ini / .user.ini:
 *      ASTRA_SEF_PATH=/home/user/sef
 *
 *   C) SERVER spremenljivka (FastCGI):
 *      fastcgi_param ASTRA_SEF_PATH /home/user/sef
 *
 * Če nobena od zgornjih ni nastavljena, sistem pade na
 * PODATKI/sef/ (privzeto) – funkcionalno, a NI varno za produkcijo.
 * ============================================================
 */

declare(strict_types=1);

// ============================================================
// 1. KORENSKA POT  — edini __DIR__ v celotnem sistemu
// ============================================================
define('POT_KOREN', __DIR__);

// ============================================================
// 2. DIREKTNI OTROCI KORENA
// ============================================================
define('POT_SISTEM',      POT_KOREN . '/SISTEM');
define('POT_GLOBALNO',    POT_KOREN . '/GLOBALNO');
define('POT_MODULI',      POT_KOREN . '/MODULI');
define('POT_UPORABNIKI',  POT_KOREN . '/UPORABNIKI');
define('POT_PODATKI',     POT_KOREN . '/PODATKI');
define('POT_VSEBINA',     POT_KOREN . '/VSEBINA');
define('POT_ASTRA',       POT_KOREN . '/ASTRA');
define('POT_ADAPTER',     POT_KOREN . '/ADAPTER');
define('POT_AI',          POT_KOREN . '/AI');

// ============================================================
// 3. POT DO SEF MAPE (skrivnosti, izven public_html)
//
//    Prioriteta iskanja:
//    1. Okoljska spremenljivka ASTRA_SEF_PATH  (Apache SetEnv /
//       php.ini / FastCGI – nastavi na pot IZVEN public_html)
//    2. $_SERVER['ASTRA_SEF_PATH']             (nekatere konfiguracije)
//    3. Privzeto: PODATKI/sef/                 (samo za razvoj/dev!)
// ============================================================
$_potSef = (string)(
    getenv('ASTRA_SEF_PATH')
    ?: ($_SERVER['ASTRA_SEF_PATH'] ?? '')
    ?: ''
);

// Fallback na PODATKI/sef – deluje, a ni primerno za produkcijo
define('POT_SEF_PRIVZET',  POT_PODATKI . '/sef');
define('POT_SEF_IZVEN',    $_potSef !== '');       // true = pot je nastavljena izven weba

if ($_potSef !== '') {
    define('POT_SEF', rtrim($_potSef, '/\\'));
} else {
    define('POT_SEF', POT_SEF_PRIVZET);
}

unset($_potSef);

// ============================================================
// 4. SISTEM PODMAPE
// ============================================================
define('POT_KERNEL',      POT_SISTEM . '/kernel');
define('POT_JEDRO',       POT_KERNEL . '/jedro');
define('POT_BAZE',        POT_KERNEL . '/baze');
define('POT_STORITVE',    POT_SISTEM . '/storitve_svetov');
define('POT_KANALI',      POT_SISTEM . '/kanali');

// ============================================================
// 5. PODATKI PODMAPE
// ============================================================
define('PODATKI_ENV',        POT_PODATKI . '/sef');
define('PODATKI_MODULI',     POT_PODATKI . '/moduli');
define('PODATKI_UPORABNIKI', POT_PODATKI . '/uporabniki');
define('PODATKI_INVENTURA',  POT_PODATKI . '/globalno/inventura');
define('PODATKI_ANALITIKA',  POT_PODATKI . '/sistem/analitika');
define('PODATKI_STATISTIKA', POT_PODATKI . '/sistem/analitika/statistika');
define('PODATKI_MERITVE',    POT_PODATKI . '/sistem/analitika/meritve');
define('PODATKI_REGISTRI',   POT_PODATKI . '/sistem/registri');
define('PODATKI_SISTEM',     POT_PODATKI . '/sistem');
define('PODATKI_LOG',        POT_PODATKI . '/sistem/dnevnik');
define('POT_LOG',            PODATKI_LOG);
define('PODATKI_CACHE',      POT_PODATKI . '/sistem/predpomnilnik');
define('PODATKI_TMP',        POT_PODATKI . '/sistem/tmp');
define('PODATKI_VRSTA',      POT_PODATKI . '/sistem/vrsta');
define('PODATKI_BAZE',       POT_PODATKI . '/baze');
define('PODATKI_JSON',       POT_PODATKI . '/baze/json');
define('PODATKI_SQLITE',     POT_PODATKI . '/baze/sqlite');
define('PODATKI_MYSQL',      POT_PODATKI . '/baze/mysql');
define('PODATKI_ARHIV',      POT_PODATKI . '/arhiv');

// ============================================================
// 6. URL KONSTANTE
// ============================================================
$_prot = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_mapa = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

define('KOREN_URL',    $_prot . '://' . $_host . $_mapa);
define('GLOBALNO_URL', KOREN_URL . '/GLOBALNO');
define('MODULI_URL',   KOREN_URL . '/MODULI');
define('ASTRA_URL',    KOREN_URL . '/ASTRA');

unset($_prot, $_host, $_mapa);

// ============================================================
// 7. RBAC VLOGE
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
// 8. SISTEMSKE NASTAVITVE
// ============================================================
define('IME_APLIKACIJE', 'AstraMentalica');
define('CASOVNA_CONA',   'Europe/Ljubljana');
define('RAZVOJNI_NACIN', true);
define('SISTEM_VERZIJA', 'v118');

// ============================================================
// 9. VAROVALKA
// ============================================================
define('SISTEM_VARNOST', true);
