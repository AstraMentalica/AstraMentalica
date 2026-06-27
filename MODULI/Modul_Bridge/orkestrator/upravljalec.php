<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/orkestrator/upravljalec.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / ORKESTRATOR
 *
 * 📰 NAMEN:
 *     Upravljanje modulov — seznam, preverjanje, info.
 *     NOVA STRUKTURA: direktno iz MODULI/*/podatki/manifest.json
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - upravljalec_seznam_modulov(): array
 *     - upravljalec_modul_info(string $pot): array
 *     - upravljalec_modul_obstaja(string $ime): bool
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
 *     bridge, orkestrator, upravljalec
 * ============================================================
 */

declare(strict_types=1);

function upravljalec_seznam_modulov(): array {
    $moduli = [];
    $pot_moduli = MINI_MODULI;

    if (!is_dir($pot_moduli)) {
        return [];
    }

    foreach (glob($pot_moduli . '/*', GLOB_ONLYDIR) as $mod_pot) {
        $ime = basename($mod_pot);
        if ($ime === 'Modul_Bridge' || str_starts_with($ime, '.')) {
            continue;
        }

        $info = upravljalec_modul_info($mod_pot);
        if (!empty($info)) {
            $moduli[] = $info;
        }
    }

    return $moduli;
}

function upravljalec_modul_info(string $pot): array {
    // NOVA STRUKTURA: podatki/manifest.json
    $manifest_pot = rtrim($pot, '/') . '/podatki/manifest.json';
    if (!file_exists($manifest_pot)) {
        return [];
    }

    $manifest = json_decode(file_get_contents($manifest_pot), true);
    if (!is_array($manifest) || !isset($manifest['_id'])) {
        return [];
    }

    $ime     = $manifest['modul']['ime'] ?? $manifest['_id'] ?? basename($pot);
    $id      = $manifest['_id'] ?? $manifest['modul']['id'] ?? '';
    $verzija = $manifest['modul']['verzija'] ?? $manifest['_verzija'] ?? '—';
    $tip     = $manifest['modul']['tip'] ?? '—';
    $vloga   = $manifest['dostop']['minimalna_vloga'] ?? 'S0';

    return [
        'ime'        => $ime,
        'id'         => $id,
        'verzija'    => $verzija,
        'tip'        => $tip,
        'vloga'      => $vloga,
        'pot'        => $pot,
        'manifest'   => $manifest,
        'ima_vstop'  => file_exists($pot . '/modul.php'),
    ];
}

function upravljalec_modul_obstaja(string $ime): bool {
    return is_dir(MINI_MODULI . '/' . $ime);
}