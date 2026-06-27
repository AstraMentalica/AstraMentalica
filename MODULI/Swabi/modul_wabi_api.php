<?php
/**
 * MODUL: Swabi
 * API: modul_wabi_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: praksa
 */

declare(strict_types=1);

function modul_wabi_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Swabi', 'id' => 'wabi', 'akcija' => $akcija, 'cas' => time()];
}