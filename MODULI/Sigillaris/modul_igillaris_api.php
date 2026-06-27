<?php
/**
 * MODUL: Sigillaris
 * API: modul_igillaris_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: orodje
 */

declare(strict_types=1);

function modul_igillaris_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene = ['info', 'domov', 'isci', 'pridobi'];
    if (!in_array($akcija, $dovoljene)) {
        return ['status' => 'napaka', 'sporocilo' => "Neznana akcija: $akcija", 'koda' => 400];
    }
    return ['status' => 'uspeh', 'modul' => 'Sigillaris', 'id' => 'igillaris', 'akcija' => $akcija, 'cas' => time()];
}