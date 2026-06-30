<?php
class ModulRuneApi {
    private static function lokalniMet(int $kolicina): array {
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
        $keys = array_keys($m);
        $out = [];
        for ($i=0;$i<$kolicina;$i++) {
            $r = $keys[array_rand($keys)];
            $out[] = ['runa' => $r, 'interpretacija' => $m[$r]];
        }
        return $out;
    }

    public static function klicAI($input) {
        $akcija = $input['akcija'] ?? '';
        if ($akcija === 'metanje') {
            $kolicina = max(1, min(12, (int)($input['kolicina'] ?? 3)));

            // NE vključujemo CorpusMysticum tukaj (preprečimo klicno zanko).
            // Namesto tega zagotovimo lasten, hiter generator.
            $rez = self::lokalniMet($kolicina);
            return ['uspeh' => true, 'rezultat' => $rez];
        }

        return ['uspeh' => true, 'odgovor' => "API odziv za modul Rune", 'input' => $input];
    }
}
?>
