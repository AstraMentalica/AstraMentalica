no <?php
/**
 * Preveri demo vsebino: codex vnosi, forum teme, blog posts
 */
declare(strict_types=1);

function safe_count($path, $key = null) {
    if (!file_exists($path)) return 0;
    $data = json_decode(file_get_contents($path), true);
    if (!is_array($data)) return 0;
    if ($key === null) return count($data);
    return count($data[$key] ?? []);
}

echo "VERIFY DEMO CONTENT\n\n";

$codex = __DIR__ . '/Codex/podatki/moduli/codex/vnosi.json';
$forum = __DIR__ . '/Aetheris/podatki/forum.json';
$blog  = __DIR__ . '/Celestara/podatki/posts.json';

echo "Codex file: " . (file_exists($codex) ? 'FOUND' : 'MISSING') . PHP_EOL;
if (file_exists($codex)) {
    $vnosi = json_decode(file_get_contents($codex), true) ?? [];
    echo "Codex entries: " . count($vnosi) . PHP_EOL;
}

echo "Forum file: " . (file_exists($forum) ? 'FOUND' : 'MISSING') . PHP_EOL;
if (file_exists($forum)) {
    $f = json_decode(file_get_contents($forum), true) ?? [];
    echo "Forum temas: " . count($f['teme'] ?? []) . PHP_EOL;
    $objave = 0;
    foreach ($f['teme'] as $t) $objave += count($t['objave'] ?? []);
    echo "Forum posts total: " . $objave . PHP_EOL;
}

echo "Blog file: " . (file_exists($blog) ? 'FOUND' : 'MISSING') . PHP_EOL;
if (file_exists($blog)) {
    $b = json_decode(file_get_contents($blog), true) ?? [];
    echo "Blog posts: " . count($b['posts'] ?? []) . PHP_EOL;
}

echo PHP_EOL . "END VERIFY\n";