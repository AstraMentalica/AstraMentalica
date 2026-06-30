<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/avatar/avatar_nagrade.php
 * v100 (10.06.2026)
 * ---------------------------------------------------------
 * OPIS: Zakladnica Varuha – simboli, kristali, rune, odklenitve
 * ---------------------------------------------------------
 * FUNKCIJE:
 *     avatar_zakladnica_dodaj(id, predmet_id) : array
 *     avatar_zakladnica_pridobi(id) : array
 *     avatar_odkleni_predmet(id, predmet_id) : array
 *     avatar_preveri_dosezek(id, dosezek_id) : bool
 *     avatar_dodaj_dosezek(id, dosezek_id) : array
 * ---------------------------------------------------------
 */
declare(strict_types=1);

// ── Predmeti zakladnice ───────────────────────────────────────────────────────
const ZAKLADNICA_PREDMETI = [

    // Kristali (Lapidaria)
    'kristal_kvarc' => [
        'id' => 'kristal_kvarc', 'tip' => 'kristal',
        'ime' => 'Jasni Kvarc', 'ikona' => '💎',
        'opis' => 'Ojačuje jasnost in fokus.',
        'bonus' => ['tocke_mnozitelj' => 1.05],
        'pridobi_prek' => 'Lapidaria'
    ],
    'kristal_ametist' => [
        'id' => 'kristal_ametist', 'tip' => 'kristal',
        'ime' => 'Ametist', 'ikona' => '🔮',
        'opis' => 'Krepi intuicijo in duhovni uvid.',
        'bonus' => ['arhetip_bonus' => 'lunaris'],
        'pridobi_prek' => 'Lapidaria'
    ],
    'kristal_obsidian' => [
        'id' => 'kristal_obsidian', 'tip' => 'kristal',
        'ime' => 'Obsidian', 'ikona' => '⚫',
        'opis' => 'Zaščita pred negativnimi energijami.',
        'bonus' => ['arhetip_bonus' => 'occultum'],
        'pridobi_prek' => 'Lapidaria'
    ],

    // Rune (NordicaMystica)
    'runa_algiz' => [
        'id' => 'runa_algiz', 'tip' => 'runa',
        'ime' => 'Algiz (ᛉ)', 'ikona' => 'ᛉ',
        'opis' => 'Zaščita in višja zavest.',
        'bonus' => ['xp_bonus' => 10],
        'pridobi_prek' => 'NordicaMystica'
    ],
    'runa_othala' => [
        'id' => 'runa_othala', 'tip' => 'runa',
        'ime' => 'Othala (ᛟ)', 'ikona' => 'ᛟ',
        'opis' => 'Dediščina in korenine.',
        'bonus' => ['arhetip_bonus' => 'animaris'],
        'pridobi_prek' => 'NordicaMystica'
    ],
    'runa_tiwaz' => [
        'id' => 'runa_tiwaz', 'tip' => 'runa',
        'ime' => 'Tiwaz (ᛏ)', 'ikona' => 'ᛏ',
        'opis' => 'Pravičnost in pogum.',
        'bonus' => ['tocke_bonus' => 5],
        'pridobi_prek' => 'NordicaMystica'
    ],

    // Egipčanski simboli (AegypticaArcana)
    'simbol_ankh' => [
        'id' => 'simbol_ankh', 'tip' => 'simbol',
        'ime' => 'Ankh (☥)', 'ikona' => '☥',
        'opis' => 'Simbol življenja in nesmrtnosti.',
        'bonus' => ['dnevna_nagrada_bonus' => 5],
        'pridobi_prek' => 'AegypticaArcana'
    ],
    'simbol_oko_ra' => [
        'id' => 'simbol_oko_ra', 'tip' => 'simbol',
        'ime' => 'Oko Ra (𓂀)', 'ikona' => '𓂀',
        'opis' => 'Zaščita in kozmični uvid.',
        'bonus' => ['arhetip_bonus' => 'stelaris'],
        'pridobi_prek' => 'AegypticaArcana'
    ],

    // Sigili (Occultum)
    'sigil_transformacije' => [
        'id' => 'sigil_transformacije', 'tip' => 'sigil',
        'ime' => 'Sigil Transformacije', 'ikona' => '⎊',
        'opis' => 'Pospeši transformacijo zavesti.',
        'bonus' => ['xp_mnozitelj' => 1.1],
        'pridobi_prek' => 'Occultum'
    ],

    // Talismani (posebni dosežki)
    'talisman_iskalca' => [
        'id' => 'talisman_iskalca', 'tip' => 'talisman',
        'ime' => 'Talisman Iskalca', 'ikona' => '🧭',
        'opis' => 'Podeljen ob prvem koraku na poti.',
        'bonus' => ['tocke_bonus' => 10],
        'pridobi_prek' => 'registracija'
    ],
    'talisman_zvestobe' => [
        'id' => 'talisman_zvestobe', 'tip' => 'talisman',
        'ime' => 'Talisman Zvestobe', 'ikona' => '🌟',
        'opis' => '7 dni zapored na poti.',
        'bonus' => ['dnevna_nagrada_bonus' => 10],
        'pridobi_prek' => 'streak_7'
    ],
];

// ── Dosežki ───────────────────────────────────────────────────────────────────
const DOSEZKI = [
    'prvi_korak' => [
        'id'     => 'prvi_korak',
        'ime'    => 'Prvi Korak',
        'ikona'  => '👣',
        'opis'   => 'Registriral si se v sistem.',
        'nagrada' => ['tocke' => 50, 'predmet' => 'talisman_iskalca']
    ],
    'teden_zvestobe' => [
        'id'     => 'teden_zvestobe',
        'ime'    => 'Teden Zvestobe',
        'ikona'  => '📅',
        'opis'   => '7 dni zapored si se prijavil.',
        'nagrada' => ['tocke' => 100, 'predmet' => 'talisman_zvestobe']
    ],
    'prvi_modul' => [
        'id'     => 'prvi_modul',
        'ime'    => 'Raziskovalec',
        'ikona'  => '🔍',
        'opis'   => 'Prvič si uporabil modul.',
        'nagrada' => ['tocke' => 30, 'predmet' => 'runa_algiz']
    ],
    'arhetip_izbran' => [
        'id'     => 'arhetip_izbran',
        'ime'    => 'Pot Izbrana',
        'ikona'  => '🌀',
        'opis'   => 'Izbral si svoj arhetip.',
        'nagrada' => ['tocke' => 50]
    ],
];

// ── Javne funkcije ────────────────────────────────────────────────────────────

function avatar_zakladnica_dodaj(string $uporabnikId, string $predmetId): array
{
    if (!isset(ZAKLADNICA_PREDMETI[$predmetId])) {
        return ['status' => 'napaka', 'sporocilo' => 'Neznani predmet.'];
    }

    $avatar = avatar_pridobi($uporabnikId);

    // Preveri ali predmet že ima
    if (in_array($predmetId, $avatar['zakladnica'] ?? [])) {
        return ['status' => 'ze_ima', 'sporocilo' => 'Predmet je že v zakladnici.'];
    }

    $avatar['zakladnica'][] = $predmetId;
    _avatar_shrani($uporabnikId, $avatar);

    $predmet = ZAKLADNICA_PREDMETI[$predmetId];

    // Dodaj bonus točke če predmet ima tocke_bonus
    if (!empty($predmet['bonus']['tocke_bonus'])) {
        avatar_dodaj_tocke($uporabnikId, $predmet['bonus']['tocke_bonus'], 'zakladnica');
    }

    dogodek_sprozi('avatar.predmet_dodan', [
        'uporabnik_id' => $uporabnikId,
        'predmet_id'   => $predmetId,
        'predmet'      => $predmet
    ]);

    return [
        'status'  => 'uspeh',
        'predmet' => $predmet,
        'sporocilo' => 'Dodano v zakladnico: ' . $predmet['ime']
    ];
}

function avatar_zakladnica_pridobi(string $uporabnikId): array
{
    $avatar = avatar_pridobi($uporabnikId);
    $zakladnica = [];

    foreach ($avatar['zakladnica'] ?? [] as $predmetId) {
        if (isset(ZAKLADNICA_PREDMETI[$predmetId])) {
            $zakladnica[] = ZAKLADNICA_PREDMETI[$predmetId];
        }
    }

    return $zakladnica;
}

function avatar_preveri_dosezek(string $uporabnikId, string $dosezekId): bool
{
    $avatar = avatar_pridobi($uporabnikId);
    return in_array($dosezekId, $avatar['dosezki'] ?? []);
}

function avatar_dodaj_dosezek(string $uporabnikId, string $dosezekId): array
{
    if (!isset(DOSEZKI[$dosezekId])) {
        return ['status' => 'napaka', 'sporocilo' => 'Neznan dosežek.'];
    }

    if (avatar_preveri_dosezek($uporabnikId, $dosezekId)) {
        return ['status' => 'ze_ima', 'sporocilo' => 'Dosežek je bil že podeljen.'];
    }

    $avatar = avatar_pridobi($uporabnikId);
    $avatar['dosezki'][] = $dosezekId;
    _avatar_shrani($uporabnikId, $avatar);

    $dosezek = DOSEZKI[$dosezekId];

    // Dodaj nagrado
    if (!empty($dosezek['nagrada']['tocke'])) {
        avatar_dodaj_tocke($uporabnikId, $dosezek['nagrada']['tocke'], 'dosezek');
    }
    if (!empty($dosezek['nagrada']['predmet'])) {
        avatar_zakladnica_dodaj($uporabnikId, $dosezek['nagrada']['predmet']);
    }

    dogodek_sprozi('avatar.dosezek_dodan', [
        'uporabnik_id' => $uporabnikId,
        'dosezek_id'   => $dosezekId,
        'dosezek'      => $dosezek
    ]);

    return [
        'status'  => 'uspeh',
        'dosezek' => $dosezek,
        'sporocilo' => '🏆 Dosežek odklenjen: ' . $dosezek['ime']
    ];
}
