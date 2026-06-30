<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/avatar/avatar_napredovanje.php
 * v100 (10.06.2026)
 * ---------------------------------------------------------
 * OPIS: Stopnje in arhetipski razvoj Duhovnega Varuha
 * ---------------------------------------------------------
 * FUNKCIJE:
 *     avatar_ustvari(id) : array
 *     avatar_pridobi(id) : array
 *     avatar_izracunaj_stopnjo(tocke) : array
 *     avatar_pridobi_arhetip(arhetip_id) : array|null
 *     avatar_nastavi_arhetip(id, arhetip_id) : array
 * ---------------------------------------------------------
 */
declare(strict_types=1);

// ── Stopnje evolucije Varuha ─────────────────────────────────────────────────
const VARUH_STOPNJE = [
    ['stopnja' => 0, 'ime' => 'Meglica',          'ikona' => '🌫️', 'opis' => 'Šele nastajajoča zavest',        'tocke' => 0,    'evolucija' => 'senca'],
    ['stopnja' => 1, 'ime' => 'Iskrica',           'ikona' => '✨',  'opis' => 'Prvi utrinek zavesti',           'tocke' => 100,  'evolucija' => 'senca'],
    ['stopnja' => 2, 'ime' => 'Kalček',            'ikona' => '🌱',  'opis' => 'Zavest se ukorenini',            'tocke' => 300,  'evolucija' => 'silhueta'],
    ['stopnja' => 3, 'ime' => 'Rastlina',          'ikona' => '🌿',  'opis' => 'Rasteš v modrosti',             'tocke' => 600,  'evolucija' => 'silhueta'],
    ['stopnja' => 4, 'ime' => 'Cvet',              'ikona' => '🌸',  'opis' => 'Tvoja zavest cveti',            'tocke' => 1000, 'evolucija' => 'oblecheni'],
    ['stopnja' => 5, 'ime' => 'Drevo',             'ikona' => '🌳',  'opis' => 'Močna, ukoreninjena zavest',    'tocke' => 1500, 'evolucija' => 'oblecheni'],
    ['stopnja' => 6, 'ime' => 'Zvezda',            'ikona' => '⭐',  'opis' => 'Svetiš v temi',                 'tocke' => 2200, 'evolucija' => 'oblecheni'],
    ['stopnja' => 7, 'ime' => 'Sonce',             'ikona' => '☀️',  'opis' => 'Tvoja svetloba ogreva druge',   'tocke' => 3000, 'evolucija' => 'hibrid'],
    ['stopnja' => 8, 'ime' => 'Galaksija',         'ikona' => '🌌',  'opis' => 'Tvoja zavest je brezmejna',     'tocke' => 4000, 'evolucija' => 'hibrid'],
    ['stopnja' => 9, 'ime' => 'Absolut',           'ikona' => '🌀',  'opis' => 'Čista zavest – vse in nič',     'tocke' => 5000, 'evolucija' => 'ascendiran'],
];

// ── 6 Arhetipskih stebrov ────────────────────────────────────────────────────
const VARUH_ARHETIPI = [
    'lunaris' => [
        'id'       => 'lunaris',
        'ime'      => 'Lunaris',
        'opis'     => 'Intuicija, podzavest, sanje.',
        'ikona'    => '🌙',
        'barva'    => '#a78bfa',
        'silhueta' => 'Ženska silhueta z luno, tančica',
        'energija' => 'Vijolična / srebrna',
        'moduli'   => ['Lunaris', 'Somnaris', 'VibraMystica'],
        'sporocila' => [
            'Sanje ti razkrivajo resnice, ki jih budnost skriva.',
            'Luna te vodi skozi temo, zaupaj njeni svetlobi.',
            'Tvoja intuicija je most med vidnim in nevidnim.'
        ]
    ],
    'stelaris' => [
        'id'       => 'stelaris',
        'ime'      => 'Stelaris',
        'opis'     => 'Kozmično zavedanje, višji jaz.',
        'ikona'    => '⭐',
        'barva'    => '#818cf8',
        'silhueta' => 'Moška silhueta z zvezdami, krila',
        'energija' => 'Modra / zlata',
        'moduli'   => ['Stelaris', 'Jyotir', 'Seraphica', 'CosmicaScientia'],
        'sporocila' => [
            'Zvezde so tvoji predniki — slušaj njihov šepet.',
            'Tvoja pot je zapisana v kozmosu, le sledi ji.',
            'Višji jaz te kliče — ali ga slišiš?'
        ]
    ],
    'animaris' => [
        'id'       => 'animaris',
        'ime'      => 'Animaris',
        'opis'     => 'Šamanizem, narava, instinkt.',
        'ikona'    => '🐺',
        'barva'    => '#6ee7b7',
        'silhueta' => 'Žival / hibrid (volk, jelen, krokar)',
        'energija' => 'Zelena / rjava',
        'moduli'   => ['Animaris', 'BotanicaSacra', 'Somnaris', 'ViaAnimae'],
        'sporocila' => [
            'Narava govori — ali znaš poslušati?',
            'Instinkt je najstarejša modrost.',
            'Živali so tvoji učitelji, zemlja tvoja dom.'
        ]
    ],
    'sephirotica' => [
        'id'       => 'sephirotica',
        'ime'      => 'Sephirotica',
        'opis'     => 'Sveta geometrija, struktura.',
        'ikona'    => '✡️',
        'barva'    => '#fbbf24',
        'silhueta' => 'Kristalna / geometrijska figura',
        'energija' => 'Zlata / bela',
        'moduli'   => ['Sephirotica', 'NumerariumCosmicum', 'Numyra', 'AegypticaArcana'],
        'sporocila' => [
            'Vse je geometrija — v obliki je skrita resnica.',
            'Številke so jezik vesolja.',
            'Sveta struktura drži skupaj kaos.'
        ]
    ],
    'occultum' => [
        'id'       => 'occultum',
        'ime'      => 'Occultum',
        'opis'     => 'Magija, senca, transformacija.',
        'ikona'    => '🔮',
        'barva'    => '#7c3aed',
        'silhueta' => 'Temna figura z masko / plaščem',
        'energija' => 'Črna / krvavo rdeča',
        'moduli'   => ['Occultum', 'UmbraeCodex', 'LiberUmbrae', 'Tarot', 'OraculumVisionis'],
        'sporocila' => [
            'Senca je del tebe — soočiti se z njo je pogum.',
            'Transformacija zahteva, da pustiš staro za seboj.',
            'V temi leži moč, ki čaka na prebujenje.'
        ]
    ],
    'aetheris' => [
        'id'       => 'aetheris',
        'ime'      => 'Aetheris',
        'opis'     => 'Zdravilna energija, enost.',
        'ikona'    => '💫',
        'barva'    => '#67e8f9',
        'silhueta' => 'Svetlobno bitje, eterična oblika',
        'energija' => 'Turkizna / zlata',
        'moduli'   => ['QiVitalis', 'Energetica', 'Pranaymica', 'Aetheris'],
        'sporocila' => [
            'Tvoje telo je tempelj — skrbi zanj z ljubeznijo.',
            'Energija teče skozi tebe — usmeri jo z namenom.',
            'Zdravje je harmonija duha, uma in telesa.'
        ]
    ],
];

// ── Pot datoteke avatarja ─────────────────────────────────────────────────────
function _avatar_pot(string $uporabnikId): string
{
    $dir = (defined('POT_PODATKI') ? POT_PODATKI : dirname(__DIR__, 4) . '/PODATKI')
         . '/uporabniki/' . $uporabnikId;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $dir . '/avatar.json';
}

function _avatar_shrani(string $uporabnikId, array $avatar): void
{
    $avatar['zadnja_posodobitev'] = time();
    file_put_contents(
        _avatar_pot($uporabnikId),
        json_encode($avatar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

// ── Javne funkcije ────────────────────────────────────────────────────────────

function avatar_ustvari(string $uporabnikId): array
{
    $avatar = [
        'uporabnik_id'          => $uporabnikId,
        'tocke'                 => 0,
        'xp'                    => 0,
        'stopnja'               => 0,
        'ime'                   => 'Meglica',
        'ikona'                 => '🌫️',
        'evolucija'             => 'senca',
        'arhetip'               => null,
        'arhetip_afiniteta'     => [],
        'zakladnica'            => [],
        'dnevni_streak'         => 0,
        'zadnja_dnevna_nagrada' => null,
        'zgodovina_tock'        => [],
        'ustvarjeno'            => time(),
        'zadnja_posodobitev'    => time()
    ];

    _avatar_shrani($uporabnikId, $avatar);

    // Daj točke za registracijo
    return $avatar;
}

function avatar_pridobi(string $uporabnikId): array
{
    $pot = _avatar_pot($uporabnikId);
    if (!file_exists($pot)) {
        return avatar_ustvari($uporabnikId);
    }
    $podatki = json_decode(file_get_contents($pot), true);
    return $podatki ?? avatar_ustvari($uporabnikId);
}

function avatar_izracunaj_stopnjo(int $tocke): array
{
    $trenutna = VARUH_STOPNJE[0];
    $naslednja = null;

    foreach (VARUH_STOPNJE as $i => $stopnja) {
        if ($tocke >= $stopnja['tocke']) {
            $trenutna = $stopnja;
        } else {
            $naslednja = $stopnja;
            break;
        }
    }

    $doNaslednje = $naslednja ? ($naslednja['tocke'] - $tocke) : 0;
    $napredek    = $naslednja
        ? (int)(($tocke - $trenutna['tocke']) / ($naslednja['tocke'] - $trenutna['tocke']) * 100)
        : 100;

    return [
        'stopnja'            => $trenutna['stopnja'],
        'ime'                => $trenutna['ime'],
        'ikona'              => $trenutna['ikona'],
        'opis'               => $trenutna['opis'],
        'evolucija'          => $trenutna['evolucija'],
        'naslednja'          => $naslednja,
        'tocke_do_naslednje' => $doNaslednje,
        'napredek_procent'   => $napredek
    ];
}

function avatar_pridobi_arhetip(string $arhetipId): ?array
{
    return VARUH_ARHETIPI[$arhetipId] ?? null;
}

function avatar_nastavi_arhetip(string $uporabnikId, string $arhetipId): array
{
    if (!isset(VARUH_ARHETIPI[$arhetipId])) {
        return ['status' => 'napaka', 'sporocilo' => 'Neznani arhetip.'];
    }

    $avatar = avatar_pridobi($uporabnikId);
    $avatar['arhetip'] = $arhetipId;
    _avatar_shrani($uporabnikId, $avatar);

    return [
        'status'  => 'uspeh',
        'arhetip' => VARUH_ARHETIPI[$arhetipId],
        'avatar'  => $avatar
    ];
}

function avatar_pridobi_sporocilo(string $uporabnikId): string
{
    $avatar    = avatar_pridobi($uporabnikId);
    $arhetipId = $avatar['arhetip'] ?? null;

    if ($arhetipId && isset(VARUH_ARHETIPI[$arhetipId])) {
        $sporocila = VARUH_ARHETIPI[$arhetipId]['sporocila'];
        return $sporocila[array_rand($sporocila)];
    }

    $splosna = [
        'Tvoja pot se šele začenja. Korak za korakom.',
        'Vsak dan je novo poglavje tvoje zgodbe.',
        'Zavest raste v tišini in akciji.'
    ];
    return $splosna[array_rand($splosna)];
}
