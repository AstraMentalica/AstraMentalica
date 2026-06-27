<?php
declare(strict_types=1);
/**
 * DATOTEKA: prijava.php
 * NAMEN:    Logika prijave uporabnika
 * NIVO:     2
 * ODVISNO:  jedro/02_varnost.php, jedro/03_seja.php, baze/adapter_sqlite.php
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

function prijavaIzvedi(array $podatki): array {
    $email = strtolower(trim($podatki['email'] ?? ''));
    $geslo = $podatki['geslo'] ?? '';
    $zapomni = (bool)($podatki['zapomni'] ?? false);
    
    if (empty($email) || empty($geslo)) {
        return ['uspeh' => false, 'napaka' => 'Vnesite email in geslo.'];
    }
    
    $pdo = sqlitePovezava('uporabniki');
    if (!$pdo) {
        return ['uspeh' => false, 'napaka' => 'Sistemska napaka.'];
    }
    
    // Poišči uporabnika
    $stmt = $pdo->prepare("SELECT * FROM uporabniki WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $uporabnik = $stmt->fetch();
    
    if (!$uporabnik) {
        return ['uspeh' => false, 'napaka' => 'Napačen email ali geslo.'];
    }
    
    // Preveri status
    if ($uporabnik['status'] === 'blokiran') {
        return ['uspeh' => false, 'napaka' => 'Uporabnik je blokiran.'];
    }
    
    // Preveri geslo
    if (!preveriGesloHash($geslo, $uporabnik['geslo_hash'])) {
        return ['uspeh' => false, 'napaka' => 'Napačen email ali geslo.'];
    }
    
    // Posodobi zadnjo prijavo
    $pdo->prepare("UPDATE uporabniki SET zadnji_ip = :ip, zadnja_prijava = :zdaj WHERE id = :id")
        ->execute([
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ':zdaj' => date('Y-m-d H:i:s'),
            ':id' => $uporabnik['id']
        ]);
    
    // Začni sejo
    zazeniSejo();
    nastaviSejo('uporabnik_id', $uporabnik['id']);
    nastaviSejo('uporabnik_email', $uporabnik['email']);
    nastaviSejo('uporabnik_ime', $uporabnik['ime']);
    nastaviSejo('uporabnik_vloga', $uporabnik['vloga']);
    
    if ($zapomni) {
        // Daljša seja (30 dni)
        ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
        session_regenerate_id(true);
    }
    
    return [
        'uspeh' => true,
        'uporabnik' => [
            'id' => $uporabnik['id'],
            'email' => $uporabnik['email'],
            'ime' => $uporabnik['ime'],
            'vloga' => $uporabnik['vloga']
        ],
        'sporocilo' => 'Prijava uspešna.'
    ];
}

function odjavaIzvedi(): array {
    zazeniSejo();
    uniciSejo();
    return ['uspeh' => true, 'sporocilo' => 'Odjava uspešna.'];
}