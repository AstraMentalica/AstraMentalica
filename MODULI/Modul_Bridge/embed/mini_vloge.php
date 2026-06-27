<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/embed/mini_vloge.php
 * 📅 VERZIJA: v116 (18.6.2026 21:05)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL (Modul_Bridge/embed)
 *
 * 📰 NAMEN:
 *     Mini RBAC sistem za samostojno delovanje Bridge-a.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - mini_preveri_vlogo(int $zahtevana): bool
 *     - mini_dodeli_vlogo(int $vloga): void
 *     - mini_pridobi_uporabnika(): array
 *     - mini_prijavi_gosta(): void
 *     - mini_prijavi_admina(): void
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
 *     modul, bridge, embed, vloge, rbac
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return (enak vzorec kot index.php)
if (!defined('BRIDGE_VARNOST')) {
    http_response_code(403);
    return;
}

function mini_preveri_vlogo(int $zahtevana): bool
{
    $uporabnik = mini_pridobi_uporabnika();
    return (int)($uporabnik['vloga'] ?? 0) >= $zahtevana;
}

function mini_dodeli_vlogo(int $vloga): void
{
    if (!isset($_SESSION['mini_uporabnik'])) {
        mini_prijavi_gosta();
    }
    $_SESSION['mini_uporabnik']['vloga'] = $vloga;
}

function mini_pridobi_uporabnika(): array
{
    return $_SESSION['mini_uporabnik'] ?? [
        'id' => 0,
        'ime' => 'Gost',
        'vloga' => MINI_VLOGA_GOST,
    ];
}

function mini_prijavi_gosta(): void
{
    $_SESSION['mini_uporabnik'] = [
        'id' => 0,
        'ime' => 'Gost',
        'vloga' => MINI_VLOGA_GOST,
    ];
}

function mini_prijavi_admina(): void
{
    // V razvojnem načinu Bridge deluje kot admin
    $_SESSION['mini_uporabnik'] = [
        'id' => 1,
        'ime' => 'Razvijalec',
        'vloga' => MINI_VLOGA_ADMIN,
    ];
}
