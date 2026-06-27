<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_geslo.php
 * v111 (02.06.2026)
 * ---------------------------------------------------------
 * OPIS: Sprememba gesla, obnovitev, pošiljanje emaila
 * ---------------------------------------------------------
 */
declare(strict_types=1);

function uporabnik_poslji_obnovitveni_email(string $email, string $zeton): bool
{
    $potPredloge = POT_VSEBINA . '/email/obnovitev_gesla.html';
    $predloga = file_exists($potPredloge) ? file_get_contents($potPredloge) : '';
    
    if (empty($predloga)) {
        $predloga = "<h1>Obnovitev gesla</h1><p>Kliknite na povezavo: <a href='{povezava}'>{povezava}</a></p>";
    }
    
    $povezava = "https://" . $_SERVER['HTTP_HOST'] . "/?svet=UPORABNIKI&pot=ponastavi_geslo&zeton=" . urlencode($zeton);
    
    $vsebina = str_replace(
        ['{povezava}', '{ime}', '{leto}'],
        [$povezava, $email, date('Y')],
        $predloga
    );
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: AstraMentalica <noreply@astramentalica.com>\r\n";
    
    return mail($email, 'Obnovitev gesla – AstraMentalica', $vsebina, $headers);
}

function uporabnik_obnovi_geslo(string $email): array
{
    $uporabnik = uporabniki_po_emailu($email);
    if (!$uporabnik) {
        // Ne razkrivamo ali email obstaja
        return [
            'status' => 'uspeh',
            'sporocilo' => 'Če email obstaja, smo poslali navodila za obnovitev gesla.'
        ];
    }
    
    $zeton = bin2hex(random_bytes(32));
    $potek = time() + 3600; // 1 ura
    
    baza_posodobi('uporabniki', $uporabnik['id'], [
        'obnovitveni_zeton' => $zeton,
        'obnovitveni_zeton_cas' => $potek
    ]);
    
    uporabnik_poslji_obnovitveni_email($email, $zeton);
    
    return [
        'status' => 'uspeh',
        'sporocilo' => 'Če email obstaja, smo poslali navodila za obnovitev gesla.'
    ];
}

function uporabnik_ponastavi_geslo(string $zeton, string $novoGeslo): array
{
    $uporabniki = baza_beri('uporabniki');
    $najden = null;
    
    foreach ($uporabniki as $uporabnik) {
        if (($uporabnik['obnovitveni_zeton'] ?? '') === $zeton) {
            if (($uporabnik['obnovitveni_zeton_cas'] ?? 0) > time()) {
                $najden = $uporabnik;
                break;
            }
        }
    }
    
    if (!$najden) {
        return [
            'status' => 'napaka',
            'sporocilo' => 'Povezava za obnovitev gesla je neveljavna ali je potekla.'
        ];
    }
    
    // Preveri moč gesla
    if (strlen($novoGeslo) < 8) {
        return [
            'status' => 'napaka',
            'sporocilo' => 'Geslo mora imeti vsaj 8 znakov.'
        ];
    }
    
    $noviHash = password_hash($novoGeslo, PASSWORD_BCRYPT);
    
    baza_posodobi('uporabniki', $najden['id'], [
        'hash_gesla' => $noviHash,
        'obnovitveni_zeton' => null,
        'obnovitveni_zeton_cas' => null
    ]);
    
    dogodek_sprozi('uporabnik.geslo_spremenjeno', [
        'uporabnik_id' => $najden['id'],
        'prek_obnovitve' => true
    ]);
    
    // Dodaj točke avatarju za skrbnost
    avatar_dodaj_tocke($najden['id'], 10, 'obnovitev_gesla');
    
    return [
        'status' => 'uspeh',
        'sporocilo' => 'Geslo uspešno spremenjeno. Sedaj se lahko prijavite.'
    ];
}