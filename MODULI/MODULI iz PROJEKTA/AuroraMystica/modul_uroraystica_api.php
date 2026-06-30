<?php
/**
 * MODUL: AuroraMystica
 * API: modul_uroraystica_api.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     API endpoint za modul AuroraMystica
 *     Tip: vizualni
 */

declare(strict_types=1);

/**
 * API handler za modul AuroraMystica
 *
 * @param string $akcija Akcija za izvedbo
 * @param array $parametri Parametri akcije
 * @return array Rezultat
 */
function modul_uroraystica_api_izvedi(string $akcija, array $parametri = []): array {
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
        'modul' => 'AuroraMystica',
        'id' => 'uroraystica',
        'akcija' => $akcija,
        'parametri' => $parametri,
        'cas' => time()
    ];
}