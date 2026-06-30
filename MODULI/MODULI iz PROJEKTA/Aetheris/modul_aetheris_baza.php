<?php
/**
 * ForumBaza: preprosta JSON baza za modul Aetheris
 * POT: MODULI/Aetheris/modul_aetheris_baza.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

class ForumBaza
{
    private string $pot;
    private array $data;

    public function __construct()
    {
        $this->pot = __DIR__ . '/podatki/forum.json';
        $this->data = $this->nalozi();
    }

    private function nalozi(): array
    {
        if (file_exists($this->pot)) {
            $json = json_decode(file_get_contents($this->pot), true);
            if (is_array($json)) return $json;
        }
        return [
            'zadnji_id' => 0,
            'teme' => [], // id => tema
        ];
    }

    private function shrani(): bool
    {
        $mapa = dirname($this->pot);
        if (!is_dir($mapa)) mkdir($mapa, 0755, true);
        return file_put_contents($this->pot, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

    private function naslednjiId(): int
    {
        $this->data['zadnji_id'] = ($this->data['zadnji_id'] ?? 0) + 1;
        return $this->data['zadnji_id'];
    }

    public function statistika(): array
    {
        $teme = $this->data['teme'] ?? [];
        $steviloTem = count($teme);
        $objave = 0;
        $clani = [];
        foreach ($teme as $t) {
            $objave += count($t['objave'] ?? []);
            $clani[$t['avtor_id']] = true;
            foreach ($t['objave'] ?? [] as $o) $clani[$o['avtor_id']] = true;
        }
        return ['teme' => $steviloTem, 'objave' => $objave, 'clani' => count($clani)];
    }

    public function zadnjeTeme(int $limit = 10): array
    {
        $teme = array_values($this->data['teme'] ?? []);
        usort($teme, fn($a, $b) => $b['datum'] <=> $a['datum']);
        return array_slice($teme, 0, $limit);
    }

    public function kategorije(): array
    {
        $map = [];
        foreach ($this->data['teme'] as $t) {
            $k = $t['kategorija'] ?? 'splošno';
            if (!isset($map[$k])) $map[$k] = 0;
            $map[$k]++;
        }
        $out = [];
        foreach ($map as $k => $c) $out[] = ['ime' => $k, 'stevilo' => $c];
        return $out;
    }

    public function pridobiTeme(?string $kategorija = null, int $stran = 1, int $limit = 20): array
    {
        $teme = array_values($this->data['teme'] ?? []);
        if ($kategorija) {
            $teme = array_filter($teme, fn($t) => ($t['kategorija'] ?? 'splošno') === $kategorija);
        }
        usort($teme, fn($a, $b) => $b['datum'] <=> $a['datum']);
        $start = max(0, ($stran -1) * $limit);
        return array_slice($teme, $start, $limit);
    }

    public function pridobiTemo(int $id): ?array
    {
        return $this->data['teme'][$id] ?? null;
    }

    public function objaveTeme(int $temaId): array
    {
        $t = $this->pridobiTemo($temaId);
        return $t['objave'] ?? [];
    }

    public function ustvariTemo(string $naslov, string $vsebina, int $avtorId, string $avtorIme, string $kategorija = 'splošno'): int
    {
        $id = $this->naslednjiId();
        $tema = [
            'id' => $id,
            'naslov' => $naslov,
            'vsebina' => $vsebina,
            'avtor_id' => $avtorId,
            'avtor_ime' => $avtorIme,
            'kategorija' => $kategorija,
            'datum' => date('Y-m-d H:i:s'),
            'objave' => [],
            'posodobljen' => date('Y-m-d H:i:s')
        ];
        $this->data['teme'][$id] = $tema;
        $this->shrani();
        return $id;
    }

    public function dodajObjavo(int $temaId, string $vsebina, int $avtorId, string $avtorIme): int
    {
        if (!isset($this->data['teme'][$temaId])) return 0;
        $objId = $this->naslednjiId();
        $obj = [
            'id' => $objId,
            'tema_id' => $temaId,
            'vsebina' => $vsebina,
            'avtor_id' => $avtorId,
            'avtor_ime' => $avtorIme,
            'datum' => date('Y-m-d H:i:s')
        ];
        $this->data['teme'][$temaId]['objave'][] = $obj;
        $this->data['teme'][$temaId]['posodobljen'] = date('Y-m-d H:i:s');
        $this->shrani();
        return $objId;
    }

    public function isci(string $query): array
    {
        $rez = [];
        $q = mb_strtolower($query);
        foreach ($this->data['teme'] as $t) {
            if (mb_strpos(mb_strtolower($t['naslov']), $q) !== false || mb_strpos(mb_strtolower($t['vsebina']), $q) !== false) {
                $rez[] = $t;
                continue;
            }
            foreach ($t['objave'] as $o) {
                if (mb_strpos(mb_strtolower($o['vsebina']), $q) !== false) {
                    $rez[] = $t;
                    break;
                }
            }
        }
        return $rez;
    }
}