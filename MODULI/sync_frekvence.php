<?php
/**
 * MODULI/sync_frekvence.php
 * Sinhronizacija frekvenc iz modulov (Runaris) v centralno PODATKI/frekvence.json
 */

declare(strict_types=1);

$runarisPath = __DIR__ . '/Runaris/podatki/rune_meta.json';
$centralPath = __DIR__ . '/../PODATKI/frekvence.json';

if (!file_exists($runarisPath)) {
    echo "Runaris meta not found: $runarisPath\n";
    exit(1);
}

$runaris = json_decode(file_get_contents($runarisPath), true) ?? [];
$central = [];
if (file_exists($centralPath)) {
    $central = json_decode(file_get_contents($centralPath), true) ?? [];
}

// Merge: prefer module values, preserve existing other entries
foreach ($runaris as $runa => $meta) {
    $central[$runa] = array_merge($central[$runa] ?? [], [
        'frekvenca' => $meta['frekvenca'] ?? ($central[$runa]['frekvenca'] ?? null),
        'slika' => $meta['slika'] ?? ($central[$runa]['slika'] ?? null),
        'izvor' => 'Runaris',
        'posodobljeno' => date('c')
    ]);
}

// Ensure target dir exists
$dir = dirname($centralPath);
if (!is_dir($dir)) mkdir($dir, 0755, true);

file_put_contents($centralPath, json_encode($central, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Synced " . count($runaris) . " runes to $centralPath\n";