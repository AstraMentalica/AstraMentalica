<?php
/**
 * ============================================================
 * POT: index.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (Vstopna točka)
 *
 * 📰 NAMEN:
 *     Edina javna vstopna točka sistema.
 *     Je popolnoma "nema" – ne preverja sej, ne renderira,
 *     ne vsebuje poslovne logike. Samo preda nadzor adapterju.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (nobene – izvedbeni skript)
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (EDINO SIDRO)
 *     - ADAPTER/adapter.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez session_start() ali session_name()
 *     - Brez $_SESSION preverjanj
 *     - Brez renderiranja HTML
 *     - Brez poslovne logike
 *     - Brez define() konstant
 *     - Brez __DIR__
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: POPRAVEK – odstranjeno ročno upravljanje sej,
 *             preverjanje uporabnikov in direktno renderiranje
 *             domov strani. index.php je zdaj arhitekturno čist.
 *             (Gemini review – kršitev Pravila 1.1 in 1.4 Ustave)
 *     - v113: dodana seja in renderiranje (zdaj odstranjeno)
 *     - v112: prva implementacija
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     vstopna-tocka, javno, globalno
 * ============================================================
 */

declare(strict_types=1);

// EDINO SIDRO – edina uporaba __DIR__ je v pot.php
require_once __DIR__ . '/pot.php';

// Vse zahteve (WEB, API, AJAX, Forme) gredo skozi ADAPTER
require_once POT_ADAPTER . '/adapter.php';

adapter_zagon();
