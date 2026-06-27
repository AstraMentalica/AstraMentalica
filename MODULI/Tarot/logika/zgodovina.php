<?php
/**
 * ============================================================
 * POT: MODULI/ORAKLEUM/Tarot/logika/zgodovina.php
 * 📅 VERZIJA: v1.0.0 (19.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL / LOGIKA
 *
 * 📰 NAMEN:
 *     Upravljanje zgodovine vedeževanj in priljubljenih.
 *     Piše SAMO v lastno mapo modula: PODATKI/moduli/modul_tarot/
 *     (po manifestu, izhod.pise_v).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tarot_zgodovina_zapisi(array $vedezevanje, string $uporabnik_id): bool
 *     - tarot_zgodovina_beri(string $uporabnik_id): array
 *     - tarot_priljubljene_dodaj(string $uid_vedezevanja, string $uporabnik_id): bool
 *     - tarot_priljubljene_beri(string $uporabnik_id): array
 *     - tarot_priljubljene_brisi(string $uid_vedezevanja, string $uporabnik_id): bool
 *
 * 📡 ODVISNOSTI:
 *     - most_podatki_beri() / most_podatki_pisi() iz Modul_Bridge
 *       (ali sistemski baza_beri/baza_pisi, če sistem obstaja)
 *
 * 🚫 PREPOVEDI:
 *     - Brez pisanja izven svoje mape modula
 *     - Brez branja $_SESSION direktno
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
 *     modul, tarot, logika, zgodovina, priljubljene
 * ============================================================
 */

declare(strict_types=1);

const TAROT_PODATKI_MAPA = 'modul_tarot';

function tarot_zgodovina_zapisi(array $vedezevanje, string $uporabnik_id): bool {
    $datoteka = "zgodovina_{$uporabnik_id}.json";
    $obstojeci = tarot_zgodovina_beri($uporabnik_id);
    $obstojeci[] = $vedezevanje;

    return _tarot_podatki_pisi($datoteka, $obstojeci);
}

function tarot_zgodovina_beri(string $uporabnik_id): array {
    $datoteka = "zgodovina_{$uporabnik_id}.json";
    return _tarot_podatki_beri($datoteka) ?? [];
}

function tarot_priljubljene_dodaj(string $uid_vedezevanja, string $uporabnik_id): bool {
    $zgodovina = tarot_zgodovina_beri($uporabnik_id);

    $najdeno = null;
    foreach ($zgodovina as $vnos) {
        if (($vnos['uid'] ?? '') === $uid_vedezevanja) {
            $najdeno = $vnos;
            break;
        }
    }

    if ($najdeno === null) {
        return false;
    }

    $datoteka = "priljubljene_{$uporabnik_id}.json";
    $priljubljene = _tarot_podatki_beri($datoteka) ?? [];

    foreach ($priljubljene as $p) {
        if (($p['uid'] ?? '') === $uid_vedezevanja) {
            return true; // že obstaja
        }
    }

    $priljubljene[] = $najdeno;
    return _tarot_podatki_pisi($datoteka, $priljubljene);
}

function tarot_priljubljene_beri(string $uporabnik_id): array {
    $datoteka = "priljubljene_{$uporabnik_id}.json";
    return _tarot_podatki_beri($datoteka) ?? [];
}

function tarot_priljubljene_brisi(string $uid_vedezevanja, string $uporabnik_id): bool {
    $datoteka = "priljubljene_{$uporabnik_id}.json";
    $priljubljene = _tarot_podatki_beri($datoteka) ?? [];

    $nove = array_values(array_filter(
        $priljubljene,
        fn($p) => ($p['uid'] ?? '') !== $uid_vedezevanja
    ));

    if (count($nove) === count($priljubljene)) {
        return false; // ni bilo najdeno
    }

    return _tarot_podatki_pisi($datoteka, $nove);
}

// ── ZASEBNI POMOČNIKI (abstrakcija nad shrambo) ─────────────

/**
 * Bere podatke modula. Uporabi sistemski upravljalec_baz, če obstaja,
 * sicer pade nazaj na lokalni JSON v podatki/uporabnik/.
 */
function _tarot_podatki_beri(string $datoteka): ?array {
    if (function_exists('most_podatki_beri')) {
        return most_podatki_beri(TAROT_PODATKI_MAPA, $datoteka);
    }

    $pot = __DIR__ . '/../uporabnik/' . $datoteka;
    if (!file_exists($pot)) {
        return [];
    }

    $vsebina = file_get_contents($pot);
    $podatki = json_decode($vsebina, true);

    return is_array($podatki) ? $podatki : [];
}

function _tarot_podatki_pisi(string $datoteka, array $podatki): bool {
    if (function_exists('most_podatki_pisi')) {
        return most_podatki_pisi(TAROT_PODATKI_MAPA, $datoteka, $podatki);
    }

    $mapa = __DIR__ . '/../uporabnik/';
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }

    $pot = $mapa . $datoteka;
    $rezultat = file_put_contents($pot, json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return $rezultat !== false;
}
