<?php
/**
 * MODUL: Sshenlong
 * API: modul_shenlong_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: enciklopedija
 */

declare(strict_types=1);

function modul_shenlong_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Sshenlong', 'id' => 'shenlong', 'akcija' => $akcija, 'cas' => time()];
}