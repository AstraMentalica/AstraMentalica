/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/zid_modrosti.php
 * v116 (15.06.2026 06:44)
 * ---------------------------------------------------------
 * OPIS: Anonimni nabiralnik – shranjevanje in branje sporočil
 * ---------------------------------------------------------
 */
declare(strict_types=1);

function zid_modrosti_shrani(string $sporocilo, ?string $avtor = null): array
{
    $sporocilo = trim($sporocilo);
    if (strlen($sporocilo) < 3) {
        return ['status' => 'napaka', 'sporocilo' => 'Sporočilo je prekratko.'];
    }
    if (strlen($sporocilo) > 500) {
        return ['status' => 'napaka', 'sporocilo' => 'Sporočilo je predolgo (max 500 znakov).'];
    }
    
    $pot = POT_PODATKI_GLOBALNO . '/zid_modrosti.json';
    $sporocila = [];
    
    if (file_exists($pot)) {
        $sporocila = json_decode(file_get_contents($pot), true) ?? [];
    }
    
    // Dodaj novo sporočilo
    $novo = [
        'id' => uniqid('msg_', true),
        'sporocilo' => htmlspecialchars($sporocilo, ENT_QUOTES, 'UTF-8'),
        'avtor' => $avtor ? htmlspecialchars($avtor) : null,
        'cas' => time(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
    ];
    
    array_unshift($sporocila, $novo);
    
    // Omeji na zadnjih 200 sporočil
    if (count($sporocila) > 200) {
        $sporocila = array_slice($sporocila, 0, 200);
    }
    
    file_put_contents($pot, json_encode($sporocila, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    return ['status' => 'uspeh', 'sporocilo' => 'Modrost je dodana na zid.'];
}

function zid_modrosti_pridobi(int $limit = 50): array
{
    $pot = POT_PODATKI_GLOBALNO . '/zid_modrosti.json';
    if (!file_exists($pot)) {
        return [];
    }
    
    $sporocila = json_decode(file_get_contents($pot), true) ?? [];
    
    // Vrni samo nepomembne podatke (anonimno)
    $anonimna = [];
    foreach (array_slice($sporocila, 0, $limit) as $s) {
        $anonimna[] = [
            'sporocilo' => $s['sporocilo'],
            'cas' => $s['cas'],
            'cas_formatiran' => date('d.m.Y H:i', $s['cas'])
        ];
    }
    
    return $anonimna;
}

// API endpoint (dodaj v api.php)
if (function_exists('api_dodaj_pot')) {
    api_dodaj_pot('/zid_modrosti/dodaj', function($zahteva) {
        $sporocilo = $zahteva['vsebina']['sporocilo'] ?? $zahteva['parametri']['sporocilo'] ?? '';
        $avtor = $zahteva['vsebina']['avtor'] ?? null;
        
        if (seja_je_prijavljen()) {
            $uporabnik = seja_pridobi_uporabnika();
            $avtor = $avtor ?? $uporabnik['ime'] ?? 'Anonimnež';
        }
        
        return zid_modrosti_shrani($sporocilo, $avtor);
    }, ['OBJAVA', 'POST']);
    
    api_dodaj_pot('/zid_modrosti/beri', function($zahteva) {
        $limit = (int)($zahteva['parametri']['limit'] ?? 50);
        return odziv_uspeh(zid_modrosti_pridobi($limit), 'Sporočila naložena.');
    }, ['DOBI', 'GET']);
}