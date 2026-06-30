<?php
/**
 * NUMYRA — funkcije.php
 * Numerološki izračuni: realna matematika, brez placeholderjev.
 * Pomeni in interpretacije se berejo iz numyra_baza/pomeni.json
 * — dopolnjuj bazo, koda ostane nedotaknjena.
 *
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

// ─────────────────────────────────────────────────────────────────────────────
// BAZA POMENOV (JSON — urejaj numyra_baza/pomeni.json, ne to datoteko)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Naloži in cache-aj bazo pomenov (en branje na zahtevo).
 */
function numyra_baza(): array {
    static $baza = null;
    if ($baza !== null) return $baza;

    $pot = __DIR__ . '/numyra_baza/pomeni.json';
    if (!file_exists($pot)) {
        // Varno odpovedovanje — modul deluje tudi brez baze (samo brez besedil)
        return $baza = ['stevila' => [], 'letni_cikli' => [], 'glavni_cikli' => [],
                         'angelska_stevila' => [], 'kompatibilnost_matrika' => []];
    }

    $vsebina = file_get_contents($pot);
    $podatki = json_decode($vsebina, true);

    return $baza = $podatki ?? [];
}

/**
 * Pomen posameznega števila iz baze.
 */
function numyra_pomen_stevila(int $stevilo): ?array {
    $baza = numyra_baza();
    return $baza['stevila'][(string)$stevilo] ?? null;
}

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
    $dolzina = mb_strlen($polno_ime_lower, 'UTF-8');

    // Življenjska pot
    $zivljenjska_pot = $datum ? numyra_zivljenjska_pot($datum) : null;

    // Dušna številka (samoglasniki)
    $samoglasniki = '';
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

        // Trije glavni cikli (potrebujejo datum)
        $rezultat['glavni_cikli'] = numyra_glavni_cikli($datum);
    }

    // Dodaj interpretacije iz JSON baze
    foreach (['zivljenjska_pot','dusna_stevilka','osebno_stevilo','izrazno_stevilo'] as $kljuc) {
        if (isset($rezultat[$kljuc])) {
            $rezultat['interpretacije'][$kljuc] = numyra_pomen_stevila($rezultat[$kljuc]);
        }
    }

    // Angelska števila v vnesenem besedilu (npr. "111", "222")
    $angelska = numyra_najdi_angelska($besedilo);
    if (!empty($angelska)) {
        $rezultat['angelska_stevila'] = $angelska;
    }

    // Bridge izhod (za Svetovalec/Mystaio)
    $rezultat['bridge'] = [
        'modul'              => 'numyra',
        'manjkajoca_stevila' => $karmicne_lekcije,
        'dominantno'         => $zivljenjska_pot ?? $izrazno,
        'mojstrska'          => !empty($vse_stevike),
    ];

    return $rezultat;
}

/**
 * Življenjska pot iz datuma rojstva.
 */
function numyra_zivljenjska_pot(string $datum): int {
    $datum = preg_replace('/[^\d]/', '', $datum);
    return numyra_reduciraj(array_sum(str_split($datum)));
}

/**
 * Trije glavni cikli: mladost (mesec), zrelost (dan), starost (leto).
 * Pomeni se berejo iz baze (glavni_cikli).
 */
function numyra_glavni_cikli(string $datum): array {
    // Razčleni datum (sprejme YYYY-MM-DD ali D.M.YYYY)
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $datum, $m)) {
        [, $leto, $mesec, $dan] = $m;
    } elseif (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $datum, $m)) {
        [, $dan, $mesec, $leto] = $m;
    } else {
        return ['napaka' => 'Neprepoznan format datuma'];
    }

    $cikel_mladost  = numyra_reduciraj((int)$mesec);
    $cikel_zrelost  = numyra_reduciraj((int)$dan);
    $cikel_starost  = numyra_reduciraj(array_sum(str_split((string)(int)$leto)));

    $baza = numyra_baza()['glavni_cikli'] ?? [];

    return [
        'mladost' => [
            'stevilo'  => $cikel_mladost,
            'obdobje'  => $baza['mladost']['obdobje'] ?? '0–28 let',
            'opis'     => $baza['mladost']['opis'] ?? '',
        ],
        'zrelost' => [
            'stevilo'  => $cikel_zrelost,
            'obdobje'  => $baza['zrelost']['obdobje'] ?? '28–56 let',
            'opis'     => $baza['zrelost']['opis'] ?? '',
        ],
        'starost' => [
            'stevilo'  => $cikel_starost,
            'obdobje'  => $baza['starost']['obdobje'] ?? '56+ let',
            'opis'     => $baza['starost']['opis'] ?? '',
        ],
    ];
}

/**
 * Osebno leto (za dashboard in Svetovalec).
 */
function numyra_osebno_leto(string $datum, ?int $leto = null): array {
    $leto = $leto ?? (int)date('Y');

    preg_match('/(\d{1,4})[.\-\/](\d{1,2})[.\-\/](\d{2,4})/', $datum, $m);
    if (count($m) < 4) return ['napaka' => 'Neveljaven datum'];

    if (strlen($m[1]) === 4) {
        [$y, $mes, $dan] = [(int)$m[1], (int)$m[2], (int)$m[3]];
    } else {
        [$dan, $mes, $y] = [(int)$m[1], (int)$m[2], (int)$m[3]];
    }

    $vsota = array_sum(str_split((string)$dan))
           + array_sum(str_split((string)$mes))
           + array_sum(str_split((string)$leto));

    $osebno_leto = numyra_reduciraj($vsota);
    $naslednje   = ($osebno_leto % 9) + 1;

    $cikli = numyra_baza()['letni_cikli'] ?? [];

    return [
        'leto'        => $leto,
        'osebno_leto' => $osebno_leto,
        'opis'        => $cikli[(string)$osebno_leto] ?? '',
        'naslednje_leto' => [
            'stevilo' => $naslednje,
            'opis'    => $cikli[(string)$naslednje] ?? '',
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

    $pot_a = $a['zivljenjska_pot'] ?? $a['izrazno_stevilo'];
    $pot_b = $b['zivljenjska_pot'] ?? $b['izrazno_stevilo'];

    $matrika = numyra_baza()['kompatibilnost_matrika'] ?? [];
    $ujema_se = in_array($pot_b, $matrika[(string)$pot_a] ?? [], true);

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
            'modul'    => 'numyra',
            'tip'      => 'sinastrija',
            'ujemanje' => $ujema_se,
        ],
    ];
}

/**
 * Iskalec imen — najde imena/vzdevke, ki dajo ciljno število.
 * Preizkusi seznam kandidatov (npr. variante imena, vzdevki) in vrne tiste,
 * ki se reducirajo na željeno vibracijo.
 *
 * @param array $kandidati  Seznam imen za preizkus (npr. ["Ana","Anuška","Anita"])
 * @param int   $cilj       Željeno število (1-9, 11, 22, 33)
 */
function numyra_iskalec_imen(array $kandidati, int $cilj): array {
    $zadetki = [];
    $vsi = [];

    foreach ($kandidati as $ime) {
        $izrazno = numyra_reduciraj(numyra_vsota_besedila(mb_strtolower($ime, 'UTF-8')));
        $vnos = ['ime' => $ime, 'izrazno_stevilo' => $izrazno];
        $vsi[] = $vnos;
        if ($izrazno === $cilj) $zadetki[] = $vnos;
    }

    return [
        'cilj'           => $cilj,
        'pomen_cilja'    => numyra_pomen_stevila($cilj),
        'zadetki'        => $zadetki,
        'vsi_preizkusani'=> $vsi,
        'najdeno'        => !empty($zadetki),
    ];
}

/**
 * Najdi angelska števila (ponavljajoče se trojke) v poljubnem besedilu.
 */
function numyra_najdi_angelska(string $besedilo): array {
    $baza = numyra_baza()['angelska_stevila'] ?? [];
    $najdeno = [];

    if (preg_match_all('/(\d)\1{2}/', $besedilo, $m)) {
        foreach (array_unique($m[0]) as $zaporedje) {
            $najdeno[$zaporedje] = $baza[$zaporedje] ?? 'Ponavljajoče se število — okrepljena vibracija.';
        }
    }

    return $najdeno;
}
