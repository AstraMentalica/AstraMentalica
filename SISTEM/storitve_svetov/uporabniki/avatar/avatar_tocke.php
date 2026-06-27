<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/avatar/avatar_tocke.php
 * v100 (10.06.2026)
 * ---------------------------------------------------------
 * OPIS: Točke in XP sistem za Duhovnega Varuha
 * ---------------------------------------------------------
 * FUNKCIJE:
 *     avatar_dodaj_tocke(id, tocke, razlog) : array
 *     avatar_dodaj_xp(id, xp, vir) : array
 *     avatar_pridobi_tocke(id) : int
 *     avatar_dnevna_nagrada(id) : array
 * ---------------------------------------------------------
 * RAZLOGI ZA TOČKE:
 *     prijava, registracija, modul_uporaba,
 *     modul_dokoncanje, meditacija, tarot,
 *     dnevna_naloga, dosezek
 * ---------------------------------------------------------
 */
declare(strict_types=1);

// Točke po razlogih
const TOCKE_PO_RAZLOGU = [
    'registracija'     => 50,
    'prijava'          => 1,
    'modul_uporaba'    => 5,
    'modul_dokoncanje' => 20,
    'meditacija'       => 10,
    'tarot'            => 8,
    'dnevna_naloga'    => 15,
    'dosezek'          => 30,
    'zakladnica'       => 3,
];

// XP po virih (moduli dajo XP glede na kategorijo)
const XP_PO_VIRU = [
    'NEBO'     => 12,
    'ZEMLJA'   => 10,
    'SIMBOLI'  => 8,
    'POTI'     => 10,
    'SVET'     => 15,
    'ORAKLEUM' => 12,
    'VIP'      => 20,
];

function avatar_dodaj_tocke(string $uporabnikId, int $tocke, string $razlog): array
{
    $avatar = avatar_pridobi($uporabnikId);
    $staraStopnja = $avatar['stopnja'] ?? 0;

    $avatar['tocke'] = ($avatar['tocke'] ?? 0) + $tocke;
    $avatar['xp']    = ($avatar['xp']    ?? 0) + (int)($tocke * 0.5);
    $avatar['zadnja_posodobitev'] = time();

    // Omejimo zgodovino na 200 zapisov
    $avatar['zgodovina_tock'][] = [
        'tocke'  => $tocke,
        'razlog' => $razlog,
        'cas'    => time()
    ];
    if (count($avatar['zgodovina_tock'] ?? []) > 200) {
        array_shift($avatar['zgodovina_tock']);
    }

    // Posodobi stopnjo
    $novaStopnja = avatar_izracunaj_stopnjo($avatar['tocke']);
    $avatar['stopnja'] = $novaStopnja['stopnja'];
    $avatar['ime']     = $novaStopnja['ime'];
    $avatar['ikona']   = $novaStopnja['ikona'];

    // Posodobi arhetip glede na aktivnosti
    $avatar = _avatar_posodobi_arhetip($avatar, $razlog);

    _avatar_shrani($uporabnikId, $avatar);

    dogodek_sprozi('avatar.tocke_dodane', [
        'uporabnik_id' => $uporabnikId,
        'tocke'        => $tocke,
        'razlog'       => $razlog,
        'nova_stopnja' => $novaStopnja['stopnja'],
        'napredoval'   => $novaStopnja['stopnja'] > $staraStopnja
    ]);

    return [
        'avatar'       => $avatar,
        'napredoval'   => $novaStopnja['stopnja'] > $staraStopnja,
        'nova_stopnja' => $novaStopnja
    ];
}

function avatar_dodaj_xp(string $uporabnikId, int $xp, string $vir): array
{
    $tocke = XP_PO_VIRU[$vir] ?? 5;
    return avatar_dodaj_tocke($uporabnikId, $tocke, 'modul_uporaba');
}

function avatar_pridobi_tocke(string $uporabnikId): int
{
    $avatar = avatar_pridobi($uporabnikId);
    return $avatar['tocke'] ?? 0;
}

function avatar_dnevna_nagrada(string $uporabnikId): array
{
    $avatar = avatar_pridobi($uporabnikId);
    $danes  = date('Y-m-d');

    // Preveri ali je nagrada danes že bila prevzeta
    if (($avatar['zadnja_dnevna_nagrada'] ?? '') === $danes) {
        return [
            'status'   => 'ze_prevzeto',
            'sporocilo' => 'Dnevna nagrada je bila že prevzeta.'
        ];
    }

    // Izračunaj nagrado glede na streak
    $streak = ($avatar['dnevni_streak'] ?? 0);
    $vceraj = date('Y-m-d', strtotime('-1 day'));

    if (($avatar['zadnja_dnevna_nagrada'] ?? '') === $vceraj) {
        $streak++;
    } else {
        $streak = 1;
    }

    $tocke = min(10 + ($streak * 2), 50); // Max 50 točk

    $avatar['dnevni_streak']        = $streak;
    $avatar['zadnja_dnevna_nagrada'] = $danes;
    _avatar_shrani($uporabnikId, $avatar);

    return avatar_dodaj_tocke($uporabnikId, $tocke, 'prijava') + [
        'streak' => $streak,
        'tocke'  => $tocke
    ];
}

function _avatar_posodobi_arhetip(array $avatar, string $razlog): array
{
    // Poveži razloge z arhetipi
    $razlogArhetip = [
        'meditacija'       => 'lunaris',
        'tarot'            => 'occultum',
        'modul_dokoncanje' => 'stelaris',
        'zakladnica'       => 'sephirotica',
        'dnevna_naloga'    => 'animaris',
    ];

    $arhetipId = $razlogArhetip[$razlog] ?? null;
    if (!$arhetipId) return $avatar;

    // Povečaj afiniteto za ta arhetip
    $avatar['arhetip_afiniteta'][$arhetipId] =
        ($avatar['arhetip_afiniteta'][$arhetipId] ?? 0) + 1;

    // Nastavi dominantni arhetip (tisti z največ afinitete)
    $max = 0;
    $dominantni = $avatar['arhetip'] ?? null;
    foreach ($avatar['arhetip_afiniteta'] as $id => $vrednost) {
        if ($vrednost > $max) {
            $max = $vrednost;
            $dominantni = $id;
        }
    }
    $avatar['arhetip'] = $dominantni;

    return $avatar;
}