<?php
/**
 * MODUL: Sshiatsu
 * API: modul_shiatsu_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: praksa
 */

declare(strict_types=1);

function modul_shiatsu_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Sshiatsu', 'id' => 'shiatsu', 'akcija' => $akcija, 'cas' => time()];
}