<?php
/**
 * ============================================================
 * POT: VSEBINA/pot_vsebina.php
 * 📅 VERZIJA: v118 (19.6.2026 00:00)
 * ============================================================
 *
 * 🏛️ NIVO: VSEBINA (pod-sidro)
 *
 * 📰 NAMEN:
 *     Definira vse VSEBINA_* konstante za podmape mape VSEBINA/.
 *     Analogno AI/pot_ai.php — lokalno sidro za svojo vejo.
 *     Ne kliče __DIR__. Odvisna od pot.php (POT_VSEBINA).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (nobene – samo konstante)
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_VSEBINA mora biti definiran)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez logike
 *     - Brez require_once
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * ✅ KONVENCIJA POIMENOVANJA:
 *     POT_VSEBINA   → glavno sidro (definiran v pot.php)
 *     VSEBINA_*     → vse podmape te veje (definirane tukaj)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v118: preimenovanje media → gradniki (VSEBINA_MEDIA → VSEBINA_GRADNIKI)
 *     - v117: nova datoteka; ločitev VSEBINA_* konstant iz pot.php
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     sidro, vsebina, poti, konstante, gradniki
 * ============================================================
 */

declare(strict_types=1);

// Varnostni preverek — pot.php mora biti naložen pred nami
if (!defined('ELEMENTI_VSEBINA')) {
    throw new RuntimeException('pot_elementi.php zahteva pot.php (POT_ELEMENTI ni definiran).');
}

// ============================================================
// GRADNIKI — koren
// ============================================================
define('VSEBINA_GRADNIKI',         POT_VSEBINA . '/gradniki');

// ============================================================
// AVATARJI
// ============================================================
define('VSEBINA_AVATARJI',         VSEBINA_GRADNIKI . '/avatarji');
define('VSEBINA_AVT_OTROSKI',      VSEBINA_AVATARJI . '/otroski');
define('VSEBINA_AVT_OSNOVNI',      VSEBINA_AVATARJI . '/osnovni');
define('VSEBINA_AVT_MALI',         VSEBINA_AVATARJI . '/mali');
define('VSEBINA_AVT_SREDNJI',      VSEBINA_AVATARJI . '/srednji');
define('VSEBINA_AVT_VELIK',        VSEBINA_AVATARJI . '/velik');
define('VSEBINA_AVT_DOPRSNI',      VSEBINA_AVATARJI . '/doprsni');
define('VSEBINA_AVT_ANGELSKI',     VSEBINA_AVATARJI . '/angelski');
define('VSEBINA_AVT_VIP',          VSEBINA_AVATARJI . '/vip');
define('VSEBINA_AVT_PREMIUM',      VSEBINA_AVATARJI . '/premium');
define('VSEBINA_AVT_RAZVIT',       VSEBINA_AVATARJI . '/razvit');

// ============================================================
// VARUHI
// ============================================================
define('VSEBINA_VARUHI',           VSEBINA_GRADNIKI . '/varuhi');
define('VSEBINA_VAR_ZIVA',         VSEBINA_VARUHI . '/ziva');
define('VSEBINA_VAR_OTROSKI',      VSEBINA_VARUHI . '/otroski');
define('VSEBINA_VAR_ANGELSKI',     VSEBINA_VARUHI . '/angelski');
define('VSEBINA_VAR_ELEMENTARNI',  VSEBINA_VARUHI . '/elementarni');
define('VSEBINA_VAR_MODULSKI',     VSEBINA_VARUHI . '/modulski');

// ============================================================
// EZOTERIJA
// ============================================================
define('VSEBINA_EZOTERIJA',        VSEBINA_GRADNIKI . '/ezoterija');
define('VSEBINA_RUNE',             VSEBINA_EZOTERIJA . '/rune');
define('VSEBINA_SIGILI',           VSEBINA_EZOTERIJA . '/sigili');
define('VSEBINA_ZNAKI',            VSEBINA_EZOTERIJA . '/znaki');
define('VSEBINA_KARTE',            VSEBINA_EZOTERIJA . '/karte');
define('VSEBINA_CAKRE',            VSEBINA_EZOTERIJA . '/cakre');
define('VSEBINA_GEOMETRIJA',       VSEBINA_EZOTERIJA . '/geometrija');
define('VSEBINA_KOMPAS',           VSEBINA_EZOTERIJA . '/kompas');
define('VSEBINA_PECATI',           VSEBINA_EZOTERIJA . '/pecati');
define('VSEBINA_ZIGI',             VSEBINA_EZOTERIJA . '/zigi');
define('VSEBINA_RELIKTI',          VSEBINA_EZOTERIJA . '/relikti');
define('VSEBINA_PORTALI',          VSEBINA_EZOTERIJA . '/portali');
define('VSEBINA_KOZMOS',           VSEBINA_EZOTERIJA . '/kozmos');
define('VSEBINA_DUHOVI',           VSEBINA_EZOTERIJA . '/duhovi');
define('VSEBINA_MAGIJA',           VSEBINA_EZOTERIJA . '/magija');

// ============================================================
// NARAVA
// ============================================================
define('VSEBINA_NARAVA',           VSEBINA_GRADNIKI . '/narava');
define('VSEBINA_ZIVALI',           VSEBINA_NARAVA . '/zivali');
define('VSEBINA_ELEMENTI',         VSEBINA_NARAVA . '/elementi');

// ============================================================
// NAGRADE
// ============================================================
define('VSEBINA_NAGRADE',          VSEBINA_GRADNIKI . '/nagrade');
define('VSEBINA_AMULETI',          VSEBINA_NAGRADE . '/amuleti');
define('VSEBINA_AMU_OTROSKI',      VSEBINA_AMULETI . '/otroski');
define('VSEBINA_AMU_REDKI',        VSEBINA_AMULETI . '/redki');
define('VSEBINA_AMU_POSEBNI',      VSEBINA_AMULETI . '/posebni');
define('VSEBINA_KRISTALI',         VSEBINA_NAGRADE . '/kristali');
define('VSEBINA_KRI_OSNOVNI',      VSEBINA_KRISTALI . '/osnovni');
define('VSEBINA_KRI_REDKI',        VSEBINA_KRISTALI . '/redki');
define('VSEBINA_KRI_POSEBNI',      VSEBINA_KRISTALI . '/posebni');
define('VSEBINA_KLJUCI',           VSEBINA_NAGRADE . '/kljuci');
define('VSEBINA_BONUS',            VSEBINA_NAGRADE . '/bonus');
define('VSEBINA_DODATKI',          VSEBINA_NAGRADE . '/dodatki');

// ============================================================
// BRALNIK
// ============================================================
define('VSEBINA_BRALNIK',          VSEBINA_GRADNIKI . '/bralnik');
define('VSEBINA_ZVITKI',           VSEBINA_BRALNIK . '/zvitki');
define('VSEBINA_PERESA',           VSEBINA_BRALNIK . '/peresa');
define('VSEBINA_KNJIGE',           VSEBINA_BRALNIK . '/knjige');

// ============================================================
// SVET & UI
// ============================================================
define('VSEBINA_ZEMLJEVID',        VSEBINA_GRADNIKI . '/zemljevid');
define('VSEBINA_PORTRETI',         VSEBINA_GRADNIKI . '/portreti');
define('VSEBINA_LOGOTIPI',         VSEBINA_GRADNIKI . '/logotipi');

define('VSEBINA_UI',               VSEBINA_GRADNIKI . '/ui');
define('VSEBINA_UI_GUMBI',         VSEBINA_UI . '/gumbi');
define('VSEBINA_UI_OKVIRJI',       VSEBINA_UI . '/okvirji');
define('VSEBINA_UI_BADGE',         VSEBINA_UI . '/badge');
define('VSEBINA_UI_KARTICE',       VSEBINA_UI . '/kartice');
define('VSEBINA_UI_TOOLTIPI',      VSEBINA_UI . '/tooltipi');
define('VSEBINA_UI_INVENTAR',      VSEBINA_UI . '/inventar');
define('VSEBINA_UI_DOSEZKI',       VSEBINA_UI . '/dosezki');
define('VSEBINA_UI_SIMBOLI',       VSEBINA_UI . '/simboli');

define('VSEBINA_IKONE',            VSEBINA_GRADNIKI . '/ikone');
define('VSEBINA_IKONE_SOC',        VSEBINA_IKONE . '/socialno');
define('VSEBINA_IKONE_SIS',        VSEBINA_IKONE . '/sistem');
define('VSEBINA_IKONE_UI',         VSEBINA_IKONE . '/ui');

define('VSEBINA_OZADJE',           VSEBINA_GRADNIKI . '/ozadje');
define('VSEBINA_OZADJE_GLAVA',     VSEBINA_OZADJE . '/glava');
define('VSEBINA_OZADJE_STRANI',    VSEBINA_OZADJE . '/strani');
define('VSEBINA_OZADJE_TEME',      VSEBINA_OZADJE . '/teme');

// ============================================================
// UPORABNIKI (gradniki profili)
// ============================================================
define('VSEBINA_UPORABNIKI',       VSEBINA_GRADNIKI . '/uporabniki');
