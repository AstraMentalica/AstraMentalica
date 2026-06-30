<?php declare(strict_types=1);

// ── BRIDGE / ADMIN DOSTOP ──────────────────────────────────────────
$bridgeNajden = false;
foreach ([__DIR__ . '/Modul_Bridge/modul_bridge.php'] as $p) {
    if (file_exists($p)) {
        require_once $p;
        $bridgeNajden = true;
        break;
    }
}
if (!$bridgeNajden) {
    die("❌ Modul_Bridge ni najden.\n");
}

// Prijava (za HTTP dostop)
$trenutni = Modul_Bridge::uporabnik_pridobi();
$jeAdmin = ($trenutni['vloga'] ?? 0) >= 100;

// Izberi način: CLI ali HTTP
$jeCli = (PHP_SAPI === 'cli');
if (!$jeCli && !$jeAdmin) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'napaka', 'sporocilo' => 'Dostop zavrnjen — potrebuješ admin vlogo']);
    exit;
}

// Ustvari mapo zlogov, če še ne obstaja
function zlog(string $msg): void {
    $f = __DIR__ . '/cache/generator_log.txt';
    @file_put_contents($f, date('c') . ' ' . $msg . "\n", FILE_APPEND);
}

// Normaliziraj ime modula v ID
function id_za_ime(string $ime): string {
    return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $ime));
}

// Poišči modul_bridge vstavek za datoteko modul.php
function bridge_vstavek(string $relativnaPot = '/../../Modul_Bridge/modul_bridge.php'): string {
    $pot = '__DIR__ . \'' . $relativnaPot . '\'';
    return '<?php' . "\n" .
    'declare(strict_types=1);' . "\n" .
    '$h=[' . $pot . ']; $f=false;' . "\n" .
    'foreach($h as $p){if(file_exists($p)){require_once $p; $f=true; break;}}' . "\n" .
    'if(!$f){header("Content-Type: application/json"); echo json_encode(["e"=>"Bridge"]); exit;}' . "\n";
}

// Ustvari enostaven modul.php za podane podatke
function ustvari_modul_php(string $ime, string $id, string $kategorija): string {
    $a = 'modul_' . $id . '_a';
    $b = '_' . $id . '_i';
    $c = '_' . $id . '_d';
    $imeEsc = addslashes($ime);
    $idEsc = addslashes($id);
    return bridge_vstavek() .
    'if(!function_exists("ru")){function ru($d,$s=""){return["st"=>"ok","c"=>200,"sp"=>$s,"v"=>$d];} function er($m,$k=400){return["st"=>"err","c"=>$k,"sp"=>$m,"v"=>[]];}}' . "\n" .
    'function ' . $a . '($a,$d=[]){if(!Modul_Bridge::vloga_preveri("S0")) return er("Zaprto",403); return match($a){"info"=>' . $b . '($d), "domov"=>' . $c . '($d), default=>er("?",400)};}' . "\n" .
    'function ' . $b . '($d){$u=Modul_Bridge::uporabnik_pridobi(); return ru(["n"=>"' . $imeEsc . '","i"=>"' . $idEsc . '","v"=>"1.0","k"=>"' . $idEsc . '","o"=>"' . $imeEsc . '","u"=>($u["ime"]??"Gost"), "opis"=>"Modul ' . $imeEsc . ' — kategorija ' . addslashes($kategorija) . '."], "i");}' . "\n" .
    'function ' . $c . '($d){return ru(["s"=>"Pozdravljen v modulu ' . $imeEsc . '!","t"=>time(),"i"=>"' . $idEsc . '"], "d");}' . "\n" .
    'if(basename($_SERVER["SCRIPT_FILENAME"]??"")==="modul.php" && !defined("SISTEM_OBSTAJA")){$q=$_REQUEST["akcija"]??"domov"; $o=' . $a . '($q,$_REQUEST); header("Content-Type: application/json; charset=utf-8"); echo json_encode($o,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);}' . "\n";
}

// ── SHEME ZA MANIFEST / API / IZHOD ────────────────────────────────
function shema_manifest(string $ime, string $id, string $kategorija, string $opis = ''): array {
    if ($opis === '') {
        $opis = "Modul $ime — kategorija $kategorija.";
    }
    return [
        '_id' => $id,
        '_verzija' => '1.0.0',
        'modul' => [
            'id' => $id,
            'ime' => $ime,
            'tip' => 'zbiralec',
            'nivo' => 1,
            'verzija' => '1.0.0',
            'aktiviran' => true,
            'vstopna' => 'modul.php',
            'opis' => $opis,
            'status' => 'raz',
            'demo' => false,
            'zacasen' => false,
        ],
        'dostop' => [
            'minimalna_vloga' => 'S0',
            'plan' => 'osnova',
            'javno_vidno' => true,
            'placljivo' => false,
            'otroski' => false,
            'vidnost' => 'vsi',
            'dovoljenja' => ['branje'],
        ],
        'cache' => [
            'omogocen' => true,
            'ttl' => 3600,
        ],
        'ui' => [
            'ima_prikaz' => true,
            'ikona' => '📖',
            'barva' => '#8bf',
            'kategorija' => $kategorija,
            'dovoljene_postavitve' => ['standard'],
            'tags' => [$id, $kategorija],
            'jeziki' => ['sl'],
        ],
        'izvajanje' => [
            'tip' => 'ui',
            'api_only' => false,
            'interval' => null,
            'ob_zagonu' => false,
            'prioriteta' => 50,
            'bootstrap' => null,
        ],
        'migracije' => [
            'obstajajo' => false,
            'zadnja' => null,
        ],
        'integriteta' => [
            'checksum' => null,
        ],
        'log' => [
            'omogocen' => true,
            'nivo' => 'info',
        ],
        'cas' => [
            'ustvarjen' => date('c'),
            'posodobljen' => date('c'),
            'zadnji_zagon' => null,
        ],
    ];
}

function shema_api(string $id): array {
    return [
        '_id' => $id,
        'poti' => [
            ['akcija' => 'info', 'metoda' => 'GET', 'opis' => 'Informacije o modulu'],
            ['akcija' => 'domov', 'metoda' => 'GET', 'opis' => 'Osnovni prikaz modula'],
        ],
    ];
}

function shema_izhod(string $id): array {
    return [
        '_id' => $id,
        'odvisnosti' => [],
        'vtičniki' => [],
        'vhodna_vmesnika' => [
            'info' => ['opis' => 'Vrne osnovne informacije o modulu'],
            'domov' => ['opis' => 'Uvodno zaslon modula'],
        ],
    ];
}

// ── KATEGORIJE ──────────────────────────────────────────────────────
$root = __DIR__;
$moduliDir = is_dir($root . '/MODULI') ? $root . '/MODULI' : $root;

$privzeteKategorije = [
    'Antika',
    'EvropaSlovani',
    'EvropaKelti',
    'EvropaNordija',
    'HebrejskaMistika',
    'Indija',
    'Japonska',
    'Kitajska',
    'Koreja',
    'AmerikaMezoamerika',
    'AmerikaAmazonija',
    'Samanizem',
    'Univerzalno',
    'Znanost',
    'Prihodnost',
];

$kategorije = [];
foreach (glob($moduliDir . '/*', GLOB_ONLYDIR) as $dir) {
    $ime = basename($dir);
    if ($ime === 'Modul_Bridge' || $ime === 'PWA manifesti' || $ime === 'MODULI iz PROJEKTA') {
        continue;
    }
    $kategorije[] = $ime;
}
if (empty($kategorije)) {
    $kategorije = $privzeteKategorije;
}

// ── CLI / HTTP LOGIKA ───────────────────────────────────────────────
if ($jeCli) {
    $ukaz = $argv[1] ?? 'pomoč';
    switch ($ukaz) {
        case 'pomoč':
        case 'help':
        case '--help':
        case '-h':
            echo "=== 🛠️ GENERATOR MODULOV — ADMIN ORODJE ===\n\n";
            echo "Upotreba: php generator_modul_v2.php <ukaz> [parametri]\n\n";
            echo "Ukazi:\n";
            echo "  kategorije                              Prikaže vse kategorije\n";
            echo "  ustvari-kategorijo <ime>                Ustvari novo kategorijo\n";
            echo "  ustvari-modul <ime> <kategorija> [opis] Ustvari nov modul\n";
            echo "  sheme                                   Prikaže sheme manifest/api/izhod\n";
            echo "  popravi-manifeste                       Popravi vse manifeste\n";
            echo "  dodaj-modul-php                         Dodaj manjkajoče modul.php\n";
            echo "  seznam-modulov [kategorija]             Seznam modulov\n";
            echo "  pomoč                                   Ta zaslon\n\n";
            echo "Primer:\n";
            echo "  php generator_modul_v2.php ustvari-modul \"Novi Modul\" Antika\n";
            echo "  php generator_modul_v2.php ustvari-kategorijo \"MojaKategorija\"\n";
            exit(0);

        case 'kategorije':
            echo "=== 📂 KATEGORIJE ===\n\n";
            foreach ($kategorije as $kat) {
                $st = count(glob($moduliDir . '/' . $kat . '/*', GLOB_ONLYDIR));
                echo "  📁 $kat ($st modulov)\n";
            }
            echo "\n";
            exit(0);

        case 'ustvari-kategorijo':
            $nova = $argv[2] ?? null;
            if (!$nova) {
                die("❌ Uporaba: php generator_modul_v2.php ustvari-kategorijo <ime>\n");
            }
            $novaPot = $moduliDir . '/' . $nova;
            if (is_dir($novaPot)) {
                die("⚠️ Kategorija že obstaja: $nova\n");
            }
            if (!mkdir($novaPot, 0755, true)) {
                die("❌ Ni bilo mogoče ustvariti mape: $novaPot\n");
            }
            foreach (['cache', 'temp', 'podatki'] as $pod) {
                mkdir($novaPot . '/' . $pod, 0755, true);
                file_put_contents($novaPot . '/' . $pod . '/.gitkeep', '');
            }
            echo "✅ Kategorija ustvarjena: $nova\n   $novaPot\n";
            exit(0);

        case 'ustvari-modul':
            $ime = trim($argv[2] ?? '');
            $kat = trim($argv[3] ?? '');
            $opis = trim($argv[4] ?? '');
            if ($ime === '' || $kat === '') {
                die("❌ Uporaba: php generator_modul_v2.php ustvari-modul <ime> <kategorija> [opis]\n");
            }
            $katPot = $moduliDir . '/' . $kat;
            if (!is_dir($katPot)) {
                die("❌ Kategorija ne obstaja: $kat\n");
            }
            $id = id_za_ime($ime);
            $modulPot = $katPot . '/' . $ime;
            if (is_dir($modulPot)) {
                die("⚠️ Modul že obstaja: $ime\n");
            }
            if (!mkdir($modulPot, 0755, true)) {
                die("❌ Ni bilo mogoče ustvariti mape: $modulPot\n");
            }
            foreach (['cache', 'temp'] as $pod) {
                mkdir($modulPot . '/' . $pod, 0755, true);
                file_put_contents($modulPot . '/' . $pod . '/.gitkeep', '');
            }
            mkdir($modulPot . '/podatki', 0755, true);

            file_put_contents($modulPot . '/modul.php', ustvari_modul_php($ime, $id, $kat));
            file_put_contents($modulPot . '/podatki/manifest.json', json_encode(shema_manifest($ime, $id, $kat, $opis), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($modulPot . '/podatki/api.json', json_encode(shema_api($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($modulPot . '/podatki/izhod.json', json_encode(shema_izhod($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($modulPot . '/podatki/modul.md', "# $ime\n\nModul v kategoriji $kat.\n\n## Opis\n\n" . ($opis ?: 'Dodaj opis modula.') . "\n\n## Značilnosti\n\n- Značilnost 1\n- Značilnost 2\n");

            echo "✅ Modul ustvarjen: $ime ($id)\n   Kategorija: $kat\n   Pot: $modulPot\n";
            echo "   Datoteke: modul.php, manifest.json, api.json, izhod.json, modul.md\n";
            exit(0);

        case 'sheme':
            echo "=== 📋 SHEME ZA MANIFEST / API / IZHOD ===\n\n";
            echo "--- MANIFEST ---\n";
            echo json_encode(shema_manifest('Primer', 'primer', 'Univerzalno', ' primer '), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            echo "--- API ---\n";
            echo json_encode(shema_api('primer'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            echo "--- IZHOD ---\n";
            echo json_encode(shema_izhod('primer'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            exit(0);

        case 'popravi-manifeste':
            $popravljeni = 0;
            foreach ($kategorije as $kat) {
                $katPot = $moduliDir . '/' . $kat;
                if (!is_dir($katPot)) continue;
                foreach (glob($katPot . '/*', GLOB_ONLYDIR) as $mp) {
                    $ime = basename($mp);
                    $id = id_za_ime($ime);
                    $mf = $mp . '/podatki/manifest.json';
                    if (!file_exists($mf)) continue;
                    $j = json_decode(file_get_contents($mf), true);
                    if (!$j) {
                        echo "⚠️ Napačen JSON: $kat/$ime\n";
                        continue;
                    }
                    $n = array_merge(shema_manifest($ime, $id, $kat, $j['modul']['opis'] ?? ''), $j);
                    if (file_put_contents($mf, json_encode($n, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                        echo "❌ Napaka pri pisanju: $kat/$ime\n";
                        continue;
                    }
                    echo "✅ Manifest popravljen: $kat/$ime\n";
                    $popravljeni++;
                }
            }
            echo "\n=== POPRAVLJENO: $popravljeni manifestov ===\n";
            zlog("POPRAVLJENI MANIFESTI: $popravljeni");
            exit(0);

        case 'dodaj-modul-php':
            $dodani = 0;
            $kopirani = 0;
            $arhiv = $root . '/MODULI iz PROJEKTA';
            foreach ($kategorije as $kat) {
                $katPot = $moduliDir . '/' . $kat;
                if (!is_dir($katPot)) continue;
                foreach (glob($katPot . '/*', GLOB_ONLYDIR) as $mp) {
                    $ime = basename($mp);
                    $id = id_za_ime($ime);
                    $ph = $mp . '/modul.php';
                    if (file_exists($ph)) continue;
                    $mat = null;
                    foreach ([$arhiv . "/$ime/modul.php", $arhiv . "/$ime/modul_$id.php", $katPot . "/$ime/modul.php"] as $s) {
                        if (file_exists($s)) {
                            $mat = file_get_contents($s);
                            break;
                        }
                    }
                    if ($mat) {
                        $mat = str_replace(
                            ["__DIR__ . '/../Modul_Bridge/modul_bridge.php'", "__DIR__ . '/../../Modul_Bridge/modul_bridge.php'"],
                            "__DIR__ . '/../../Modul_Bridge/modul_bridge.php'",
                            $mat
                        );
                        file_put_contents($ph, $mat);
                        echo "📋 KOPIRAN: $kat/$ime\n";
                        $kopirani++;
                    } else {
                        file_put_contents($ph, ustvari_modul_php($ime, $id, $kat));
                        echo "🛠️ GENERIRAN: $kat/$ime\n";
                        $dodani++;
                    }
                }
            }
            echo "\n=== DODANO: $dodani | KOPIRANIH: $kopirani ===\n";
            zlog("DODANI MODULI PHP: $dodani | KOPIRANI: $kopirani");
            exit(0);

        case 'seznam-modulov':
            $filter = $argv[2] ?? null;
            echo "=== 📚 SEZNAM MODULOV ===\n\n";
            if ($filter) {
                $katPot = $moduliDir . '/' . $filter;
                if (!is_dir($katPot)) {
                    die("❌ Kategorija ne obstaja: $filter\n");
                }
                echo "Kategorija: $filter\n\n";
                foreach (glob($katPot . '/*', GLOB_ONLYDIR) as $mp) {
                    $ime = basename($mp);
                    $id = id_za_ime($ime);
                    $ph = $mp . '/modul.php';
                    $mf = $mp . '/podatki/manifest.json';
                    $oz = '';
                    if (!file_exists($ph)) $oz .= ' [BREZ modul.php]';
                    if (!file_exists($mf)) $oz .= ' [BREZ manifest]';
                    echo "  • $ime ($id)$oz\n";
                }
            } else {
                foreach ($kategorije as $kat) {
                    $st = count(glob($moduliDir . '/' . $kat . '/*', GLOB_ONLYDIR));
                    echo "📁 $kat ($st modulov)\n";
                    foreach (glob($moduliDir . '/' . $kat . '/*', GLOB_ONLYDIR) as $mp) {
                        $ime = basename($mp);
                        $id = id_za_ime($ime);
                        $ph = $mp . '/modul.php';
                        $mf = $mp . '/podatki/manifest.json';
                        $oz = '';
                        if (!file_exists($ph)) $oz .= ' ⚠️';
                        if (!file_exists($mf)) $oz .= ' ❌';
                        echo "    • $ime ($id)$oz\n";
                    }
                    echo "\n";
                }
            }
            exit(0);

        default:
            echo "❌ Neznan ukaz: $ukaz\n";
            echo "Zagon: php generator_modul_v2.php pomoč\n";
            exit(1);
    }
}

// ── HTTP OBDELAVA (CLI) ───────────────────────────────────────────
if (!$jeCli) {
    header('Content-Type: application/json');
    $vnos = json_decode(file_get_contents('php://input'), true) ?: [];
    $akcija = strtolower(trim($vnos['akcija'] ?? ''));
    unset($vnos['akcija']);

    switch ($akcija) {
        case 'kategorije':
            echo json_encode(odziv_uspeh(['kategorije' => $kategorije], 'Seznam kategorij'));
            break;

        case 'ustvari-kategorijo':
            $ime = trim($vnos['ime'] ?? '');
            if ($ime === '') {
                echo json_encode(odziv_napaka('Manjka ime kategorije'));
                break;
            }
            $pot = $moduliDir . '/' . $ime;
            if (is_dir($pot)) {
                echo json_encode(odziv_napaka('Kategorija že obstaja'));
                break;
            }
            if (!mkdir($pot, 0755, true)) {
                echo json_encode(odziv_napaka('Ni bilo mogoče ustvariti mape'));
                break;
            }
            foreach (['cache', 'temp', 'podatki'] as $pod) {
                mkdir($pot . '/' . $pod, 0755, true);
                file_put_contents($pot . '/' . $pod . '/.gitkeep', '');
            }
            $kategorije[] = $ime;
            zlog("USTVARJENA KATEGORIJA: $ime");
            echo json_encode(odziv_uspeh(['kategorija' => $ime], 'Kategorija ustvarjena'));
            break;

        case 'ustvari-modul':
            $ime = trim($vnos['ime'] ?? '');
            $kat = trim($vnos['kategorija'] ?? '');
            $opis = trim($vnos['opis'] ?? '');
            if ($ime === '' || $kat === '') {
                echo json_encode(odziv_napaka('Manjkajo obvezni podatki: ime, kategorija'));
                break;
            }
            $katPot = $moduliDir . '/' . $kat;
            if (!is_dir($katPot)) {
                echo json_encode(odziv_napaka('Kategorija ne obstaja'));
                break;
            }
            $id = id_za_ime($ime);
            $modulPot = $katPot . '/' . $ime;
            if (is_dir($modulPot)) {
                echo json_encode(odziv_napaka('Modul že obstaja'));
                break;
            }
            if (!mkdir($modulPot, 0755, true)) {
                echo json_encode(odziv_napaka('Ni bilo mogoče ustvariti mape'));
                break;
            }
            foreach (['cache', 'temp'] as $pod) {
                mkdir($modulPot . '/' . $pod, 0755, true);
                file_put_contents($modulPot . '/' . $pod . '/.gitkeep', '');
            }
            mkdir($modulPot . '/podatki', 0755, true);

            file_put_contents($modulPot . '/modul.php', ustvari_modul_php($ime, $id, $kat));
            file_put_contents($modulPot . '/podatki/manifest.json', json_encode(shema_manifest($ime, $id, $kat, $opis), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($modulPot . '/podatki/api.json', json_encode(shema_api($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($modulPot . '/podatki/izhod.json', json_encode(shema_izhod($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($modulPot . '/podatki/modul.md', "# $ime\n\nModul v kategoriji $kat.\n\n## Opis\n\n" . ($opis ?: 'Dodaj opis modula.') . "\n");

            zlog("USTVARJEN MODUL (HTTP): $kat/$ime ($id)");
            echo json_encode(odziv_uspeh([
                'modul' => $ime,
                'id' => $id,
                'kategorija' => $kat,
                'pot' => $modulPot,
            ], 'Modul ustvarjen'));
            break;

        case 'sheme':
            echo json_encode(odziv_uspeh([
                'manifest' => shema_manifest('Primer', 'primer', 'Univerzalno'),
                'api' => shema_api('primer'),
                'izhod' => shema_izhod('primer'),
            ], 'Sheme za module'));
            break;

        case 'seznam-modulov': {
            $filter = trim($vnos['kategorija'] ?? '');
            if ($filter !== '') {
                $katPot = $moduliDir . '/' . $filter;
                if (!is_dir($katPot)) {
                    echo json_encode(odziv_napaka('Kategorija ne obstaja'));
                    break;
                }
                $rez = [];
                foreach (glob($katPot . '/*', GLOB_ONLYDIR) as $mp) {
                    $ime = basename($mp);
                    $id = id_za_ime($ime);
                    $ph = $mp . '/modul.php';
                    $mf = $mp . '/podatki/manifest.json';
                    $rez[] = [
                        'ime' => $ime,
                        'id' => $id,
                        'ima_modul_php' => file_exists($ph),
                        'ima_manifest' => file_exists($mf),
                    ];
                }
                echo json_encode(odziv_uspeh(['kategorija' => $filter, 'moduli' => $rez], 'Seznam modulov'));
            } else {
                $vse = [];
                foreach ($kategorije as $kat) {
                    $st = count(glob($moduliDir . '/' . $kat . '/*', GLOB_ONLYDIR));
                    $vse[] = ['kategorija' => $kat, 'st_modulov' => $st];
                }
                echo json_encode(odziv_uspeh(['kategorije' => $vse], 'Seznam kategorij'));
            }
            break;
        }

        default:
            echo json_encode(odziv_napaka('Neznana akcija', 400));
            break;
    }
    exit;
}
