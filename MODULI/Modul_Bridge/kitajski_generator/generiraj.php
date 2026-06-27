<?php
declare(strict_types=1);

$datum = '2026-06-23T12:00:00Z';
$izhod = __DIR__ . '/izhod';

function jlep(array $d): string {
    return json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
}

// ─────────────────────────────────────────────────────────────────
// DEFINICIJA VSEH MODULOV
// ─────────────────────────────────────────────────────────────────
$moduli = [

    // ── NEBO / ASTROLOGIJA ────────────────────────────────────────
    'Bazi' => [
        'id' => 'bazi',
        'ime' => 'Bazi',
        'ime_izvirno' => '八字',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'Bazi (八字) — štirje stebri usode. Kitajska natalna astrologija na osnovi leta, meseca, dneva in ure rojstva. Izračun desetletnih in letnih period.',
        'ikona' => '🏮',
        'barva' => '#f87171',
        'kategorija' => 'NEBO',
        'plan' => 'razsirjeno',
        'minimalna_vloga' => 'S1',
        'ttl' => 86400,
        'prioriteta' => 20,
        'poti' => ['/bazi/stebri', '/bazi/period', '/bazi/kompatibilnost', '/bazi/letna'],
        'potrebuje' => ['datum_rojstva', 'ura_rojstva', 'spol'],
        'opcijsko' => ['kraj_rojstva', 'ime'],
        'oddaja' => ['bazi.stebri.izracunani'],
        'bere_iz' => [],
        'tags' => ['bazi', 'kitajska-astrologija', 'stebri-usode', '八字'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Ziwei' => [
        'id' => 'ziwei',
        'ime' => 'Ziwei',
        'ime_izvirno' => '紫微斗數',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'Ziwei Dou Shu (紫微斗數) — purpurna zvezda. Cesarska kitajska astrologija z 12 hišami in 114 zvezdami. Natančna analiza osebnosti, kariere, zdravja in odnosov.',
        'ikona' => '⭐',
        'barva' => '#c084fc',
        'kategorija' => 'NEBO',
        'plan' => 'razsirjeno',
        'minimalna_vloga' => 'S1',
        'ttl' => 86400,
        'prioriteta' => 20,
        'poti' => ['/ziwei/horoskop', '/ziwei/hiše', '/ziwei/zvezde', '/ziwei/period'],
        'potrebuje' => ['datum_rojstva', 'ura_rojstva', 'spol'],
        'opcijsko' => ['ime'],
        'oddaja' => ['ziwei.horoskop.generiran'],
        'bere_iz' => [],
        'tags' => ['ziwei', 'purpurna-zvezda', 'cesarska-astrologija', '紫微'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Unmei' => [
        'id' => 'unmei',
        'ime' => 'Unmei',
        'ime_izvirno' => '運命',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Unmei (運命) — japonska usoda in karma. Sistem životnih poti, preteklih življenj in karminskih vozlov po japonski duhovni tradiciji. Vključuje eto (干支) — japonski zodiak.',
        'ikona' => '🎋',
        'barva' => '#86efac',
        'kategorija' => 'NEBO',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/unmei/pot', '/unmei/eto', '/unmei/karma', '/unmei/letni'],
        'potrebuje' => ['datum_rojstva'],
        'opcijsko' => ['ime', 'spol'],
        'oddaja' => ['unmei.pot.izracunana'],
        'bere_iz' => [],
        'tags' => ['unmei', 'japonska-usoda', 'karma', 'eto', '運命'],
        'jeziki' => ['sl', 'en', 'ja', 'zh'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino'],
    ],

    // ── PROSTOR / ENERGIJA ────────────────────────────────────────
    'Fengshui' => [
        'id' => 'fengshui',
        'ime' => 'Fengshui',
        'ime_izvirno' => '風水',
        'tip' => 'izvajalec',
        'nivo' => 3,
        'opis' => 'Feng Shui (風水) — veter in voda. Analiza prostora, smeri in toka qi energije. Bagua mreža, kua število, ugodne in neugodne smeri za dom, pisarno in spletni prostor.',
        'ikona' => '🧭',
        'barva' => '#34d399',
        'kategorija' => 'PROSTOR',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/fengshui/analiza', '/fengshui/bagua', '/fengshui/kua', '/fengshui/ugodne-smeri'],
        'potrebuje' => [],
        'opcijsko' => ['datum_rojstva', 'tloris', 'smer_vhoda'],
        'oddaja' => ['fengshui.analiza.opravljena'],
        'bere_iz' => ['PODATKI/moduli/bazi/'],
        'tags' => ['fengshui', 'bagua', 'qi', 'prostor', '風水'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Wuxing' => [
        'id' => 'wuxing',
        'ime' => 'Wuxing',
        'ime_izvirno' => '五行',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Wu Xing (五行) — pet elementov: les, ogenj, zemlja, kovina, voda. Temelj kitajske kozmologije. Cikli ustvarjanja in uničevanja, osebnostni profil po elementih, sezonska priporočila.',
        'ikona' => '☯️',
        'barva' => '#fbbf24',
        'kategorija' => 'PROSTOR',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/wuxing/elementi', '/wuxing/profil', '/wuxing/cikel', '/wuxing/sezona'],
        'potrebuje' => [],
        'opcijsko' => ['datum_rojstva', 'element'],
        'oddaja' => [],
        'bere_iz' => [],
        'tags' => ['wuxing', 'pet-elementov', 'kitajska-kozmologija', '五行'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['branje'],
    ],

    'Reiki' => [
        'id' => 'reiki',
        'ime' => 'Reiki',
        'ime_izvirno' => '靈氣',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Reiki (靈氣) — duhovna energija. Japonska tehnika energijskega zdravljenja. Simboli Reiki, distance healing, chakra uravnavanje po japonski tradiciji. Vodeni seansi in meditacije.',
        'ikona' => '✋',
        'barva' => '#a5f3fc',
        'kategorija' => 'PROSTOR',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/reiki/simboli', '/reiki/seansa', '/reiki/chakre', '/reiki/distance'],
        'potrebuje' => [],
        'opcijsko' => ['namen', 'trajanje', 'chakra'],
        'oddaja' => ['reiki.seansa.opravljena'],
        'bere_iz' => [],
        'tags' => ['reiki', 'energijsko-zdravljenje', 'simboli', '靈氣'],
        'jeziki' => ['sl', 'en', 'ja', 'zh'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    // ── DIVINACIJA ────────────────────────────────────────────────
    'Yijing' => [
        'id' => 'yijing',
        'ime' => 'Yijing',
        'ime_izvirno' => '易經',
        'tip' => 'izvajalec',
        'nivo' => 3,
        'opis' => 'Yi Jing (易經) — Knjiga sprememb. 64 heksagramov, 384 črt. Starodavna kitajska divinacija in filozofija. Metanje kovancev, yarrow stick metoda, interpretacija sprememb.',
        'ikona' => '☰',
        'barva' => '#f59e0b',
        'kategorija' => 'DIVINACIJA',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/yijing/vprasaj', '/yijing/heksagram', '/yijing/zgodovina', '/yijing/knjiznica'],
        'potrebuje' => ['vprasanje'],
        'opcijsko' => ['metoda', 'datum'],
        'oddaja' => ['yijing.heksagram.generiran'],
        'bere_iz' => [],
        'tags' => ['yijing', 'i-ching', 'heksagrami', 'divinacija', '易經'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Liuren' => [
        'id' => 'liuren',
        'ime' => 'Liuren',
        'ime_izvirno' => '六壬',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'Liu Ren (六壬) — šest nebeških ploščic. Ena najstarejših kitajskih divinacijskih metod. Dvanajst nebeških dostojanstvenikov, analiza časa in prostora za odločitve.',
        'ikona' => '🔯',
        'barva' => '#818cf8',
        'kategorija' => 'DIVINACIJA',
        'plan' => 'razsirjeno',
        'minimalna_vloga' => 'S1',
        'ttl' => 3600,
        'prioriteta' => 15,
        'poti' => ['/liuren/vprasaj', '/liuren/ploščice', '/liuren/analiza'],
        'potrebuje' => ['vprasanje', 'datum', 'ura'],
        'opcijsko' => ['kraj'],
        'oddaja' => ['liuren.analiza.opravljena'],
        'bere_iz' => [],
        'tags' => ['liuren', 'kitajska-divinacija', 'nebeške-ploščice', '六壬'],
        'jeziki' => ['sl', 'en', 'zh'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Kijou' => [
        'id' => 'kijou',
        'ime' => 'Kijou',
        'ime_izvirno' => '奇門遁甲',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'Qi Men Dun Jia (奇門遁甲) — čudovita vrata skrite rose. Napredna kitajska divinacija za strateške odločitve. Analiza časa, prostora in smeri za optimalne akcije.',
        'ikona' => '🚪',
        'barva' => '#6366f1',
        'kategorija' => 'DIVINACIJA',
        'plan' => 'razsirjeno',
        'minimalna_vloga' => 'S2',
        'ttl' => 3600,
        'prioriteta' => 15,
        'poti' => ['/kijou/chart', '/kijou/analiza', '/kijou/vrata'],
        'potrebuje' => ['datum', 'ura', 'vprasanje'],
        'opcijsko' => ['kraj', 'namen'],
        'oddaja' => ['kijou.chart.generiran'],
        'bere_iz' => [],
        'tags' => ['kijou', 'qimen', 'strategija', 'divinacija', '奇門遁甲'],
        'jeziki' => ['sl', 'en', 'zh'],
        'pwa' => false,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    // ── MITOLOGIJA ────────────────────────────────────────────────
    'Shenlong' => [
        'id' => 'shenlong',
        'ime' => 'Shenlong',
        'ime_izvirno' => '神龍',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Shen Long (神龍) — enciklopedija kitajskih in japonskih zmajev ter mitoloških bitij. Nebeški zmaji, zemeljski zmaji, feniksov, kilin, tengu, kitsune. Simbolika in legenda.',
        'ikona' => '🐉',
        'barva' => '#f97316',
        'kategorija' => 'MITOLOGIJA',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/shenlong/bitja', '/shenlong/zmaji', '/shenlong/iskanje', '/shenlong/simboli'],
        'potrebuje' => [],
        'opcijsko' => ['iskanje', 'tradicija'],
        'oddaja' => [],
        'bere_iz' => [],
        'tags' => ['shenlong', 'zmaji', 'mitologija', 'kitajska', '神龍'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['branje'],
    ],

    'Kami' => [
        'id' => 'kami',
        'ime' => 'Kami',
        'ime_izvirno' => '神',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Kami (神) — japonski duhovi. Shintoistični panteon: duhovi narave, prednikov, krajev, pojavov. Amaterasu, Susanoo, Izanagi, Inari in tisoči lokalnih kami. Rituali in molitve.',
        'ikona' => '⛩️',
        'barva' => '#e879f9',
        'kategorija' => 'MITOLOGIJA',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/kami/panteon', '/kami/iskanje', '/kami/ritual', '/kami/svetišča'],
        'potrebuje' => [],
        'opcijsko' => ['iskanje', 'kategorija'],
        'oddaja' => [],
        'bere_iz' => [],
        'tags' => ['kami', 'shinto', 'japonska-mitologija', '神'],
        'jeziki' => ['sl', 'en', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['branje'],
    ],

    'Bajixing' => [
        'id' => 'bajixing',
        'ime' => 'Bajixing',
        'ime_izvirno' => '八仙',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Ba Xian (八仙) — osem daoističnih nesmrtnikov. Arhetipski vodiči transformacije: Li Tieguai, He Xiangu, Zhongli Quan in drugi. Simboli, moči in zgodbe osmih nesmrtnikov.',
        'ikona' => '🧙',
        'barva' => '#fcd34d',
        'kategorija' => 'MITOLOGIJA',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/bajixing/nesmrtniki', '/bajixing/simboli', '/bajixing/zgodbe'],
        'potrebuje' => [],
        'opcijsko' => ['nesmrtnik'],
        'oddaja' => [],
        'bere_iz' => [],
        'tags' => ['bajixing', 'nesmrtniki', 'daoizem', '八仙'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW'],
        'pwa' => true,
        'dovoljenja' => ['branje'],
    ],

    // ── NOTRANJE PRAKSE ───────────────────────────────────────────
    'Neidan' => [
        'id' => 'neidan',
        'ime' => 'Neidan',
        'ime_izvirno' => '內丹',
        'tip' => 'zbiralec',
        'nivo' => 2,
        'opis' => 'Nei Dan (內丹) — notranja alkimija. Daoistična praksa transformacije jing, qi in shen energij. Meditativne tehnike, dihanje, vizualizacije za duhovno prebujenje.',
        'ikona' => '⚗️',
        'barva' => '#fb923c',
        'kategorija' => 'PRAKSE',
        'plan' => 'razsirjeno',
        'minimalna_vloga' => 'S1',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/neidan/prakse', '/neidan/energije', '/neidan/meditacija', '/neidan/napredek'],
        'potrebuje' => [],
        'opcijsko' => ['izkušenost', 'namen'],
        'oddaja' => ['neidan.praksa.opravljena'],
        'bere_iz' => [],
        'tags' => ['neidan', 'notranja-alkimija', 'daoizem', 'qi', '內丹'],
        'jeziki' => ['sl', 'en', 'zh'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Zazen' => [
        'id' => 'zazen',
        'ime' => 'Zazen',
        'ime_izvirno' => '坐禅',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Zazen (坐禅) — sedeča zen meditacija. Japonska budistična praksa tišine in prisotnosti. Vodene seje, timer, dnevnik uvida, koani za razmišljanje. Theravada in Mahayana pristopi.',
        'ikona' => '🧘',
        'barva' => '#94a3b8',
        'kategorija' => 'PRAKSE',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/zazen/seja', '/zazen/timer', '/zazen/koani', '/zazen/dnevnik'],
        'potrebuje' => [],
        'opcijsko' => ['trajanje', 'tradicija', 'izkušenost'],
        'oddaja' => ['zazen.seja.opravljena'],
        'bere_iz' => [],
        'tags' => ['zazen', 'zen', 'meditacija', 'budizem', '坐禅'],
        'jeziki' => ['sl', 'en', 'ja', 'zh'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Mushin' => [
        'id' => 'mushin',
        'ime' => 'Mushin',
        'ime_izvirno' => '無心',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Mushin (無心) — um brez misli. Japonska filozofija pretočnosti in prisotnosti brez ega. Vadbe za doseganje stanja brez-misli: gibanje, dih, fokus. Budo in zen vplivi.',
        'ikona' => '🌊',
        'barva' => '#7dd3fc',
        'kategorija' => 'PRAKSE',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/mushin/vadbe', '/mushin/stanje', '/mushin/dnevnik'],
        'potrebuje' => [],
        'opcijsko' => ['tehnika', 'trajanje'],
        'oddaja' => ['mushin.stanje.dosezeno'],
        'bere_iz' => [],
        'tags' => ['mushin', 'zen', 'pretočnost', 'um', '無心'],
        'jeziki' => ['sl', 'en', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    // ── NARAVA / CIKLI ────────────────────────────────────────────
    'Sekki' => [
        'id' => 'sekki',
        'ime' => 'Sekki',
        'ime_izvirno' => '節氣',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Jie Qi / Sekki (節氣) — 24 solarnih terminov kitajskega leta. Lichun, Qingming, Dongzhi... Vsak termin nosi svoja priporočila za prehrano, počitek, aktivnost in rituale.',
        'ikona' => '🌸',
        'barva' => '#f9a8d4',
        'kategorija' => 'NARAVA',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/sekki/trenutni', '/sekki/koledar', '/sekki/priporocila', '/sekki/ritual'],
        'potrebuje' => [],
        'opcijsko' => ['datum', 'lokacija'],
        'oddaja' => ['sekki.termin.sprememba'],
        'bere_iz' => [],
        'tags' => ['sekki', 'jieqi', '24-terminov', 'lunarni-koledar', '節氣'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['branje'],
    ],

    'Hanami' => [
        'id' => 'hanami',
        'ime' => 'Hanami',
        'ime_izvirno' => '花見',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Hanami (花見) — opazovanje cvetja. Japonski rituali letnih časov, praznovanje minljivosti narave. Cvetenje češenj, momiji, festivali. Wabi-sabi estetika in sezonski rituali.',
        'ikona' => '🌸',
        'barva' => '#fbcfe8',
        'kategorija' => 'NARAVA',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/hanami/sezona', '/hanami/festivali', '/hanami/ritual', '/hanami/cvetje'],
        'potrebuje' => [],
        'opcijsko' => ['datum', 'lokacija', 'cvetje'],
        'oddaja' => [],
        'bere_iz' => ['PODATKI/moduli/sekki/'],
        'tags' => ['hanami', 'sakura', 'japonska-narava', 'wabi-sabi', '花見'],
        'jeziki' => ['sl', 'en', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['branje'],
    ],

    'Wabi' => [
        'id' => 'wabi',
        'ime' => 'Wabi',
        'ime_izvirno' => '侘寂',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Wabi-Sabi (侘寂) — japonska estetika nepopolnosti in minljivosti. Filozofija lepote v preprostosti, starosti, nepopolnosti. Vodene refleksije, dnevnik zahvalnosti, estetske vaje.',
        'ikona' => '🍂',
        'barva' => '#d97706',
        'kategorija' => 'NARAVA',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/wabi/refleksija', '/wabi/filozofija', '/wabi/dnevnik', '/wabi/estetika'],
        'potrebuje' => [],
        'opcijsko' => ['tema'],
        'oddaja' => ['wabi.refleksija.zapisana'],
        'bere_iz' => [],
        'tags' => ['wabi', 'wabi-sabi', 'japonska-estetika', 'minljivost', '侘寂'],
        'jeziki' => ['sl', 'en', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    // ── ZDRAVJE / TELO ────────────────────────────────────────────
    'Kanpo' => [
        'id' => 'kanpo',
        'ime' => 'Kanpo',
        'ime_izvirno' => '漢方',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Kampo / Kanpo (漢方) — japonska zeliščna medicina. Razvita iz TCM, prilagojena japonski tradiciji. 200+ formul, zeliščni slovar, diagnostika po japonski medicinski tradiciji.',
        'ikona' => '🌿',
        'barva' => '#4ade80',
        'kategorija' => 'ZDRAVJE',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 86400,
        'prioriteta' => 10,
        'poti' => ['/kanpo/zelisca', '/kanpo/formule', '/kanpo/diagnostika', '/kanpo/iskanje'],
        'potrebuje' => [],
        'opcijsko' => ['simptom', 'zelisce', 'konstitucija'],
        'oddaja' => [],
        'bere_iz' => [],
        'tags' => ['kanpo', 'kampo', 'japonska-medicina', 'zelisca', '漢方'],
        'jeziki' => ['sl', 'en', 'ja', 'zh'],
        'pwa' => true,
        'dovoljenja' => ['branje'],
    ],

    'Shiatsu' => [
        'id' => 'shiatsu',
        'ime' => 'Shiatsu',
        'ime_izvirno' => '指圧',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Shiatsu (指圧) — pritisk s prsti. Japonska tehnika pritiskovnih točk na meridianih. Interaktivni atlas točk, vodene self-shiatsu sekvence, diagnostika po meridianih.',
        'ikona' => '👆',
        'barva' => '#f87171',
        'kategorija' => 'ZDRAVJE',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/shiatsu/točke', '/shiatsu/sekvence', '/shiatsu/meridiani', '/shiatsu/diagnostika'],
        'potrebuje' => [],
        'opcijsko' => ['simptom', 'meridian', 'točka'],
        'oddaja' => ['shiatsu.sekvenca.opravljena'],
        'bere_iz' => ['PODATKI/moduli/qivitalis/'],
        'tags' => ['shiatsu', 'pritiskovne-točke', 'meridiani', '指圧'],
        'jeziki' => ['sl', 'en', 'ja', 'zh'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'Makoto' => [
        'id' => 'makoto',
        'ime' => 'Makoto',
        'ime_izvirno' => '誠',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'opis' => 'Makoto (誠) — japonska iskrenost in celovitost kot duhovnost. Bushido vrednote, ikigai iskanje, hara hachi bu praksa. Osebni razvoj po japonski filozofiji življenja.',
        'ikona' => '🎯',
        'barva' => '#e2e8f0',
        'kategorija' => 'ZDRAVJE',
        'plan' => 'osnova',
        'minimalna_vloga' => 'S0',
        'ttl' => 3600,
        'prioriteta' => 10,
        'poti' => ['/makoto/ikigai', '/makoto/vrednote', '/makoto/dnevnik', '/makoto/praksa'],
        'potrebuje' => [],
        'opcijsko' => ['tema', 'vprasanje'],
        'oddaja' => ['makoto.dnevnik.zapisan'],
        'bere_iz' => [],
        'tags' => ['makoto', 'ikigai', 'bushido', 'japonska-filozofija', '誠'],
        'jeziki' => ['sl', 'en', 'ja'],
        'pwa' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    // ── PROFESIONALNI MODULI ──────────────────────────────────────
    'QiMapper' => [
        'id' => 'qimapper',
        'ime' => 'QiMapper',
        'ime_izvirno' => '氣圖',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'QiMapper (氣圖) — profesionalni kartograf qi energije. Združuje Feng Shui, Bazi in Wu Xing v vizualno energijsko karto prostora ali osebe. API za integracije, JSON izvoz, PDF poročila.',
        'ikona' => '🗺️',
        'barva' => '#22d3ee',
        'kategorija' => 'PROFESIONALNO',
        'plan' => 'pro',
        'minimalna_vloga' => 'S2',
        'ttl' => 3600,
        'prioriteta' => 50,
        'poti' => ['/qimapper/karta', '/qimapper/analiza', '/qimapper/izvoz', '/qimapper/api'],
        'potrebuje' => ['datum_rojstva'],
        'opcijsko' => ['tloris', 'smer_vhoda', 'format_izvoza'],
        'oddaja' => ['qimapper.karta.generirana'],
        'bere_iz' => ['PODATKI/moduli/bazi/', 'PODATKI/moduli/fengshui/', 'PODATKI/moduli/wuxing/'],
        'tags' => ['qimapper', 'profesionalno', 'api', 'qi-karta', '氣圖'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW', 'ja'],
        'pwa' => false,
        'api_only' => true,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'ChronoSync' => [
        'id' => 'chronosync',
        'ime' => 'ChronoSync',
        'ime_izvirno' => '時間同步',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'ChronoSync (時間同步) — sinhronizacija kozmičnih in zemeljskih ciklov. Združuje kitajski lunarni, solarni (Sekki), gregorijanski in vedski (Jyotir) koledar. Ugodni dnevi za akcije, opomniki.',
        'ikona' => '🕐',
        'barva' => '#a78bfa',
        'kategorija' => 'PROFESIONALNO',
        'plan' => 'pro',
        'minimalna_vloga' => 'S2',
        'ttl' => 900,
        'prioriteta' => 50,
        'poti' => ['/chronosync/danes', '/chronosync/teden', '/chronosync/ugodni', '/chronosync/api'],
        'potrebuje' => [],
        'opcijsko' => ['datum_rojstva', 'lokacija', 'namen'],
        'oddaja' => ['chronosync.ugodni_dan.oznacen'],
        'bere_iz' => ['PODATKI/moduli/sekki/', 'PODATKI/moduli/lunaris/', 'PODATKI/moduli/bazi/'],
        'tags' => ['chronosync', 'koledar', 'ugodni-dnevi', 'profesionalno', '時間同步'],
        'jeziki' => ['sl', 'en', 'zh', 'zh-TW', 'ja'],
        'pwa' => true,
        'api_only' => false,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'AuraMetrics' => [
        'id' => 'aurametrics',
        'ime' => 'AuraMetrics',
        'ime_izvirno' => '氣場測量',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'AuraMetrics (氣場測量) — merjenje energijskega polja. Kvantitativni model aure, čaker in meridianskega sistema. Vprašalniki, časovne meritve, trendi, primerjave. Profesionalni izvoz za terapevte.',
        'ikona' => '📊',
        'barva' => '#fb7185',
        'kategorija' => 'PROFESIONALNO',
        'plan' => 'pro',
        'minimalna_vloga' => 'S2',
        'ttl' => 3600,
        'prioriteta' => 50,
        'poti' => ['/aurametrics/merjenje', '/aurametrics/trend', '/aurametrics/primerjava', '/aurametrics/izvoz'],
        'potrebuje' => [],
        'opcijsko' => ['obdobje', 'format_izvoza', 'tip_meritve'],
        'oddaja' => ['aurametrics.meritev.opravljena'],
        'bere_iz' => ['PODATKI/moduli/reiki/', 'PODATKI/moduli/wuxing/', 'PODATKI/moduli/qimapper/'],
        'tags' => ['aurametrics', 'merjenje', 'profesionalno', 'terapevti', '氣場測量'],
        'jeziki' => ['sl', 'en', 'zh', 'ja'],
        'pwa' => false,
        'api_only' => false,
        'dovoljenja' => ['brati_zgodovino', 'pisati_nastavitve'],
    ],

    'ZodiacAPI' => [
        'id' => 'zodiacapi',
        'ime' => 'ZodiacAPI',
        'ime_izvirno' => '星象API',
        'tip' => 'sestavljalec',
        'nivo' => 2,
        'opis' => 'ZodiacAPI (星象API) — enotni API za vse astrološke sisteme. Zahodna, vedska (Jyotir), kitajska (Bazi, Ziwei) in japonska (Unmei) astrologija v enem klicu. Za razvijalce in integracije.',
        'ikona' => '🔌',
        'barva' => '#6366f1',
        'kategorija' => 'PROFESIONALNO',
        'plan' => 'pro',
        'minimalna_vloga' => 'S2',
        'ttl' => 86400,
        'prioriteta' => 50,
        'poti' => ['/zodiacapi/v1/profil', '/zodiacapi/v1/kompatibilnost', '/zodiacapi/v1/forecast', '/zodiacapi/v1/docs'],
        'potrebuje' => ['datum_rojstva', 'sistem'],
        'opcijsko' => ['ura_rojstva', 'kraj_rojstva', 'format'],
        'oddaja' => [],
        'bere_iz' => ['PODATKI/moduli/bazi/', 'PODATKI/moduli/ziwei/', 'PODATKI/moduli/unmei/', 'PODATKI/moduli/stelaris/'],
        'tags' => ['zodiacapi', 'api', 'razvijalci', 'astrologija', '星象API'],
        'jeziki' => ['sl', 'en', 'zh', 'ja'],
        'pwa' => false,
        'api_only' => true,
        'dovoljenja' => ['branje'],
    ],
];

// ─────────────────────────────────────────────────────────────────
// GENERATOR
// ─────────────────────────────────────────────────────────────────
foreach ($moduli as $PascalIme => $m) {
    $id = $m['id'];
    $dir = "$izhod/$PascalIme/podatki";
    @mkdir($dir, 0755, true);

    $je_pwa   = $m['pwa'] ?? false;
    $api_only = $m['api_only'] ?? false;
    $jeziki   = $m['jeziki'] ?? ['sl', 'en'];
    $ima_zh   = in_array('zh', $jeziki) || in_array('zh-TW', $jeziki);

    // ── manifest.json ─────────────────────────────────────────────
    $manifest = [
        '_id'      => $id,
        '_verzija' => '1.0.0',
        'modul' => [
            'id'        => $id,
            'ime'       => $PascalIme,
            'ime_izvirno' => $m['ime_izvirno'],
            'tip'       => $m['tip'],
            'nivo'      => $m['nivo'],
            'verzija'   => '1.0.0',
            'aktiviran' => true,
            'vstopna'   => 'modul.php',
            'opis'      => $m['opis'],
            'status'    => 'razvoj',
            'demo'      => false,
            'zacasen'   => false,
        ],
        'dostop' => [
            'minimalna_vloga' => $m['minimalna_vloga'],
            'plan'            => $m['plan'],
            'javno_vidno'     => $m['minimalna_vloga'] === 'S0',
            'placljivo'       => in_array($m['plan'], ['pro', 'vip']),
            'otroski'         => false,
            'vidnost'         => $m['minimalna_vloga'] === 'S0' ? 'vsi' : 'clani',
            'dovoljenja'      => $m['dovoljenja'],
        ],
        'cache' => [
            'omogocen' => true,
            'ttl'      => $m['ttl'],
        ],
        'ui' => [
            'ima_prikaz'         => !$api_only,
            'ikona'              => $m['ikona'],
            'barva'              => $m['barva'],
            'kategorija'         => $m['kategorija'],
            'dovoljene_postavitve' => in_array($m['plan'], ['pro', 'vip']) ? ['pro'] : ['standard', 'pro'],
            'tags'               => $m['tags'],
            'jeziki'             => $jeziki,
            'pwa'                => $je_pwa,
            'pwa_orientacija'    => $je_pwa ? 'portrait' : null,
        ],
        'izvajanje' => [
            'tip'       => 'ui',
            'api_only'  => $api_only,
            'interval'  => null,
            'ob_zagonu' => false,
            'prioriteta' => $m['prioriteta'],
            'bootstrap' => null,
        ],
        'lokalizacija' => [
            'primarna'    => $ima_zh ? 'zh' : 'ja',
            'podprte'     => $jeziki,
            'rtl'         => false,
            'unicode_blok' => $ima_zh ? 'CJK' : 'Hiragana',
        ],
        'migracije'  => ['obstajajo' => false, 'zadnja' => null],
        'integriteta' => ['checksum' => null],
        'log' => ['omogocen' => true, 'nivo' => 'info'],
        'cas' => [
            'ustvarjen'    => $datum,
            'posodobljen'  => $datum,
            'zadnji_zagon' => null,
        ],
    ];
    file_put_contents("$dir/manifest.json", jlep($manifest));

    // ── api.json ──────────────────────────────────────────────────
    $poti = $m['poti'];
    $metode = [];
    foreach ($poti as $p) {
        $del = explode('/', trim($p, '/'));
        $metode[] = end($del);
    }
    $api = [
        '_id'     => $id,
        '_verzija' => '1.0.0',
        'kanali'  => $api_only ? ['api'] : ['api', 'pwa'],
        'vstop'   => ['web' => 'modul.php'],
        'javne_metode' => $metode,
        'http_poti'    => $poti,
    ];
    file_put_contents("$dir/api.json", jlep($api));

    // ── izhod.json ────────────────────────────────────────────────
    $izhod_json = [
        '_id'     => $id,
        '_verzija' => '1.0.0',
        'vhod' => [
            'potrebuje'  => $m['potrebuje'],
            'opcijsko'   => $m['opcijsko'],
            'vir'        => 'uporabnik',
            'validacija' => null,
            'omejitve'   => ['max_velikost' => null],
        ],
        'izhod' => [
            'format'  => 'json',
            'pise_v'  => ["PODATKI/moduli/$id/"],
        ],
        'odvisnosti' => [
            'bere_iz'          => $m['bere_iz'],
            'prepovedani_moduli' => [],
            'ne_pozna'         => 'vse_ostalo',
            'kompatibilnost'   => ['min_sistem' => '2.0.0', 'max_sistem' => null],
        ],
        'cache' => [
            'omogocen'       => true,
            'ttl'            => $m['ttl'],
            'strategija'     => in_array('datum_rojstva', $m['potrebuje'] ?? []) ? 'uporabnik' : 'parameter',
            'cisti_ob_zagonu' => false,
        ],
        'ui'     => ['varuh' => null, 'duhec' => null],
        'dogodki' => ['poslusa' => [], 'oddaja' => $m['oddaja']],
    ];
    file_put_contents("$dir/izhod.json", jlep($izhod_json));

    // ── modul.md ──────────────────────────────────────────────────
    $jeziki_str   = implode(', ', $jeziki);
    $tags_str     = implode(', ', $m['tags']);
    $poti_curl    = implode("\n", array_map(fn($p) => "curl http://example.com$p", $poti));
    $bere_str     = count($m['bere_iz']) ? implode(', ', $m['bere_iz']) : '(nič)';
    $oddaja_str   = count($m['oddaja']) ? implode(', ', $m['oddaja']) : '(nič)';
    $pwa_str      = $je_pwa ? 'Da' : 'Ne';
    $api_str      = $api_only ? 'Da' : 'Ne';
    $placljivo_str = in_array($m['plan'], ['pro', 'vip']) ? 'Da' : 'Ne';

    $md = <<<MD
# {$PascalIme} · {$m['ime_izvirno']}

**ID:** {$id}
**Verzija:** 1.0.0
**Tip:** {$m['tip']}
**Nivo:** {$m['nivo']}
**Status:** razvoj

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

{$m['opis']}

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | {$m['ime_izvirno']} | CJK |

> Vsebina modula se sklicuje na izvirne vire. Vsak sistem je
> predstavljen v svojem kulturnem kontekstu — ne prilagojen zahodni
> mistiki, temveč ohranjem v izvirni obliki z razlago za uporabnika.

---

## Dostop

- **Minimalna vloga:** {$m['minimalna_vloga']}
- **Plan:** {$m['plan']}
- **Plačljivo:** {$placljivo_str}
- **Otroški:** Ne
- **Vidnost:** {$manifest['dostop']['vidnost']}
- **Dovoljenja:** {$m['dovoljenja'][0]}

---

## UI & PWA

- **Ima prikaz:** {$pwa_str}
- **Ikona:** {$m['ikona']}
- **Barva:** {$m['barva']}
- **Kategorija:** {$m['kategorija']}
- **PWA podpora:** {$pwa_str}
- **API only:** {$api_str}
- **Tags:** {$tags_str}
- **Jeziki:** {$jeziki_str}

---

## Odvisnosti

- **Bere iz:** {$bere_str}
- **Oddaja:** {$oddaja_str}
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/{$PascalIme}/`
2. Aktiviraj modul v sistemu
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem 2.0.0
- PHP 8.1, 8.2, 8.3

---

## Changelog

### 1.0.0 (23.06.2026)
- Prva izdaja — azijski in japonski modul
- Večjezična podpora: {$jeziki_str}

---

## Uporaba

```bash
{$poti_curl}
```
MD;
    file_put_contents("$dir/modul.md", $md);

    echo "✔ $PascalIme ($id) · {$m['ime_izvirno']}\n";
}

echo "\nSkupaj: " . count($moduli) . " modulov\n";
