<?php
/**
 * MODULI/Codex/modul_codex_api.php
 * Preprost API adapter, ki sprejema vnose iz drugih modulov (Aetheris, Celestara).
 */

declare(strict_types=1);

function modul_codex_dodaj_vnos(array $podatki): bool {
    $mapa = __DIR__ . '/podatki/moduli/codex';
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    $file = $mapa . '/vnosi.json';
    $vnosi = [];
    if (file_exists($file)) {
        $vnosi = json_decode(file_get_contents($file), true) ?? [];
    }

    $id = count($vnosi) + 1;
    $vnos = [
        'id' => $id,
        'tip' => $podatki['tip'] ?? 'neznano',
        'naslov' => $podatki['naslov'] ?? ($podatki['title'] ?? ''),
        'vsebina' => $podatki['vsebina'] ?? ($podatki['content'] ?? ''),
        'avtor' => $podatki['avtor'] ?? ($podatki['author'] ?? 'anonim'),
        'vir' => $podatki['vir'] ?? ($podatki['source'] ?? 'external'),
        'vir_id' => $podatki['forum_id'] ?? ($podatki['source_id'] ?? null),
        'cas' => date('Y-m-d H:i:s'),
    ];

    $vnosi[] = $vnos;
    return file_put_contents($file, json_encode($vnosi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}