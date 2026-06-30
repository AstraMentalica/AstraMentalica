<?php
/**
 * MODUL: Codex
 * API: modul_odex_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: knjiga
 */

declare(strict_types=1);

function modul_odex_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Codex', 'id' => 'odex', 'akcija' => $akcija, 'cas' => time()];
}