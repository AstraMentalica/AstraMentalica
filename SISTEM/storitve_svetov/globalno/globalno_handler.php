<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/globalno/globalno_handler.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Handler za GLOBALNO svet – prikaz strani.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - globalno_handler_izvedi(array $zahteva): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - GLOBALNO/render/render.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, globalno, handler
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function globalno_handler_izvedi(array $zahteva): array
{
    // Če je to API klic, ne prikazuj strani
    if (!empty($zahteva['parametri']['akcija']) || !empty($zahteva['vsebina']['akcija'])) {
        return $zahteva;
    }
    
    // Prikaz domov strani
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'tip' => 'html',
        'vsebina' => [
            'stran' => 'domov',
            'podatki' => [
                'uporabnik' => $zahteva['uporabnik'] ?? null,
                'parametri' => $zahteva['parametri'] ?? []
            ]
        ],
        'kanal' => $zahteva['kanal'] ?? 'splet'
    ];
}