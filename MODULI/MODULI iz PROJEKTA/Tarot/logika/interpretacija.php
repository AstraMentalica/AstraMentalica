<?php
/**
 * ============================================================
 * POT: MODULI/ORAKLEUM/Tarot/logika/interpretacija.php
 * 📅 VERZIJA: v1.0.0 (19.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL / LOGIKA
 *
 * 📰 NAMEN:
 *     Generira besedilno interpretacijo postavitve kart
 *     glede na tip širjenja (ena_karta, tri_karte, keltski_kriz).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tarot_interpretiraj(array $karte, string $sirjenje, string $vprasanje): array
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
 *     modul, tarot, logika, interpretacija
 * ============================================================
 */

declare(strict_types=1);

function tarot_interpretiraj(array $karte, string $sirjenje, string $vprasanje): array {
    return match ($sirjenje) {
        'ena_karta'    => _tarot_interp_ena($karte, $vprasanje),
        'tri_karte'    => _tarot_interp_tri($karte, $vprasanje),
        'keltski_kriz' => _tarot_interp_keltski_kriz($karte, $vprasanje),
        default        => _tarot_interp_splosno($karte, $vprasanje),
    };
}

/**
 * Vrne RX status karte. Podpira oba ključa: javni alias 'rx'
 * (uporablja se v API/UI) in notranje ime 'obrnjena'.
 */
function _tarot_rx(array $karta): bool {
    return (bool)($karta['rx'] ?? $karta['obrnjena'] ?? false);
}

function _tarot_interp_ena(array $karte, string $vprasanje): array {
    $karta = $karte[0];
    $rx    = _tarot_rx($karta);

    return [
        'naslov'   => 'Karta dneva',
        'besedilo' => "Na vprašanje \"$vprasanje\" se je razkrila karta {$karta['ime']}"
            . ($rx ? ' (RX)' : '') . ". {$karta['pomen']}.",
        'pozicije' => [
            ['oznaka' => 'Odgovor', 'karta' => $karta['ime'], 'rx' => $rx, 'pomen' => $karta['pomen']],
        ],
    ];
}

function _tarot_interp_tri(array $karte, string $vprasanje): array {
    $oznake = ['Preteklost', 'Sedanjost', 'Prihodnost'];
    $pozicije = [];

    foreach ($karte as $i => $karta) {
        $pozicije[] = [
            'oznaka' => $oznake[$i] ?? "Pozicija " . ($i + 1),
            'karta'  => $karta['ime'],
            'rx'     => _tarot_rx($karta),
            'pomen'  => $karta['pomen'],
        ];
    }

    $povzetek = implode(' → ', array_map(fn($k) => $k['ime'], $karte));

    return [
        'naslov'   => 'Postavitev: Preteklost – Sedanjost – Prihodnost',
        'besedilo' => "Glede na vprašanje \"$vprasanje\" se zaporedje kart razvija takole: $povzetek. "
            . "To kaže pot, po kateri se tema giblje skozi čas — od korenin, preko trenutne situacije, do njenega izida.",
        'pozicije' => $pozicije,
    ];
}

function _tarot_interp_keltski_kriz(array $karte, string $vprasanje): array {
    $oznake = [
        'Trenutno stanje', 'Izziv', 'Podzavestni vpliv', 'Nedavna preteklost',
        'Možna prihodnost', 'Bližnja prihodnost', 'Tvoj pristop', 'Zunanji vplivi',
        'Upi in strahovi', 'Končni izid',
    ];

    $pozicije = [];
    foreach ($karte as $i => $karta) {
        $pozicije[] = [
            'oznaka' => $oznake[$i] ?? "Pozicija " . ($i + 1),
            'karta'  => $karta['ime'],
            'rx'     => _tarot_rx($karta),
            'pomen'  => $karta['pomen'],
        ];
    }

    return [
        'naslov'   => 'Keltski križ',
        'besedilo' => "Popolna postavitev za vprašanje \"$vprasanje\" razkriva 10 plasti situacije — "
            . "od trenutnega stanja do končnega izida. Preberi vsako pozicijo posebej za globlji vpogled.",
        'pozicije' => $pozicije,
    ];
}

function _tarot_interp_splosno(array $karte, string $vprasanje): array {
    return [
        'naslov'   => 'Vedeževanje',
        'besedilo' => "Karte za vprašanje \"$vprasanje\" so spregovorile.",
        'pozicije' => array_map(fn($k) => ['oznaka' => '', 'karta' => $k['ime'], 'rx' => _tarot_rx($k), 'pomen' => $k['pomen']], $karte),
    ];
}
