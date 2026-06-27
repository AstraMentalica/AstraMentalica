<?php
/**
 * ============================================================
 * POT: MODULI/ORAKLEUM/Tarot/podatki/karte_78.php
 * 📅 VERZIJA: v1.0.0 (19.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL / PODATKI
 *
 * 📰 NAMEN:
 *     Statični podatki — celoten tarot špil (78 kart).
 *     22 velikih arkan + 56 malih arkan (4 barve x 14 kart).
 *     Vsaka karta ima ločen pomen za pokončno (uspravno)
 *     in obrnjeno (obrnjeno) pozicijo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tarot_karte_vse(): array
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
 *     modul, tarot, podatki, karte
 * ============================================================
 */

declare(strict_types=1);

function tarot_karte_vse(): array {
    static $karte = null;

    if ($karte !== null) {
        return $karte;
    }

    $velike_arkane = [
        ['id' => 0,  'ime' => 'Norec',          'arkana' => 'velika', 'uspravno' => 'Nove poti, zaupanje, svoboda, spontanost',         'obrnjeno' => 'Lahkomiselnost, tveganje brez razmisleka, kaos'],
        ['id' => 1,  'ime' => 'Čarovnik',        'arkana' => 'velika', 'uspravno' => 'Manifestacija, ustvarjanje, moč volje',            'obrnjeno' => 'Manipulacija, izkoriščanje, neizkoriščen potencial'],
        ['id' => 2,  'ime' => 'Visoka svečenica','arkana' => 'velika', 'uspravno' => 'Intuicija, skrivnosti, notranje znanje',           'obrnjeno' => 'Skrite agende, prekinjena intuicija'],
        ['id' => 3,  'ime' => 'Cesarica',        'arkana' => 'velika', 'uspravno' => 'Obilje, ustvarjalnost, materinstvo',               'obrnjeno' => 'Blokirana ustvarjalnost, odvisnost'],
        ['id' => 4,  'ime' => 'Cesar',           'arkana' => 'velika', 'uspravno' => 'Struktura, avtoriteta, stabilnost',                'obrnjeno' => 'Tiranija, rigidnost, izguba nadzora'],
        ['id' => 5,  'ime' => 'Papež',           'arkana' => 'velika', 'uspravno' => 'Tradicija, duhovnost, modrost',                    'obrnjeno' => 'Dogma, uporništvo proti normam'],
        ['id' => 6,  'ime' => 'Ljubimca',        'arkana' => 'velika', 'uspravno' => 'Ljubezen, odločitve, harmonija',                   'obrnjeno' => 'Neravnovesje, slabe odločitve, konflikt vrednot'],
        ['id' => 7,  'ime' => 'Kočija',          'arkana' => 'velika', 'uspravno' => 'Zmaga, nadzor, odločnost',                         'obrnjeno' => 'Izguba smeri, agresija, šibka volja'],
        ['id' => 8,  'ime' => 'Moč',             'arkana' => 'velika', 'uspravno' => 'Pogum, sočutje, notranja moč',                     'obrnjeno' => 'Dvom vase, šibkost, nesigurnost'],
        ['id' => 9,  'ime' => 'Puščavnik',       'arkana' => 'velika', 'uspravno' => 'Samota, introspekcija, iskanje resnice',           'obrnjeno' => 'Izolacija, osamljenost, izguba smeri'],
        ['id' => 10, 'ime' => 'Kolo sreče',      'arkana' => 'velika', 'uspravno' => 'Spremembe, usoda, cikli',                          'obrnjeno' => 'Smola, odpor do sprememb, ponavljajoči vzorci'],
        ['id' => 11, 'ime' => 'Pravica',         'arkana' => 'velika', 'uspravno' => 'Resnica, ravnotežje, karmična odgovornost',        'obrnjeno' => 'Nepravičnost, nepoštenost, neravnovesje'],
        ['id' => 12, 'ime' => 'Obešeni',         'arkana' => 'velika', 'uspravno' => 'Predaja, nov pogled, žrtvovanje',                  'obrnjeno' => 'Odlašanje, upiranje spremembam'],
        ['id' => 13, 'ime' => 'Smrt',            'arkana' => 'velika', 'uspravno' => 'Transformacija, konci, novi začetki',              'obrnjeno' => 'Upiranje spremembi, stagnacija'],
        ['id' => 14, 'ime' => 'Zmernost',        'arkana' => 'velika', 'uspravno' => 'Ravnotežje, potrpežljivost, zdravljenje',          'obrnjeno' => 'Neravnovesje, pretiravanje, nestrpnost'],
        ['id' => 15, 'ime' => 'Hudič',           'arkana' => 'velika', 'uspravno' => 'Pasti, odvisnost, materializem',                   'obrnjeno' => 'Osvoboditev, prekinjanje vezi, prebujanje'],
        ['id' => 16, 'ime' => 'Stolp',           'arkana' => 'velika', 'uspravno' => 'Uničenje, preobrat, razodetje',                    'obrnjeno' => 'Izogibanje katastrofi, odložena kriza'],
        ['id' => 17, 'ime' => 'Zvezda',          'arkana' => 'velika', 'uspravno' => 'Upanje, navdih, duhovna povezava',                 'obrnjeno' => 'Obup, izguba vere, odklop'],
        ['id' => 18, 'ime' => 'Luna',            'arkana' => 'velika', 'uspravno' => 'Iluzije, strahovi, podzavest',                     'obrnjeno' => 'Razkrivanje resnice, zmedenost se razčisti'],
        ['id' => 19, 'ime' => 'Sonce',           'arkana' => 'velika', 'uspravno' => 'Sreča, uspeh, radost',                             'obrnjeno' => 'Začasna potrtost, nerealna pričakovanja'],
        ['id' => 20, 'ime' => 'Sodba',           'arkana' => 'velika', 'uspravno' => 'Prebujenje, odpuščanje, klic',                     'obrnjeno' => 'Samokritičnost, izogibanje preteklosti'],
        ['id' => 21, 'ime' => 'Svet',            'arkana' => 'velika', 'uspravno' => 'Celovitost, izpolnitev, potovanje',                'obrnjeno' => 'Nezaključenost, odlašanje zaključka'],
    ];

    $male_arkane = [];
    $barve = [
        'pali'    => ['simbol' => '🔥', 'tema' => 'volja, strast, akcija'],
        'kelihi'  => ['simbol' => '💧', 'tema' => 'čustva, odnosi, intuicija'],
        'meci'    => ['simbol' => '💨', 'tema' => 'misli, konflikt, resnica'],
        'denarji' => ['simbol' => '🌍', 'tema' => 'materija, denar, telo'],
    ];

    $stevilke = [
        1 => 'As', 2 => 'Dvojka', 3 => 'Trojka', 4 => 'Štirica', 5 => 'Petica',
        6 => 'Šestica', 7 => 'Sedmica', 8 => 'Osmica', 9 => 'Devetica', 10 => 'Desetica',
        11 => 'Paž', 12 => 'Vitez', 13 => 'Kraljica', 14 => 'Kralj',
    ];

    $id_stevec = 22;
    foreach ($barve as $barva_ime => $barva_info) {
        foreach ($stevilke as $st => $st_ime) {
            $male_arkane[] = [
                'id'       => $id_stevec++,
                'ime'      => "$st_ime $barve_ime",
                'arkana'   => 'mala',
                'barva'    => $barva_ime,
                'uspravno' => "Tema {$barva_info['tema']} — pozitivna, gradeča energija ({$st_ime}, $barva_ime).",
                'obrnjeno' => "Tema {$barva_info['tema']} — blokirana ali notranje obrnjena energija ({$st_ime}, $barva_ime).",
            ];
        }
    }

    $karte = array_merge($velike_arkane, $male_arkane);

    return $karte;
}
