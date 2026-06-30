<?php
/**
 * MODUL: Orakleum
 * API: modul_rakleum_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: divinacija
 */

declare(strict_types=1);

function modul_rakleum_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Orakleum', 'id' => 'rakleum', 'akcija' => $akcija, 'cas' => time()];
}