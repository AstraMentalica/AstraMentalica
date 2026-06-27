<?php
/**
 * MODULI/CorpusMysticum/zavest.php
 * Preprost sistem "zavesti" za varuhe, duhove, avatarje in magične živali.
 * Uporablja JSON datoteko za shranjevanje atributov in nudi generatorje
 * meditacij, pravljic ter osnovnih osebnostnih profilov.
 */

declare(strict_types=1);

class ZavestManager
{
    private string $pot;
    private array $data;

    public function __construct()
    {
        $this->pot = __DIR__ . '/podatki/zavest.json';
        $this->data = $this->nalozi();
    }

    private function nalozi(): array
    {
        if (file_exists($this->pot)) {
            $json = json_decode(file_get_contents($this->pot), true);
            if (is_array($json)) return $json;
        }
        return ['entities' => []];
    }

    private function shrani(): bool
    {
        $mapa = dirname($this->pot);
        if (!is_dir($mapa)) mkdir($mapa, 0755, true);
        return file_put_contents($this->pot, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

    public function pridobi(string $id): ?array
    {
        return $this->data['entities'][$id] ?? null;
    }

    public function posodobi(string $id, array $atributi): bool
    {
        $ent = $this->data['entities'][$id] ?? [
            'id' => $id,
            'ime' => $atributi['ime'] ?? 'Anonim',
            'tip' => $atributi['tip'] ?? 'neznano',
            'atributi' => $atributi['atributi'] ?? [],
            'stanje' => $atributi['stanje'] ?? []
        ];
        $ent['atributi'] = array_merge($ent['atributi'] ?? [], $atributi['atributi'] ?? []);
        $ent['stanje'] = array_merge($ent['stanje'] ?? [], $atributi['stanje'] ?? []);
        $this->data['entities'][$id] = $ent;
        return $this->shrani();
    }

    public function generirajMeditacijo(string $id, int $dolzina = 3): string
    {
        $ent = $this->pridobi($id);
        $ime = $ent['ime'] ?? 'Prijatelj';
        $tema = $ent['atributi'][0] ?? 'ravnovesje';
        $uvod = "Sprostite se, $ime. Osredotočite se na svoje dihanje.";
        $vaje = [
            "Zaprite oči in sledite vdihu štiri sekunde, izdihu štiri.",
            "Predstavljajte si valove energije, ki prihajajo in odhajajo.",
            "Osredotočite se na temo: $tema, dovolite ji, da se zlije z vami."
        ];
        $izhod = "Odprite oči počasi in začutite spremembo.";
        $sekvence = array_slice($vaje, 0, max(1, min($dolzina, count($vaje))));
        return $uvod . " " . implode(" ", $sekvence) . " " . $izhod;
    }

    public function generirajPravljico(string $id): string
    {
        $ent = $this->pridobi($id);
        $ime = $ent['ime'] ?? 'Mali junak';
        $zival = $ent['atributi'][1] ?? 'čarobna žival';
        $uvod = "Nekoč je bil $ime, ki je spoznal svojega prijatelja, $zival.";
        $sreda = "Skupaj sta odkrivala skrivnosti sveta in se učila poguma, prijaznosti in modrosti.";
        $konec = "In tako sta vsak dan rasla, ustvarjala zgodbe in spreminjala svet okoli sebe.";
        return $uvod . " " . $sreda . " " . $konec;
    }
}

// Pripravni API funkciji za modul
function corpus_zavest_pridobi(array $p): array {
    $mgr = new ZavestManager();
    $id = $p['id'] ?? '';
    if (empty($id)) return ['status'=>'napaka','sporocilo'=>'Manjka id'];
    $v = $mgr->pridobi($id);
    return $v ? ['status'=>'uspeh','vsebina'=>$v] : ['status'=>'napaka','sporocilo'=>'Entiteta ne obstaja'];
}

function corpus_zavest_posodobi(array $p): array {
    $mgr = new ZavestManager();
    $id = $p['id'] ?? '';
    if (empty($id)) return ['status'=>'napaka','sporocilo'=>'Manjka id'];
    $ok = $mgr->posodobi($id, $p);
    return $ok ? ['status'=>'uspeh'] : ['status'=>'napaka','sporocilo'=>'Napaka pri shranjevanju'];
}

function corpus_zavest_meditacija(array $p): array {
    $mgr = new ZavestManager();
    $id = $p['id'] ?? '';
    $dol = (int)($p['dolzina'] ?? 3);
    if (empty($id)) return ['status'=>'napaka','sporocilo'=>'Manjka id'];
    $text = $mgr->generirajMeditacijo($id, $dol);
    return ['status'=>'uspeh','meditacija'=>$text];
}

function corpus_zavest_pravljica(array $p): array {
    $mgr = new ZavestManager();
    $id = $p['id'] ?? '';
    if (empty($id)) return ['status'=>'napaka','sporocilo'=>'Manjka id'];
    $story = $mgr->generirajPravljico($id);
    return ['status'=>'uspeh','pravljica'=>$story];
}