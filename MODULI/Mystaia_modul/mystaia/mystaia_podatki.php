<?php
/**
 * mystaia_podatki.php – Direktna JSON shramba za Mystaia
 * Ne gre prek bridgea – bere/piše direktno v PODATKI/mystaia/
 * Bridge se pokliče SAMO za preverjanje vloge pri admin akcijah.
 */

defined('MYSTAIA_ACTIVE') or die('Direkten dostop ni dovoljen.');

// ── POTI ────────────────────────────────────────────────────────────────────
define('MY_POT',        PODATKI . '/mystaia');
define('MY_ARTIKLI',    MY_POT  . '/artikli.json');
define('MY_NAROCILA',   MY_POT  . '/narocila.json');
define('MY_NASTAVITVE', MY_POT  . '/nastavitve.json');
define('MY_KATEGORIJE', MY_POT  . '/kategorije.json');

// ── INIT – ustvari mape in seed podatke ─────────────────────────────────────
function mystaia_init(): void {
    if (!is_dir(MY_POT)) mkdir(MY_POT, 0755, true);
    if (!file_exists(MY_ARTIKLI))    mystaia_seed_artikli();
    if (!file_exists(MY_NAROCILA))   file_put_contents(MY_NAROCILA, '[]');
    if (!file_exists(MY_NASTAVITVE)) mystaia_seed_nastavitve();
    if (!file_exists(MY_KATEGORIJE)) mystaia_seed_kategorije();
}

// ── BERI/PISI JSON ──────────────────────────────────────────────────────────
function my_beri(string $pot): array {
    if (!file_exists($pot)) return [];
    return json_decode(file_get_contents($pot), true) ?? [];
}

function my_pisi(string $pot, array $data): void {
    file_put_contents($pot, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// ── ARTIKLI ─────────────────────────────────────────────────────────────────
function mystaia_artikli(string $kategorija = ''): array {
    $vse = my_beri(MY_ARTIKLI);
    if ($kategorija && $kategorija !== 'vse') {
        return array_values(array_filter($vse, fn($a) => $a['kategorija'] === $kategorija && $a['aktivno']));
    }
    return array_values(array_filter($vse, fn($a) => $a['aktivno']));
}

function mystaia_artikel(string $id): ?array {
    $vse = my_beri(MY_ARTIKLI);
    foreach ($vse as $a) {
        if ($a['id'] === $id) return $a;
    }
    return null;
}

function mystaia_shrani_artikel(array $artikel): void {
    $vse = my_beri(MY_ARTIKLI);
    $najden = false;
    foreach ($vse as &$a) {
        if ($a['id'] === $artikel['id']) { $a = $artikel; $najden = true; break; }
    }
    if (!$najden) $vse[] = $artikel;
    my_pisi(MY_ARTIKLI, $vse);
}

function mystaia_brisi_artikel(string $id): void {
    $vse = my_beri(MY_ARTIKLI);
    $vse = array_values(array_filter($vse, fn($a) => $a['id'] !== $id));
    my_pisi(MY_ARTIKLI, $vse);
}

// ── KOŠARICA (seja) ──────────────────────────────────────────────────────────
function mystaia_kosarica_dodaj(string $artikel_id, int $kolicina = 1): array {
    $kosarica = $_SESSION['mystaia_kosarica'] ?? [];
    $artikel = mystaia_artikel($artikel_id);
    if (!$artikel) return ['ok' => false, 'napaka' => 'Artikel ne obstaja'];
    if ($artikel['zalogo'] < $kolicina) return ['ok' => false, 'napaka' => 'Ni dovolj zaloge'];

    if (isset($kosarica[$artikel_id])) {
        $kosarica[$artikel_id]['kolicina'] += $kolicina;
    } else {
        $kosarica[$artikel_id] = ['artikel' => $artikel, 'kolicina' => $kolicina];
    }
    $_SESSION['mystaia_kosarica'] = $kosarica;
    return ['ok' => true, 'stevilo' => array_sum(array_column($kosarica, 'kolicina'))];
}

function mystaia_kosarica_odstrani(string $artikel_id): void {
    $kosarica = $_SESSION['mystaia_kosarica'] ?? [];
    unset($kosarica[$artikel_id]);
    $_SESSION['mystaia_kosarica'] = $kosarica;
}

function mystaia_kosarica_kolicina(string $artikel_id, int $kolicina): void {
    $kosarica = $_SESSION['mystaia_kosarica'] ?? [];
    if ($kolicina <= 0) { unset($kosarica[$artikel_id]); }
    else { $kosarica[$artikel_id]['kolicina'] = $kolicina; }
    $_SESSION['mystaia_kosarica'] = $kosarica;
}

function mystaia_kosarica_skupaj(): array {
    $kosarica = $_SESSION['mystaia_kosarica'] ?? [];
    $vsota = 0;
    foreach ($kosarica as $vnos) {
        $vsota += $vnos['artikel']['cena'] * $vnos['kolicina'];
    }
    return ['postavke' => $kosarica, 'vsota' => $vsota, 'stevilo' => array_sum(array_column($kosarica, 'kolicina'))];
}

// ── NAROČILA ─────────────────────────────────────────────────────────────────
function mystaia_ustvari_narocilo(array $podatki_kupca): array {
    $kosarica = mystaia_kosarica_skupaj();
    if (empty($kosarica['postavke'])) return ['ok' => false, 'napaka' => 'Košarica je prazna'];

    // Validacija
    $ime   = trim($podatki_kupca['ime']   ?? '');
    $email = trim($podatki_kupca['email'] ?? '');
    $naslov= trim($podatki_kupca['naslov']?? '');
    $tel   = trim($podatki_kupca['tel']   ?? '');

    if (!$ime || !$email || !$naslov) return ['ok' => false, 'napaka' => 'Izpolni vsa obvezna polja'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['ok' => false, 'napaka' => 'Neveljavna e-pošta'];

    $id = 'MY-' . strtoupper(substr(md5(uniqid()), 0, 6)) . '-' . date('Ymd');
    $narocilo = [
        'id'         => $id,
        'cas'        => date('Y-m-d H:i:s'),
        'kupec'      => ['ime' => $ime, 'email' => $email, 'naslov' => $naslov, 'tel' => $tel],
        'postavke'   => $kosarica['postavke'],
        'vsota'      => $kosarica['vsota'],
        'status'     => 'novo',
        'opomba'     => htmlspecialchars($podatki_kupca['opomba'] ?? ''),
        'placilo'    => $podatki_kupca['placilo'] ?? 'po_povzetku',
        'uporabnik_id' => $_SESSION['uporabnik_id'] ?? null,
    ];

    $narocila = my_beri(MY_NAROCILA);
    array_unshift($narocila, $narocilo);
    my_pisi(MY_NAROCILA, $narocila);

    // Zmanjšaj zalogo
    foreach ($kosarica['postavke'] as $vnos) {
        $a = mystaia_artikel($vnos['artikel']['id']);
        if ($a) { $a['zalogo'] = max(0, $a['zalogo'] - $vnos['kolicina']); mystaia_shrani_artikel($a); }
    }

    // Počisti košarico
    $_SESSION['mystaia_kosarica'] = [];

    return ['ok' => true, 'id' => $id, 'vsota' => $narocilo['vsota']];
}

function mystaia_narocila_vse(): array { return my_beri(MY_NAROCILA); }
function mystaia_narocilo(string $id): ?array {
    foreach (my_beri(MY_NAROCILA) as $n) { if ($n['id'] === $id) return $n; }
    return null;
}
function mystaia_narocilo_status(string $id, string $status): void {
    $narocila = my_beri(MY_NAROCILA);
    $dovoljeni = ['novo','potrjeno','v_obdelavi','poslano','dostavljeno','stornirano'];
    foreach ($narocila as &$n) {
        if ($n['id'] === $id) { $n['status'] = in_array($status,$dovoljeni) ? $status : $n['status']; break; }
    }
    my_pisi(MY_NAROCILA, $narocila);
}

// ── NASTAVITVE ───────────────────────────────────────────────────────────────
function mystaia_nastavitve(): array { return my_beri(MY_NASTAVITVE); }

// ── SEED PODATKI ─────────────────────────────────────────────────────────────
function mystaia_seed_artikli(): void {
    my_pisi(MY_ARTIKLI, [
        ['id'=>'mys-001','ime'=>'Eterično olje vrtnice Damascene','opis'=>'Čisto eterično olje iz damske vrtnice. Razkošen, globok cvetlični vonj, ki odpira srce.','opis_kratek'=>'100% čisto, ročno destilirano','cena'=>38.90,'kategorija'=>'eterična_olja','zalogo'=>24,'aktivno'=>true,'ikona'=>'🌹','znacke'=>['bestseller','organsko'],'teza'=>'10ml'],
        ['id'=>'mys-002','ime'=>'Kadilo benzoin & mira','opis'=>'Ročno valjano kadilo iz smol benzoin in mira. Zemeljski vonj za meditacijo in mir.','opis_kratek'=>'20 palic, ročno valjano','cena'=>12.50,'kategorija'=>'kadilo','zalogo'=>60,'aktivno'=>true,'ikona'=>'🪔','znacke'=>['novo'],'teza'=>'20g'],
        ['id'=>'mys-003','ime'=>'Kristal gorski kvarc','opis'=>'Naravni gorski kvarc, čiščen z mesečino. Idealen za meditacijo in energetsko usklajevanje.','opis_kratek'=>'Naravni, neobarvan','cena'=>24.00,'kategorija'=>'kristali','zalogo'=>15,'aktivno'=>true,'ikona'=>'💎','znacke'=>['limitirano'],'teza'=>'~80g'],
        ['id'=>'mys-004','ime'=>'Čaj ayurveda detox','opis'=>'Mešanica 12 ajurvedskih zelišč za globinsko čiščenje in ravnovesje dosh. Brez kofeina.','opis_kratek'=>'50g, 12 zelišč','cena'=>16.90,'kategorija'=>'caji','zalogo'=>40,'aktivno'=>true,'ikona'=>'🍵','znacke'=>['organsko','priljubljen'],'teza'=>'50g'],
        ['id'=>'mys-005','ime'=>'Sojeva sveča pačuli & cedrovina','opis'=>'Ročno lita sojeva sveča z eteričnimi olji pačulija in cedrovine. Gori 45+ ur.','opis_kratek'=>'180g, čas gorenja 45h','cena'=>22.50,'kategorija'=>'svece','zalogo'=>18,'aktivno'=>true,'ikona'=>'🕯️','znacke'=>['ročna_dela','veganska'],'teza'=>'180g'],
        ['id'=>'mys-006','ime'=>'Frankincenz Hojari superior','opis'=>'Redke smole omanskega frankincenza iz prvorazrednih dreves. Čistilni in dvigajoč vonj.','opis_kratek'=>'Premium Hojari razred','cena'=>45.00,'kategorija'=>'kadilo','zalogo'=>8,'aktivno'=>true,'ikona'=>'✨','znacke'=>['premium','redko'],'teza'=>'30g'],
        ['id'=>'mys-007','ime'=>'Masažno olje ashwagandha','opis'=>'Ajurvedsko masažno olje z izvlečkom ashwagandhe v bazi sezamovega olja. Zemlja in mir.','opis_kratek'=>'100ml, brez parabenov','cena'=>29.90,'kategorija'=>'nega','zalogo'=>22,'aktivno'=>true,'ikona'=>'🌿','znacke'=>['organsko','ajurveda'],'teza'=>'100ml'],
        ['id'=>'mys-008','ime'=>'Tibetanska zveneča skleda B','opis'=>'Ročno kovana tibetanska zveneča skleda nota B. Premer 18cm, vključno s paličico in blazinico.','opis_kratek'=>'Ø18cm, nota B, ročno','cena'=>68.00,'kategorija'=>'instrumenti','zalogo'=>5,'aktivno'=>true,'ikona'=>'🔔','znacke'=>['ročna_dela','limitirano'],'teza'=>'420g'],
    ]);
}

function mystaia_seed_kategorije(): void {
    my_pisi(MY_KATEGORIJE, [
        ['id'=>'vse',           'ime'=>'Vse',               'ikona'=>'✦'],
        ['id'=>'eterična_olja', 'ime'=>'Eterična olja',     'ikona'=>'🌹'],
        ['id'=>'kadilo',        'ime'=>'Kadilo & smole',     'ikona'=>'🪔'],
        ['id'=>'kristali',      'ime'=>'Kristali',           'ikona'=>'💎'],
        ['id'=>'caji',          'ime'=>'Čaji & zelišča',     'ikona'=>'🍵'],
        ['id'=>'svece',         'ime'=>'Sveče',              'ikona'=>'🕯️'],
        ['id'=>'nega',          'ime'=>'Nega telesa',        'ikona'=>'🌿'],
        ['id'=>'instrumenti',   'ime'=>'Instrumenti',        'ikona'=>'🔔'],
    ]);
}

function mystaia_seed_nastavitve(): void {
    my_pisi(MY_NASTAVITVE, [
        'ime_trgovine'  => 'Mystaia',
        'podnaslov'     => 'Aromatična svetišča za dušo',
        'email'         => 'mystaia@astramento.si',
        'dostava_cena'  => 4.90,
        'dostava_brezplacna_nad' => 49.00,
        'valuta'        => 'EUR',
        'statusni_napisi' => [
            'novo'        => 'Novo naročilo',
            'potrjeno'    => 'Potrjeno',
            'v_obdelavi'  => 'V obdelavi',
            'poslano'     => 'Poslano',
            'dostavljeno' => 'Dostavljeno',
            'stornirano'  => 'Stornirano',
        ],
    ]);
}
