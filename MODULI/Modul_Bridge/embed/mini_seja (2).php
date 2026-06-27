<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/embed/mini_seja.php
 * 📅 VERZIJA: v116 (18.6.2026 21:05)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL (Modul_Bridge/embed)
 *
 * 📰 NAMEN:
 *     Mini session management za samostojno delovanje Bridge-a.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - mini_seja_zacni(): void
 *     - mini_seja_unici(): void
 *     - mini_je_prijavljen(): bool
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
 *     modul, bridge, embed, seja
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return (enak vzorec kot index.php)
if (!defined('BRIDGE_VARNOST')) {
    http_response_code(403);
    return;
}

function mini_seja_zacni(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('BRIDGE_SEJA');
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
        ]);
    }
}

function mini_seja_unici(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        session_destroy();
    }
}

function mini_je_prijavljen(): bool
{
    return isset($_SESSION['mini_uporabnik'])
        && (int)($_SESSION['mini_uporabnik']['id'] ?? 0) > 0;
}
