<?php
declare(strict_types=1);
/**
 * pozabljeno_geslo.php – Pozabljeno geslo
 * AstraMentalica v7.0
 */

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

registriraj_pot('pozabljeno_geslo', function($p) {
    $email = strtolower(trim($p['email'] ?? ''));
    if (!$email) return api_napaka('Vnesi e-pošto.');
    
    $baza = PODATKI_UPORABNIKI . '/uporabniki.json';
    if (!file_exists($baza)) return api_napaka('E-pošta ni najdena.');
    
    $up = json_decode(file_get_contents($baza), true) ?? [];
    $found = false;
    foreach ($up as &$u) {
        if (strtolower($u['email']) === $email) {
            $found = true;
            $token = bin2hex(random_bytes(32));
            $u['reset_token'] = $token;
            $u['reset_expires'] = time() + 3600;
            break;
        }
    }
    
    if (!$found) return api_napaka('E-pošta ni najdena.');
    file_put_contents($baza, json_encode($up, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    
    // V razvojnem načinu izpiši token
    if (defined('RAZVOJNI_NACIN') && RAZVOJNI_NACIN) {
        return api_uspeh(['token' => $token, 'sporocilo' => 'Link za ponastavitev: /ponastavi-geslo?token=' . $token]);
    }
    
    return api_uspeh(['sporocilo' => 'Če e-pošta obstaja, smo poslali link za ponastavitev.']);
});

registriraj_pot('ponastavi_geslo', function($p) {
    $token = $p['token'] ?? '';
    $geslo = $p['geslo'] ?? '';
    
    if (!$token || !$geslo) return api_napaka('Manjka token ali geslo.');
    if (strlen($geslo) < 8) return api_napaka('Geslo mora imeti vsaj 8 znakov.');
    
    $baza = PODATKI_UPORABNIKI . '/uporabniki.json';
    if (!file_exists($baza)) return api_napaka('Napaka sistema.');
    
    $up = json_decode(file_get_contents($baza), true) ?? [];
    $found = false;
    foreach ($up as &$u) {
        if (($u['reset_token'] ?? '') === $token && ($u['reset_expires'] ?? 0) > time()) {
            $found = true;
            $u['geslo_hash'] = password_hash($geslo, PASSWORD_DEFAULT);
            unset($u['reset_token'], $u['reset_expires']);
            break;
        }
    }
    
    if (!$found) return api_napaka('Neveljaven ali potekel token.');
    file_put_contents($baza, json_encode($up, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    
    return api_uspeh(['sporocilo' => 'Geslo uspešno spremenjeno. Prijavi se.']);
});