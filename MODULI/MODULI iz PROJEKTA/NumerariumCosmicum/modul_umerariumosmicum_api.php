<?php
/**
 * MODUL: NumerariumCosmicum
 * API: modul_umerariumosmicum_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: divinacija
 */

declare(strict_types=1);

function modul_umerariumosmicum_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'NumerariumCosmicum', 'id' => 'umerariumosmicum', 'akcija' => $akcija, 'cas' => time()];
}