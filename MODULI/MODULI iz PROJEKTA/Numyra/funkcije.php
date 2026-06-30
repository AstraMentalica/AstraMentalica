<?php
/**
 * NUMYRA — funkcije.php
 * Numerološki izračuni: realna matematika, brez placeholderjev.
 * Kliče modul.php prek akcij: izracunaj, osebno_leto, podatki, porocilo
 */

declare(strict_types=1);

// ── Pythagorean abeceda (slovenščina + latinica) ─────────────────────────────
const NUMYRA_ABECEDA = [
    'a'=>1,'b'=>2,'c'=>3,'č'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,
    'j'=>1,'k'=>2,'l'=>3,'m'=>4,'n'=>5,'o'=>6,'p'=>7,'q'=>8,'r'=>9,
    's'=>1,'š'=>1,'t'=>2,'u'=>3,'v'=>4,'w'=>5,'x'=>6,'y'=>7,'z'=>8,'ž'=>8
];

const NUMYRA_SAMOGLASNIKI = ['a','e','i','o','u'];

const NUMYRA_INTERPRETACIJE = [
    1  => ['arhetip'=>'Pionir',    'kljucne'=>'vodstvo, samostojnost, inovacija',   'izziv'=>'egoizem, trma'],
    2  => ['arhetip'=>'Diplomat',  'kljucne'=>'sodelovanje, intuicija, mir',         'izziv'=>'odvisnost, neodločnost'],
    3  => ['arhetip'=>'Ustvarjalec','kljucne'=>'ustvarjalnost, komunikacija, veselje','izziv'=>'razpršenost, površnost'],
    4  => ['arhetip'=>'Graditelj', 'kljucne'=>'red, vztrajnost, zanesljivost',       'izziv'=>'togost, omejenost'],
    5  => ['arhetip'=>'Svobodnjak','kljucne'=>'svoboda, sprememba, avantura',        'izziv'=>'nestabilnost, beg'],
    6  => ['arhetip'=>'Skrbnik',   'kljucne'=>'odgovornost, ljubezen, harmonija',    'izziv'=>'kontrola, perfekcionizem'],
    7  => ['arhetip'=>'Iskalec',   'kljucne'=>'globina, duhovnost, analiza',         'izziv'=>'zaprtost, dvom'],
    8  => ['arhetip'=>'Mojster',   'kljucne'=>'moč, uspeh, materialna resničnost',   'izziv'=>'pohlepnost, oblast'],
    9  => ['arhetip'=>'Humanist',  'kljucne'=>'sočutje, modrost, zaključki',         'izziv'=>'žrtvovanje, razdajanje'],
    11 => ['arhetip'=>'Razsvetljeni','kljucne'=>'intuicija, navdih, duhovni vodnik', 'izziv'=>'anksioznost, nerealnost'],
    22 => ['arhetip'=>'Veliki Graditelj','kljucne'=>'vizija, gradnja, manifest',     'izziv'=>'perfekcionizem, pregoreva'],
    33 => ['arhetip'=>'Mojster Učitelj','kljucne'=>'zdravljenje, razsvetljenje, služenje','izziv'=>'samožrtvovanje'],
];

const NUMYRA_LETNI_CIKLI = [
    1 => 'Leto začetkov — zasadi nova semena, začni projekte.',
    2 => 'Leto strpnosti — gradi odnose, počakaj na pravi trenutek.',
    3 => 'Leto izražanja — ustvari, komuniciraj, bodi viden.',
    4 => 'Leto dela — trdi temelji, disciplina, organizacija.',
    5 => 'Leto sprememb — premiki, nova vrata, svoboda.',
    6 => 'Leto doma — odnosi, skrb za bližnje, ravnovesje.',
    7 => 'Leto introspekcije — poglobi znanje, odmakni se od hrupa.',
    8 => 'Leto žetve — manifestacija uspeha, materialni premiki.',
    9 => 'Leto zaključkov — spusti staro, pripravi se na novo.',
];

// ─────────────────────────────────────────────────────────────────────────────
// OSNOVNA MATEMATIKA
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Redukcija na eno števko (ohrani mojstrska 11, 22, 33).
 */
function numyra_reduciraj(int $n, bool $ohrani_mojstrska = true): int {
    while ($n > 9) {
        if ($ohrani_mojstrska && in_array($n, [11, 22, 33], true)) break;
        $n = array_sum(str_split((string)$n));
    }
    return $n;
}

/**
 * Numerološka vrednost znaka (UTF-8 varna).
 */
function numyra_vrednost_znaka(string $znak): int {
    $znak = mb_strtolower($znak, 'UTF-8');
    return NUMYRA_ABECEDA[$znak] ?? 0;
}

/**
 * Vsota numeroloških vrednosti besedila.
 */
function numyra_vsota_besedila(string $besedilo): int {
    $vsota = 0;
    $dolzina = mb_strlen($besedilo, 'UTF-8');
    for ($i = 0; $i < $dolzina; $i++) {
        $vsota += numyra_vrednost_znaka(mb_substr($besedilo, $i, 1, 'UTF-8'));
    }
    return $vsota;
}

// ─────────────────────────────────────────────────────────────────────────────
// GLAVNE FUNKCIJE (klicane iz modul.php)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Celovita analiza: ime + priimek + datum.
 * Vrne asociativni array z vsemi številkami.
 */
function numyra_izracunaj(string $besedilo): array {
    // Pričakuje format "Ime Priimek YYYY-MM-DD" ali samo ime
    $deli = preg_split('/\s+/', trim($besedilo));

    // Razloči datum od imen
    $datum = null;
    $ime_deli = [];
    foreach ($deli as $del) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $del) ||
            preg_match('/^\d{1,2}\.\d{1,2}\.\d{4}$/', $del)) {
            $datum = $del;
        } else {
            $ime_deli[] = $del;
        }
    }

    $polno_ime = implode('', $ime_deli);
    $polno_ime_lower = mb_strtolower($polno_ime, 'UTF-8');

    // Življenjska pot
    $zivljenjska_pot = $datum ? numyra_zivljenjska_pot($datum) : null;

    // Dušna številka (samoglasniki)
    $samoglasniki = '';
    $dolzina = mb_strlen($polno_ime_lower, 'UTF-8');
    for ($i = 0; $i < $dolzina; $i++) {
        $z = mb_substr($polno_ime_lower, $i, 1, 'UTF-8');
        if (in_array($z, NUMYRA_SAMOGLASNIKI, true)) $samoglasniki .= $z;
    }
    $dusna = numyra_reduciraj(numyra_vsota_besedila($samoglasniki));

    // Osebno število (soglasniki)
    $soglasniki = '';
    for ($i = 0; $i < $dolzina; $i++) {
        $z = mb_substr($polno_ime_lower, $i, 1, 'UTF-8');
        if (!in_array($z, NUMYRA_SAMOGLASNIKI, true) && isset(NUMYRA_ABECEDA[$z])) {
            $soglasniki .= $z;
        }
    }
    $osebno = numyra_reduciraj(numyra_vsota_besedila($soglasniki));

    // Izrazno število (vse črke)
    $izrazno = numyra_reduciraj(numyra_vsota_besedila($polno_ime_lower));

    // Karmične lekcije (manjkajoče vrednosti 1-9)
    $prisotne = [];
    for ($i = 0; $i < $dolzina; $i++) {
        $v = numyra_vrednost_znaka(mb_substr($polno_ime_lower, $i, 1, 'UTF-8'));
        if ($v > 0) $prisotne[$v] = true;
    }
    $karmicne_lekcije = [];
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($prisotne[$i])) $karmicne_lekcije[] = $i;
    }

    // Mojstrske številke
    $vse_stevike = array_filter(
        [$zivljenjska_pot, $dusna, $osebno, $izrazno],
        fn($s) => in_array($s, [11, 22, 33], true)
    );

    $rezultat = [
        'ime'              => implode(' ', $ime_deli),
        'izrazno_stevilo'  => $izrazno,
        'dusna_stevilka'   => $dusna,
        'osebno_stevilo'   => $osebno,
        'karmicne_lekcije' => $karmicne_lekcije,
        'mojstrske'        => array_values(array_unique($vse_stevike)),
    ];

    if ($zivljenjska_pot !== null) {
        $rezultat['zivljenjska_pot'] = $zivljenjska_pot;
        $rezultat['datum'] = $datum;
    }

    // Dodaj interpretacije
    foreach (['zivljenjska_pot','dusna_stevilka','osebno_stevilo','izrazno_stevilo'] as $kljuc) {
        if (isset($rezultat[$kljuc])) {
            $st = $rezultat[$kljuc];
            $rezultat['interpretacije'][$kljuc] = NUMYRA_INTERPRETACIJE[$st] ?? null;
        }
    }

    // Bridge izhod (za Svetovalec/Mystaia)
    $rezultat['bridge'] = [
        'modul'           => 'numyra',
        'manjkajoca_stevila' => $karmicne_lekcije,
        'dominantno'      => $zivljenjska_pot ?? $izrazno,
        'mojstrska'       => !empty($vse_stevike),
    ];

    return $rezultat;
}

/**
 * Življenjska pot iz datuma rojstva.
 */
function numyra_zivljenjska_pot(string $datum): int {
    // Sprejme YYYY-MM-DD ali D.M.YYYY
    $datum = preg_replace('/[^\d]/', '', $datum);
    return numyra_reduciraj(array_sum(str_split($datum)));
}

/**
 * Osebno leto (za dashboard in Svetovalec).
 */
function numyra_osebno_leto(string $datum, ?int $leto = null): array {
    $leto = $leto ?? (int)date('Y');

    // Dan + mesec + tekoče leto
    preg_match('/(\d{1,4})[.\-\/](\d{1,2})[.\-\/](\d{2,4})/', $datum, $m);
    if (count($m) < 4) return ['napaka' => 'Neveljaven datum'];

    // Ugotovi kateri je dan in mesec
    if (strlen($m[1]) === 4) {
        [$y, $mes, $dan] = [(int)$m[1], (int)$m[2], (int)$m[3]];
    } else {
        [$dan, $mes, $y] = [(int)$m[1], (int)$m[2], (int)$m[3]];
    }

    $vsota = array_sum(str_split((string)$dan))
           + array_sum(str_split((string)$mes))
           + array_sum(str_split((string)$leto));

    $osebno_leto = numyra_reduciraj($vsota);
    $naslednje   = numyra_reduciraj($vsota - $osebno_leto + ($osebno_leto % 9 ?: 9) + 1);
    if ($naslednje > 9) $naslednje = 1;

    return [
        'leto'        => $leto,
        'osebno_leto' => $osebno_leto,
        'opis'        => NUMYRA_LETNI_CIKLI[$osebno_leto] ?? '',
        'naslednje_leto' => [
            'stevilo' => $naslednje,
            'opis'    => NUMYRA_LETNI_CIKLI[$naslednje] ?? '',
        ],
        'bridge' => [
            'modul'       => 'numyra',
            'osebno_leto' => $osebno_leto,
            'energija'    => $osebno_leto <= 4 ? 'gradnja' : ($osebno_leto <= 7 ? 'zorenje' : 'zaključek'),
        ],
    ];
}

/**
 * Primerjava dveh oseb (sinastrija).
 */
function numyra_sinastrija(string $besedilo1, string $besedilo2): array {
    $a = numyra_izracunaj($besedilo1);
    $b = numyra_izracunaj($besedilo2);

    // Ujemanje življenjskih poti
    $pot_a = $a['zivljenjska_pot'] ?? $a['izrazno_stevilo'];
    $pot_b = $b['zivljenjska_pot'] ?? $b['izrazno_stevilo'];

    // Kompatibilnost (1-9 kvadrantna matrika)
    $naravno_ujemanje = [
        1=>[1,5,7], 2=>[2,4,8], 3=>[3,6,9], 4=>[2,4,8],
        5=>[1,5,7], 6=>[3,6,9], 7=>[1,5,7], 8=>[2,4,8], 9=>[3,6,9],
    ];
    $ujema_se = in_array($pot_b, $naravno_ujemanje[$pot_a] ?? [], true);

    return [
        'oseba_a' => ['ime'=>$a['ime'], 'pot'=>$pot_a],
        'oseba_b' => ['ime'=>$b['ime'], 'pot'=>$pot_b],
        'naravno_ujemanje' => $ujema_se,
        'skupne_lekcije'   => array_intersect($a['karmicne_lekcije'], $b['karmicne_lekcije']),
        'dopolnjevanje'    => array_diff($b['karmicne_lekcije'], $a['karmicne_lekcije']),
        'opis' => $ujema_se
            ? 'Naravna harmonija — energiji se dopolnjujeta.'
            : 'Izzivno partnerstvo — rast skozi različnost.',
        'bridge' => [
            'modul'   => 'numyra',
            'tip'     => 'sinastrija',
            'ujemanje'=> $ujema_se,
        ],
    ];
}
