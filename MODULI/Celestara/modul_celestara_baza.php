<?php
/**
 * MODULI/Celestara/modul_celestara_baza.php
 * Preprosta JSON baza za blog Celestara
 */

declare(strict_types=1);

class BlogBaza
{
    private string $pot;
    private array $data;

    public function __construct()
    {
        $this->pot = __DIR__ . '/podatki/posts.json';
        $this->data = $this->nalozi();
    }

    private function nalozi(): array
    {
        if (file_exists($this->pot)) {
            $json = json_decode(file_get_contents($this->pot), true);
            if (is_array($json)) return $json;
        }
        return ['zadnji_id' => 0, 'posts' => []];
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

    public function zadnjePoste(int $limit = 10): array
    {
        $posts = array_values($this->data['posts'] ?? []);
        usort($posts, fn($a,$b)=> $b['cas'] <=> $a['cas']);
        return array_slice($posts, 0, $limit);
    }

    public function pridobiPost(int $id): ?array
    {
        return $this->data['posts'][$id] ?? null;
    }

    public function ustvariPost(string $naslov, string $vsebina, int $avtorId, string $avtorIme): int
    {
        $id = $this->naslednjiId();
        $post = [
            'id' => $id,
            'naslov' => $naslov,
            'vsebina' => $vsebina,
            'avtor_id' => $avtorId,
            'avtor_ime' => $avtorIme,
            'cas' => date('Y-m-d H:i:s'),
            'komentarji' => []
        ];
        $this->data['posts'][$id] = $post;
        $this->shrani();
        return $id;
    }

    public function dodajKomentar(int $postId, string $vsebina, int $avtorId, string $avtorIme): int
    {
        if (!isset($this->data['posts'][$postId])) return 0;
        $kom_id = $this->naslednjiId();
        $kom = [
            'id' => $kom_id,
            'vsebina' => $vsebina,
            'avtor_id' => $avtorId,
            'avtor_ime' => $avtorIme,
            'cas' => date('Y-m-d H:i:s')
        ];
        $this->data['posts'][$postId]['komentarji'][] = $kom;
        $this->shrani();
        return $kom_id;
    }

    public function isci(string $q): array
    {
        $rez = [];
        $q = mb_strtolower($q);
        foreach ($this->data['posts'] as $p) {
            if (mb_strpos(mb_strtolower($p['naslov']), $q) !== false || mb_strpos(mb_strtolower($p['vsebina']), $q) !== false) {
                $rez[] = $p;
            }
        }
        return $rez;
    }
}