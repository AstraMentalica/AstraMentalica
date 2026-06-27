<?php
/**
 * MODUL: Modul_Mystaia
 * API: modul_odulystaia_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: enciklopedija
 */

declare(strict_types=1);

function modul_odulystaia_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Modul_Mystaia', 'id' => 'odulystaia', 'akcija' => $akcija, 'cas' => time()];
}