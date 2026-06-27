<?php
/**
 * MODUL: Sreiki
 * API: modul_reiki_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: praksa
 */

declare(strict_types=1);

function modul_reiki_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Sreiki', 'id' => 'reiki', 'akcija' => $akcija, 'cas' => time()];
}