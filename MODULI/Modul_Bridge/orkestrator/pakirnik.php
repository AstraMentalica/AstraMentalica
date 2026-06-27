<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/orkestrator/pakirnik.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / ORKESTRATOR
 *
 * 📰 NAMEN:
 *     Pakira modul v ZIP arhiv za distribucijo ali prodajo.
 *     NOVA STRUKTURA: išče module direktno v MODULI/*/podatki/manifest.json
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - pakirnik_ustvari_zip(string $ime_modula): array
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
 *     bridge, orkestrator, pakiranje, zip
 * ============================================================
 */

declare(strict_types=1);

function pakirnik_ustvari_zip(string $ime_modula): array {
    if ($ime_modula === '') {
        return ['uspeh' => false, 'napaka' => 'Ime modula ni podano.'];
    }

    if (!class_exists('ZipArchive')) {
        return ['uspeh' => false, 'napaka' => 'PHP razširitev ZipArchive ni na voljo.'];
    }

    // Poišči modul direktno v MODULI/
    $pot_modula = MINI_MODULI . '/' . $ime_modula . '/';

    if (!is_dir($pot_modula) || !file_exists($pot_modula . 'podatki/manifest.json')) {
        return ['uspeh' => false, 'napaka' => "Modul '$ime_modula' ni bil najden ali nima podatki/manifest.json."];
    }

    // Ciljna ZIP datoteka
    $zip_ime = $ime_modula . '_' . date('Ymd_His') . '.zip';
    $zip_pot = MINI_BRIDGE . '/stebelne/' . $zip_ime;

    $zip = new ZipArchive();
    if ($zip->open($zip_pot, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti ZIP datoteke.'];
    }

    // Preskoči te mape
    $preskoci = ['runtime', '.git', 'tmp', 'node_modules'];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($pot_modula, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $datoteka) {
        $relativna_pot = substr($datoteka->getPathname(), strlen($pot_modula));

        // Preskoci prepovedane mape
        $preskoci_to = false;
        foreach ($preskoci as $mapa) {
            if (str_starts_with(ltrim($relativna_pot, '/\\'), $mapa)) {
                $preskoci_to = true;
                break;
            }
        }
        if ($preskoci_to) {
            continue;
        }

        if ($datoteka->isDir()) {
            $zip->addEmptyDir($ime_modula . '/' . ltrim($relativna_pot, '/\\'));
        } else {
            $zip->addFile(
                $datoteka->getPathname(),
                $ime_modula . '/' . ltrim($relativna_pot, '/\\')
            );
        }
    }

    $zip->close();

    $velikost = file_exists($zip_pot)
        ? _pakirnik_formatiran_velikost(filesize($zip_pot))
        : '?';

    return [
        'uspeh'    => true,
        'pot'      => $zip_pot,
        'ime'      => $zip_ime,
        'velikost' => $velikost,
    ];
}

// ── ZASEBNI POMOČNIKI ────────────────────────────────────────

function _pakirnik_formatiran_velikost(int $bajti): string {
    if ($bajti >= 1048576) {
        return round($bajti / 1048576, 2) . ' MB';
    }
    if ($bajti >= 1024) {
        return round($bajti / 1024, 1) . ' KB';
    }
    return $bajti . ' B';
}