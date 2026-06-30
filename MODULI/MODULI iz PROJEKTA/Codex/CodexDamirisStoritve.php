<?php
/**
 * Codex Damiris - Storitve in Poslovna Logika
 * Lokacija: /var/www/html/codex-damiris/CodexDamirisStoritve.php
 */

class CodexDamirisStoritve {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Registriraj novega uporabnika
     */
    public function registrirajUporabnika($email, $geslo, $vzdevek, $telefon = null) {
        // Preveri veljavnost
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['uspeh' => false, 'napaka' => 'Neveljaven e-poštni naslov'];
        }
        
        if (strlen($geslo) < 8) {
            return ['uspeh' => false, 'napaka' => 'Geslo mora imeti vsaj 8 znakov'];
        }
        
        // Preveri ali email že obstaja
        $obstojec = $this->pridobiUporabnikaPoEmail($email);
        if ($obstojec) {
            return ['uspeh' => false, 'napaka' => 'E-poštni naslov je že v uporabi'];
        }
        
        // Ustvari uporabnika
        $hashGeslo = password_hash($geslo, PASSWORD_DEFAULT);
        $aktivacijskaKoda = bin2hex(random_bytes(16));
        
        $sql = "INSERT INTO uporabniki (email, geslo, vzdevek, telefon, aktivacijska_koda, nivo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $email, $hashGeslo, $vzdevek, $telefon, $aktivacijskaKoda, CodexDamirisPravila::OSNOVNI
            ]);
            
            // Pošlji aktivacijski email
            $this->posljiAktivacijskiEmail($email, $vzdevek, $aktivacijskaKoda);
            
            return [
                'uspeh' => true,
                'sporocilo' => 'Registracija uspešna. Preverite e-pošto za aktivacijo.'
            ];
            
        } catch (Exception $e) {
            return ['uspeh' => false, 'napaka' => 'Napaka pri registraciji: ' . $e->getMessage()];
        }
    }
    
    /**
     * Prijavi uporabnika
     */
    public function prijaviUporabnika($email, $geslo) {
        $uporabnik = $this->pridobiUporabnikaPoEmail($email);
        
        if (!$uporabnik) {
            return ['uspeh' => false, 'napaka' => 'Nepravilen e-poštni naslov'];
        }
        
        if (!$uporabnik['aktiviran']) {
            return ['uspeh' => false, 'napaka' => 'Račun ni aktiviran. Preverite e-pošto.'];
        }
        
        if (!password_verify($geslo, $uporabnik['geslo'])) {
            return ['uspeh' => false, 'napaka' => 'Nepravilno geslo'];
        }
        
        // Uspešna prijava
        $_SESSION['codex_uporabnik_id'] = $uporabnik['id'];
        $_SESSION['codex_uporabnik_nivo'] = $uporabnik['nivo'];
        $_SESSION['codex_zadnja_aktivnost'] = time();
        
        // Posodobi zadnjo prijavo
        $this->posodobiZadnjoPrijavo($uporabnik['id']);
        
        return [
            'uspeh' => true,
            'uporabnik' => [
                'id' => $uporabnik['id'],
                'vzdevek' => $uporabnik['vzdevek'],
                'nivo' => $uporabnik['nivo']
            ]
        ];
    }
    
    /**
     * Aktiviraj uporabniški račun
     */
    public function aktivirajRacun($aktivacijskaKoda) {
        $uporabnik = $this->pridobiUporabnikaPoAktivacijskiKodi($aktivacijskaKoda);
        
        if (!$uporabnik) {
            return ['uspeh' => false, 'napaka' => 'Neveljavna aktivacijska koda'];
        }
        
        if ($uporabnik['aktiviran']) {
            return ['uspeh' => false, 'napaka' => 'Račun je že aktiviran'];
        }
        
        // Aktiviraj račun
        $this->aktivirajUporabnika($uporabnik['id']);
        
        return [
            'uspeh' => true,
            'uporabnik' => [
                'vzdevek' => $uporabnik['vzdevek'],
                'email' => $uporabnik['email']
            ]
        ];
    }
    
    /**
     * Dodaj novo vsebino
     */
    public function dodajVsebino($naslov, $vsebina, $kategorija, $avtorId) {
        $napake = CodexDamirisFunkcije::validirajVsebino($naslov, $vsebina);
        if (!empty($napake)) {
            return ['uspeh' => false, 'napaka' => implode(', ', $napake)];
        }
        
        // Pridobi ID kategorije
        $kategorijaId = $this->pridobiKategorijoId($kategorija);
        if (!$kategorijaId) {
            return ['uspeh' => false, 'napaka' => 'Neveljavna kategorija'];
        }
        
        $sql = "INSERT INTO vsebine (kategorija_id, naslov, vsebina, avtor_id, status) 
                VALUES (?, ?, ?, ?, 'objavljeno')";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kategorijaId, $naslov, $vsebina, $avtorId]);
            
            $vsebinaId = $this->pdo->lastInsertId();
            
            return [
                'uspeh' => true,
                'vsebina_id' => $vsebinaId,
                'sporocilo' => 'Vsebina uspešno dodana'
            ];
            
        } catch (Exception $e) {
            return ['uspeh' => false, 'napaka' => 'Napaka pri dodajanju vsebine: ' . $e->getMessage()];
        }
    }
    
    /**
     * Dodaj zaznamek
     */
    public function dodajZaznamek($vsebinaId, $uporabnikId, $opomba = '') {
        // Preveri ali zaznamek že obstaja
        $obstojec = $this->pridobiZaznamek($uporabnikId, $vsebinaId);
        
        if ($obstojec) {
            // Posodobi obstoječi zaznamek
            $this->posodobiZaznamek($obstojec['id'], $opomba);
            return ['uspeh' => true, 'akcija' => 'posodobljen'];
        } else {
            // Dodaj nov zaznamek
            $zaznamekId = $this->ustvariZaznamek($uporabnikId, $vsebinaId, $opomba);
            return ['uspeh' => true, 'akcija' => 'dodan', 'zaznamek_id' => $zaznamekId];
        }
    }
    
    /**
     * Povečaj števec pogledov
     */
    public function povecajPoglede($vsebinaId, $uporabnikId = null) {
        $sql = "INSERT INTO pogledi (vsebina_id, uporabnik_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$vsebinaId, $uporabnikId]);
    }
    
    // POMOŽNE METODE ZA BAZO
    private function pridobiUporabnikaPoEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM uporabniki WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    private function pridobiUporabnikaPoAktivacijskiKodi($koda) {
        $stmt = $this->pdo->prepare("SELECT * FROM uporabniki WHERE aktivacijska_koda = ?");
        $stmt->execute([$koda]);
        return $stmt->fetch();
    }
    
    private function posodobiZadnjoPrijavo($uporabnikId) {
        $stmt = $this->pdo->prepare("UPDATE uporabniki SET zadnja_prijava = NOW() WHERE id = ?");
        $stmt->execute([$uporabnikId]);
    }
    
    private function aktivirajUporabnika($uporabnikId) {
        $stmt = $this->pdo->prepare("UPDATE uporabniki SET aktiviran = 1, aktivacijska_koda = NULL WHERE id = ?");
        $stmt->execute([$uporabnikId]);
    }
    
    private function pridobiKategorijoId($kategorija) {
        $stmt = $this->pdo->prepare("SELECT id FROM kategorije WHERE ime = ?");
        $stmt->execute([$kategorija]);
        return $stmt->fetchColumn();
    }
    
    private function pridobiZaznamek($uporabnikId, $vsebinaId) {
        $stmt = $this->pdo->prepare("SELECT * FROM zaznamki WHERE uporabnik_id = ? AND vsebina_id = ?");
        $stmt->execute([$uporabnikId, $vsebinaId]);
        return $stmt->fetch();
    }
    
    private function posodobiZaznamek($zaznamekId, $opomba) {
        $stmt = $this->pdo->prepare("UPDATE zaznamki SET opomba = ?, cas = NOW() WHERE id = ?");
        $stmt->execute([$opomba, $zaznamekId]);
    }
    
    private function ustvariZaznamek($uporabnikId, $vsebinaId, $opomba) {
        $stmt = $this->pdo->prepare("INSERT INTO zaznamki (uporabnik_id, vsebina_id, opomba) VALUES (?, ?, ?)");
        $stmt->execute([$uporabnikId, $vsebinaId, $opomba]);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Pošlji aktivacijski email
     */
    private function posljiAktivacijskiEmail($email, $vzdevek, $koda) {
        $nastavitve = CodexDamirisPravila::pridobiNastavitve();
        $url = $nastavitve['url'] . "/aktivacija?koda=" . $koda;
        
        $zadeva = "Aktivacija računa - Codex Damiris";
        $sporocilo = "
            Pozdravljeni {$vzdevek}!
            
            Hvala za registracijo v Codex Damiris.
            
            Za aktivacijo računa kliknite na povezavo:
            {$url}
            
            Lep pozdrav,
            Ekipa Codex Damiris
        ";
        
        // V razvojnem okolju le prikaži povezavo
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            error_log("Aktivacijska povezava za {$email}: {$url}");
        } else {
            mail($email, $zadeva, $sporocilo);
        }
    }
}
?>