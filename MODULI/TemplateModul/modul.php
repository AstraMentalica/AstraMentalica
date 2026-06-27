<?php
/**
 * ============================================================
 * POT: MODULI/TemplateModul/modul.php
 * 📅 VERZIJA: v116 (18.6.2026 21:10)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Predloga za nov modul – kopiraj in preimenuj.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_template_akcija(string $akcija, array $podatki): array
 *
 * 📡 ODVISNOSTI:
 *     - (nobene – modul je izoliran)
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez direktnih klicev SISTEM/
 *     - Brez direktnih klicev PODATKI/
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: uskladitev s Header Standard v116,
 *             odstranjeni vsi die() in exit()
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, template, predloga
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return
if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

// ============================================================
// VSTOPNA TOČKA MODULA
// ============================================================

function modul_template_akcija(string $akcija, array $podatki = []): array
{
    return match($akcija) {
        'info'    => _template_info($podatki),
        'pozdrav' => _template_pozdrav($podatki),
        default   => ['napaka' => 'Neznana akcija: ' . $akcija]
    };
}

// ============================================================
// AKCIJE
// ============================================================

function _template_info(array $podatki): array
{
    return [
        'ime'     => 'Template Modul',
        'verzija' => '1.0.0',
        'opis'    => 'Predloga za nov modul',
        'akcije'  => ['info', 'pozdrav']
    ];
}

function _template_pozdrav(array $podatki): array
{
    $ime = $podatki['ime'] ?? 'popotnik';
    return [
        'sporocilo' => 'Pozdravljen, ' . htmlspecialchars($ime) . '!',
        'cas'       => date('Y-m-d H:i:s')
    ];
}
