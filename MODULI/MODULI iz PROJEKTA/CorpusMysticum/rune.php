<?php
/**
 * MODULI/CorpusMysticum/rune.php
 * Preprost generator metanja run/kock z interpretacijo.
 */

declare(strict_types=1);

function vrzi_rune(int $kolicina = 3): array {
    // Naloži meta (slike + frekvence) iz Runaris, če obstaja
    $meta = [];
    $metaPath = __DIR__ . '/../Runaris/podatki/rune_meta.json';
    if (file_exists($metaPath)) {
        $meta = json_decode(file_get_contents($metaPath), true) ?? [];
    }

    // Če obstaja modul Runaris, ga uporabimo kot vir (Runarium/Runaris)
    $runarisApi = __DIR__ . '/../Runaris/modul_rune_api.php';
    if (file_exists($runarisApi)) {
        require_once $runarisApi;
        if (class_exists('ModulRuneApi') && method_exists('ModulRuneApi','klicAI')) {
            $res = ModulRuneApi::klicAI(['akcija' => 'metanje', 'kolicina' => $kolicina]);
            if (is_array($res) && !empty($res['uspeh'])) {
                if (!empty($res['rezultat']) && is_array($res['rezultat'])) {
                    // dopolni z meta podatki
                    $out = [];
                    foreach ($res['rezultat'] as $item) {
                        $r = $item['runa'] ?? ($item['name'] ?? null);
                        $out[] = array_merge($item, [
                            'slika' => $meta[$r]['slika'] ?? null,
                            'frekvenca' => $meta[$r]['frekvenca'] ?? null
                        ]);
                    }
                    return $out;
                }
                if (!empty($res['odgovor'])) {
                    return [['runa' => 'AI', 'interpretacija' => (string)$res['odgovor'], 'slika' => null, 'frekvenca' => null]];
                }
            }
        }
    }

    // Privzeti lokalni generator (fallback) z meta podatki
    $rune = ['Fehu','Uruz','Thurisaz','Ansuz','Raidho','Kenaz','Gebo','Wunjo','Hagalaz','Nauthiz','Isa','Jera','Eihwaz','Perthro','Algiz','Sowilo','Tiwaz','Berkana','Ehwaz','Mannaz','Laguz','Ingwaz','Dagaz','Othala'];
    $izbrane = [];
    for ($i=0;$i<$kolicina;$i++) {
        $r = $rune[array_rand($rune)];
        $izbrane[] = [
            'runa' => $r,
            'interpretacija' => interpretiraj_runo($r),
            'slika' => $meta[$r]['slika'] ?? null,
            'frekvenca' => $meta[$r]['frekvenca'] ?? null
        ];
    }
    return $izbrane;
}

function interpretiraj_runo(string $r): string {
    $m = [
        'Fehu' => 'Blaginja, tok, materialni začetki.',
        'Uruz' => 'Sila, moč volje, preobrazba.',
        'Thurisaz' => 'Opozorilo, preizkus, zaščita.',
        'Ansuz' => 'Sporočilo, komunikacija, vpogled.',
        'Raidho' => 'Potovanje, gibanje, smer.',
        'Kenaz' => 'Učenje, zanet, ustvarjalnost.',
        'Gebo' => 'Darilo, partnerstvo, izmenjava.',
        'Wunjo' => 'Radost, harmonija, zadovoljstvo.',
        'Hagalaz' => 'Preobrat, nepričakovano, čiščenje.',
        'Nauthiz' => 'Potrebnost, notranja zaveza, disciplina.',
        'Isa' => 'Stagnacija, zadrževanje, premislek.',
        'Jera' => 'Cikel, nagrada, čas za žetev.',
        'Eihwaz' => 'Prehod, zaščitni steber, transformacija.',
        'Perthro' => 'Skrivnost, usoda, priložnost.',
        'Algiz' => 'Zaščita, intuicija, opazovanje.',
        'Sowilo' => 'Zmagoslavje, jasnost, vitalnost.',
        'Tiwaz' => 'Pravica, pogum, vodstvo.',
        'Berkana' => 'Rast, zdravje, plodnost.',
        'Ehwaz' => 'Premik, partnerstvo, napredek.',
        'Mannaz' => 'Človek, družba, identiteta.',
        'Laguz' => 'Intuicija, tok, sanje.',
        'Ingwaz' => 'Zacelitev, notranja moč, seme.',
        'Dagaz' => 'Preobrazba, preboj, nova svetloba.',
        'Othala' => 'Dediščina, dom, vrednote.'
    ];
    return $m[$r] ?? 'Skrivnost.';
}