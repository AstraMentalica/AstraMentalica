<?php
/**
 * MODUL: Shamanica
 * API: modul_hamanica_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     API endpoint za modul Shamanica
 *     Tip: praksa
 */

declare(strict_types=1);

/**
 * API handler za modul Shamanica
 *
 * @param string $akcija Akcija za izvedbo
 * @param array $parametri Parametri akcije
 * @return array Rezultat
 */
function modul_hamanica_api_izvedi(string $akcija, array $parametri = []): array {
    $dovoljene_akcije = ['info', 'domov', 'isci', 'pridobi'];
    
    if (!in_array($akcija, $dovoljene_akcije)) {
        return [
            'status' => 'napaka',
            'sporocilo' => "Neznana akcija: $akcija",
            'koda' => 400
        ];
    }
    
    return [
        'status' => 'uspeh',
        'modul' => 'Shamanica',
        'id' => 'hamanica',
        'akcija' => $akcija,
        'parametri' => $parametri,
        'cas' => time()
    ];
}