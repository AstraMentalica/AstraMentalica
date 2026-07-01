<?php
/**
 * ============================================================
 * POT: GLOBALNO/pot_vmesnik.php
 * 📅 VERZIJA: v101 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: SIDRO (1) – otrok pot.php
 *
 * 📰 NAMEN:
 *     Vse POT_* konstante za medijske datoteke sistema.
 *     Slike, ikone, avatarji, varuhi, predmeti so v
 *     GLOBALNO/vmesnik/elementi/ – NE v VSEBINA/.
 *
 *     Konstante ki nimajo ustrezne mape v vmesnik/ so
 *     rezervirane (kažejo na prihodnje VSEBINA/ lokacije)
 *     da media_storitev.php ne pade z undefined constant.
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_GLOBALNO mora biti definiran)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__, require_once, logike, echo, die()
 *
 * 📌 STATUS: Stabilno
 * 📅 ZGODOVINA:
 *     - v101: prepisano – korenina je POT_GLOBALNO/vmesnik/
 *             ne POT_VSEBINA/ (slike so v GLOBALNO, ne VSEBINA)
 *     - v100: prva implementacija (napačna korenina)
 *
 * 👤 AVTOR: AstraMentalica Mojster
 * ============================================================
 *
 * STRUKTURA (dejanska):
 *   GLOBALNO/vmesnik/
 *   ├── elementi/
 *   │   ├── ui/            → predmeti, kristali, rune, karte...
 *   │   ├── varuhi/        → avatarji, duhovi, živali
 *   │   ├── portreti/
 *   │   └── ikone/
 *   └── stili/
 *
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');
defined('POT_GLOBALNO')   or die('pot_vmesnik.php zahteva pot.php (POT_GLOBALNO).');

// ============================================================
// KORENINE
// ============================================================
define('POT_VMESNIK',         POT_GLOBALNO . '/vmesnik');
define('POT_ELEMENTI',        POT_VMESNIK  . '/elementi');
define('POT_UI',              POT_ELEMENTI . '/ui');
define('POT_MEDIA',           POT_ELEMENTI);          // alias za media_storitev

// ============================================================
// LIKI – avatarji so v varuhi/avatarji/
// Kategorija (osnovni|srednji|doprsni|odsev|velik) gre kot
// $kategorija parameter v media_avatar_url()
// ============================================================
define('POT_AVATARJI',        POT_ELEMENTI . '/varuhi/avatarji');
define('POT_VARUHI',          POT_ELEMENTI . '/varuhi');
define('POT_PORTRETI',        POT_ELEMENTI . '/portreti');
define('POT_DUHOVI',          POT_ELEMENTI . '/varuhi/duhovi');

// ============================================================
// ŽIVALI – totemske živali (31 entitet iz zivali.json)
// Fizične slike v varuhi/zivali/
// ============================================================
define('POT_ZIVALI',          POT_ELEMENTI . '/varuhi/zivali');

// ============================================================
// EZOTERIJA – vse v ui/
// ============================================================
define('POT_RUNE',            POT_UI . '/rune');
define('POT_KARTE',           POT_UI . '/karte');
define('POT_CAKRE',           POT_UI . '/cakre');
define('POT_PORTALI',         POT_UI . '/portali');
define('POT_KOZMOS',          POT_UI . '/kozmos_spirale');
define('POT_SIMBOLI',         POT_UI . '/simboli');
define('POT_ZNAKI',           POT_UI . '/simboli');      // alias – ista mapa

// PREDMETI – razdeljeni po tipu znotraj ui/predmeti/
define('POT_PREDMETI',        POT_UI . '/predmeti');
define('POT_AMULETI',         POT_UI . '/predmeti');     // amulet_moder.png, amulet_oko.png
define('POT_KOMPAS',          POT_UI . '/predmeti');     // kompas_.png
define('POT_ELEMENTI_UI',     POT_UI . '/predmeti');     // element_1-4.png

// NAGRADE
define('POT_KRISTALI',        POT_UI . '/kristali');
define('POT_KLJUCI',          POT_UI . '/kljuci');
define('POT_RELIKTI',         POT_UI . '/relikvije');

// ============================================================
// BRALNIK – zvitki, peresa, knjige
// ============================================================
define('POT_BRALNIK',         POT_UI);
define('POT_BRALNIK_MEDIA',   POT_UI);
// Posamezne podmape:
define('POT_ZVITKI',          POT_UI . '/zvitki');
define('POT_PERESA',          POT_UI . '/peresa');
define('POT_KNJIGE',          POT_UI . '/knjige');

// ============================================================
// POSEBNI PREDMETI
// ============================================================
define('POT_PREROSKA_KROGLA', POT_UI . '/preroska_krogla');
define('POT_ARTEFAKTI',       POT_UI . '/artefakt');

// ============================================================
// IKONE
// ============================================================
define('POT_IKONE',           POT_ELEMENTI . '/ikone');
define('POT_IKONE_SOC',       POT_ELEMENTI . '/ikone/socialno');
define('POT_IKONE_SIS',       POT_ELEMENTI . '/ikone/avatar_ikona');

// ============================================================
// OZADJA – teme CSS so v stili/teme/
// ============================================================
define('POT_STILI',           POT_VMESNIK  . '/stili');
define('POT_TEME',            POT_STILI    . '/teme/teme');
define('POT_OZADJE_TEME',     POT_STILI    . '/teme/teme');

// ============================================================
// JEZIKI
// ============================================================
define('POT_JEZIKI',          POT_VMESNIK  . '/jeziki');

// ============================================================
// REZERVIRANE – mape še ne obstajajo v vmesnik/
// Kažejo na prihodnje VSEBINA/ lokacije.
// media_storitev.php jih potrebuje – ne smejo biti undefined.
// ============================================================
define('POT_VSEBINA_MEDIA',   POT_VSEBINA . '/media');   // prihodnje

define('POT_SIGILI',          POT_VSEBINA_MEDIA . '/sigili');
define('POT_GEOMETRIJA',      POT_VSEBINA_MEDIA . '/geometrija');
define('POT_PECATI',          POT_VSEBINA_MEDIA . '/pecati');
define('POT_ZIGI',            POT_VSEBINA_MEDIA . '/zigi');
define('POT_MAGIJA',          POT_VSEBINA_MEDIA . '/magija');
define('POT_BONUS',           POT_VSEBINA_MEDIA . '/bonus');
define('POT_DODATKI',         POT_VSEBINA_MEDIA . '/dodatki');
define('POT_LOGOTIPI',        POT_VSEBINA_MEDIA . '/logotipi');
define('POT_ZEMLJEVID',       POT_VSEBINA_MEDIA . '/zemljevid');
define('POT_OZADJE',          POT_VSEBINA_MEDIA . '/ozadje');
define('POT_OZADJE_GLAVA',    POT_VSEBINA_MEDIA . '/ozadje/glava');
define('POT_OZADJE_STRANI',   POT_VSEBINA_MEDIA . '/ozadje/strani');

define('POT_UI_GUMBI',        POT_VSEBINA_MEDIA . '/ui/gumbi');
define('POT_UI_OKVIRJI',      POT_VSEBINA_MEDIA . '/ui/okvirji');
define('POT_UI_BADGE',        POT_UI . '/artefakt');     // začasno → artefakt
define('POT_UI_KARTICE',      POT_VSEBINA_MEDIA . '/ui/kartice');
define('POT_UI_TOOLTIPI',     POT_VSEBINA_MEDIA . '/ui/tooltipi');
define('POT_UI_INVENTAR',     POT_UI . '/preroska_krogla'); // začasno
define('POT_UI_DOSEZKI',      POT_VSEBINA_MEDIA . '/ui/dosezki');
define('POT_UI_SIMBOLI',      POT_UI . '/simboli');

// Profilne slike uporabnikov – v PODATKI (izven weba)
define('POT_UPORABNIKI_MEDIA', PODATKI_UPORABNIKI . '/profilne');
