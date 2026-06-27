<?php
/**
 * MODUL: Sbazi
 * API: modul_bazi_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     API endpoint za modul Sbazi
 *     Tip: divinacija
 */

declare(strict_types=1);

/**
 * API handler za modul Sbazi
 *
 * @param string $akcija Akcija za izvedbo
 * @param array $parametri Parametri akcije
 * @return array Rezultat
 */
function modul_bazi_api_izvedi(string $akcija, array $parametri = []): array {
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
        'modul' => 'Sbazi',
        'id' => 'bazi',
        'akcija' => $akcija,
        'parametri' => $parametri,
        'cas' => time()
    ];
}