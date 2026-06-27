<?php
/**
 * MODUL: Stelar
 * API: modul_telar_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: enciklopedija
 */

declare(strict_types=1);

function modul_telar_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Stelar', 'id' => 'telar', 'akcija' => $akcija, 'cas' => time()];
}