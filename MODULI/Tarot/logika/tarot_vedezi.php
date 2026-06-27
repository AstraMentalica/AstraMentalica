<?php
/**
 * ============================================================
 * POT: MODULI/ORAKLEUM/Tarot/logika/tarot_vedezi.php
 * 📅 VERZIJA: v1.0.0 (19.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL / LOGIKA
 *
 * 📰 NAMEN:
 *     Glavna logika vedeževanja.
 *     KLJUČNO: sistem ne meša kart naključno — uporabnik
 *     pošlje SVOJE zaporedje izbranih kart (mešal jih je sam
 *     v vmesniku, klikal/vlekel/animiral), sistem samo:
 *     1. preveri veljavnost poslanih kart
 *     2. doda interpretacijo
 *     3. zapiše vedeževanje v uporabnikovo zgodovino
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tarot_vedezi(array $vhod, array $kontekst): array
 *     - tarot_validiraj_karte(array $karte): bool
 *
 * 📡 ODVISNOSTI:
 *     - podatki/karte_78.php   (tarot_karte_vse)
 *     - logika/interpretacija.php (tarot_interpretiraj)
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
 *     modul, tarot, logika, vedezevanje
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../podatki/karte_78.php';
require_once __DIR__ . '/interpretacija.php';

/**
 * Glavna funkcija vedeževanja.
 *
 * @param array $vhod {
 *     @var string $vprasanje  Uporabnikovo vprašanje (obvezno)
 *     @var string $sirjenje   Tip postavitve: 'ena_karta'|'tri_karte'|'keltski_kriz' (opcijsko)
 *     @var array  $karte      Karte, ki jih je uporabnik IZBRAL/IZVLEKEL v UI.
 *                             Format: [['id' => int, 'rx' => bool], ...]
 *                             ('rx' = reversed/obrnjena; sprejet je tudi alias 'obrnjena')
 * }
 * @param array $kontekst { 'uporabnik_id' => string|int }
 */
function tarot_vedezi(array $vhod, array $kontekst): array {
    $vprasanje = trim($vhod['vprasanje'] ?? '');
    $sirjenje  = $vhod['sirjenje'] ?? 'tri_karte';
    $karte_vhod = $vhod['karte'] ?? [];

    // OPOMBA: $vhod['razlicica'] je rezervirano polje iz manifesta
    // (vhod.opcijsko), namen še ni definiran — trenutno se ne uporablja.

    if ($vprasanje === '') {
        return odziv_napaka('Vprašanje je obvezno.', 422);
    }

    if (!tarot_validiraj_karte($karte_vhod)) {
        return odziv_napaka('Poslane karte niso veljavne. Karte mora izbrati uporabnik v vmesniku.', 422);
    }

    $pricakovano_stevilo = match ($sirjenje) {
        'ena_karta'    => 1,
        'tri_karte'    => 3,
        'keltski_kriz' => 10,
        default        => 3,
    };

    if (count($karte_vhod) !== $pricakovano_stevilo) {
        return odziv_napaka(
            "Postavitev '$sirjenje' zahteva $pricakovano_stevilo kart, prejetih " . count($karte_vhod) . '.',
            422
        );
    }

    // Sestavi polne podatke kart (varnostno preberi iz lokalne baze, ne zaupaj klientu)
    $vse_karte = tarot_karte_vse();
    $vse_karte_po_id = [];
    foreach ($vse_karte as $k) {
        $vse_karte_po_id[$k['id']] = $k;
    }

    $izbrane_karte = [];
    foreach ($karte_vhod as $vnos) {
        $id = (int)($vnos['id'] ?? -1);
        if (!isset($vse_karte_po_id[$id])) {
            return odziv_napaka("Karta z ID $id ne obstaja.", 422);
        }

        // Vhodni alias: API/UI sprejme tako 'rx' kot 'obrnjena' (rx ima prednost, če je podan)
        $obrnjena = (bool)($vnos['rx'] ?? $vnos['obrnjena'] ?? false);
        $karta    = $vse_karte_po_id[$id];

        $izbrane_karte[] = [
            'id'       => $karta['id'],
            'ime'      => $karta['ime'],
            'arkana'   => $karta['arkana'],
            'obrnjena' => $obrnjena,  // notranje ime (jezikovni standard)
            'rx'       => $obrnjena,  // javni alias za API/UI
            'pomen'    => $obrnjena ? $karta['obrnjeno'] : $karta['uspravno'],
        ];
    }

    $interpretacija = tarot_interpretiraj($izbrane_karte, $sirjenje, $vprasanje);

    return odziv_uspeh([
        'uid'            => 'vedz_' . bin2hex(random_bytes(8)),
        'vprasanje'      => $vprasanje,
        'sirjenje'       => $sirjenje,
        'karte'          => $izbrane_karte,
        'interpretacija' => $interpretacija,
        'datum'          => date('Y-m-d H:i:s'),
        'uporabnik_id'   => $kontekst['uporabnik_id'] ?? null,
    ], 'Vedeževanje uspešno opravljeno.');
}

/**
 * Preveri, da so poslane karte v veljavni obliki.
 * Ne preverja vsebinske pravilnosti (to dela tarot_vedezi),
 * samo strukturo: array neprazen, vsak element ima 'id'.
 */
function tarot_validiraj_karte(array $karte): bool {
    if (empty($karte)) {
        return false;
    }

    // Preveri, da ni podvojenih kart (uporabnik ne sme izbrati iste karte dvakrat)
    $ids = [];
    foreach ($karte as $vnos) {
        if (!is_array($vnos) || !isset($vnos['id'])) {
            return false;
        }
        $id = (int)$vnos['id'];
        if (in_array($id, $ids, true)) {
            return false;
        }
        $ids[] = $id;
    }

    return true;
}
