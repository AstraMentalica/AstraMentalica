<?php
/**
 * DATOTEKA: universe_data.php
 * NAMEN:    Vrne podatke za 3D vesolje (planete/ module)
 * NIVO:     2
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

require_once dirname(__DIR__, 3) . '/pot.php';

function universeGetData(): array {
    $moduli = [];
    
    // Preberi vse module
    $moduliPath = MODULI_POT;
    $kategorije = ['osnovni', 'premium', 'vticniki'];
    
    $centerX = 0;
    $centerZ = 0;
    
    foreach ($kategorije as $indexKat => $kategorija) {
        $katPath = $moduliPath . '/' . $kategorija;
        if (!is_dir($katPath)) continue;
        
        $mape = scandir($katPath);
        $angleOffset = ($indexKat * 120); // 120 stopinj med kategorijami
        
        foreach ($mape as $idx => $mapa) {
            if ($mapa === '.' || $mapa === '..') continue;
            
            $modulPath = $katPath . '/' . $mapa;
            $manifest = $modulPath . '/podatki/manifest.json';
            
            if (!file_exists($manifest)) continue;
            
            $manifestData = json_decode(file_get_contents($manifest), true);
            
            // Izračunaj pozicijo planeta
            $radius = 8 + ($idx * 1.5); // radij orbite
            $angle = deg2rad($angleOffset + ($idx * 25));
            
            $x = $centerX + $radius * cos($angle);
            $z = $centerZ + $radius * sin($angle);
            
            // Velikost planeta glede na globino / število vsebin
            $size = 0.8 + (($idx % 5) * 0.1);
            
            // Barva glede na kategorijo
            $colors = [
                'osnovni' => '#4a90d9',   // modra
                'premium' => '#d9b48b',   // zlata
                'vticniki' => '#6b5b95'   // vijolična
            ];
            
            $moduli[] = [
                'id' => $mapa,
                'ime' => $mapa,
                'kategorija' => $kategorija,
                'opis' => $manifestData['opis'] ?? 'Raziskuj...',
                'position' => ['x' => $x, 'y' => 0, 'z' => $z],
                'size' => $size,
                'color' => $colors[$kategorija] ?? '#888888',
                'orbitRadius' => $radius,
                'orbitSpeed' => 0.2 + ($idx * 0.05),
                'emissive' => $kategorija === 'premium',
                'texture' => null // kasneje
            ];
        }
    }
    
    // Dodaj center (Codex - lastnikova knjiga)
    $moduli[] = [
        'id' => 'codex',
        'ime' => 'CODEX',
        'kategorija' => 'center',
        'opis' => 'Srce vsega — lastnikova knjiga modrosti',
        'position' => ['x' => 0, 'y' => 0, 'z' => 0],
        'size' => 2.5,
        'color' => '#ffd700',
        'emissive' => true,
        'isCenter' => true
    ];
    
    return [
        'uspeh' => true,
        'moduli' => $moduli,
        'center' => ['x' => 0, 'y' => 0, 'z' => 0]
    ];
}

function universeGetModulContent(string $modulId): array {
    // Poišči modul
    $paths = [
        MODULI_POT . '/osnovni/' . $modulId,
        MODULI_POT . '/premium/' . $modulId,
        MODULI_POT . '/vticniki/' . $modulId
    ];
    
    foreach ($paths as $path) {
        if (is_dir($path)) {
            $manifest = $path . '/podatki/manifest.json';
            if (file_exists($manifest)) {
                $manifestData = json_decode(file_get_contents($manifest), true);
                
                // Pokliči modul za seznam vsebine
                if (function_exists('modul' . $modulId . 'Akcija')) {
                    $content = modulAkcija($modulId, 'seznam', ['stran' => 1, 'na_stran' => 5]);
                } else {
                    $content = ['vnosi' => []];
                }
                
                return [
                    'uspeh' => true,
                    'modul' => $manifestData,
                    'vsebina' => $content['vnosi'] ?? []
                ];
            }
        }
    }
    
    return ['napaka' => 'Modul ne obstaja'];
}

// Helper za klic modula
function modulAkcija(string $imeModula, string $akcija, array $podatki): array {
    // Klic čez API, ne direktno
    return apiKlic("api/modul/$imeModula", 'POST', [
        'akcija' => $akcija,
        'podatki' => $podatki
    ]);
}