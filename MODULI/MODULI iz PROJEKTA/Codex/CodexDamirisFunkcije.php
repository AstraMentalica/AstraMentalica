<?php
/**
 * Codex Damiris - Pomožne Funkcije
 * Lokacija: /var/www/html/codex-damiris/CodexDamirisFunkcije.php
 */

class CodexDamirisFunkcije {
    
    /**
     * Preveri veljavnost e-pošte
     */
    public static function preveriEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Preveri moč gesla
     */
    public static function preveriGeslo($geslo) {
        return strlen($geslo) >= 8;
    }
    
    /**
     * Generiraj naključni token
     */
    public static function generirajToken($dolzina = 32) {
        return bin2hex(random_bytes($dolzina));
    }
    
    /**
     * Zaščiti izhod pred XSS
     */
    public static function varniIzhod($besedilo) {
        return htmlspecialchars($besedilo, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Formatiraj datum
     */
    public static function lepDatum($datum) {
        $date = new DateTime($datum);
        $slovenskiMeseci = [
            'January' => 'januar', 'February' => 'februar', 'March' => 'marec',
            'April' => 'april', 'May' => 'maj', 'June' => 'junij',
            'July' => 'julij', 'August' => 'avgust', 'September' => 'september',
            'October' => 'oktober', 'November' => 'november', 'December' => 'december'
        ];
        
        $angleksiMesec = $date->format('F');
        $slovenskiMesec = $slovenskiMeseci[$angleksiMesec] ?? $angleksiMesec;
        
        return $date->format('j. ') . $slovenskiMesec . $date->format(' Y ob H:i');
    }
    
    /**
     * Skrajšaj besedilo
     */
    public static function skrajsajBesedilo($besedilo, $dolzina = 150) {
        if (strlen($besedilo) <= $dolzina) {
            return $besedilo;
        }
        
        $skrajsano = substr($besedilo, 0, $dolzina);
        $zadnjiPresledek = strrpos($skrajsano, ' ');
        
        if ($zadnjiPresledek !== false) {
            $skrajsano = substr($skrajsano, 0, $zadnjiPresledek);
        }
        
        return $skrajsano . '...';
    }
    
    /**
     * Preusmeri na drugo stran
     */
    public static function preusmeri($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Prikaži sporočilo
     */
    public static function prikaziSporocilo($tip, $sporocilo) {
        $_SESSION['codex_sporocila'][] = [
            'tip' => $tip, // uspeh, napaka, opozorilo, info
            'vsebina' => $sporocilo
        ];
    }
    
    /**
     * Pridobi prikazana sporočila
     */
    public static function pridobiSporocila() {
        $sporocila = $_SESSION['codex_sporocila'] ?? [];
        unset($_SESSION['codex_sporocila']);
        return $sporocila;
    }
    
    /**
     * Preveri CSRF token
     */
    public static function preveriCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!$token || $token !== ($_SESSION['codex_csrf_token'] ?? '')) {
                die('Neveljaven CSRF token');
            }
        }
    }
    
    /**
     * Generiraj CSRF token
     */
    public static function generirajCSRF() {
        if (empty($_SESSION['codex_csrf_token'])) {
            $_SESSION['codex_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['codex_csrf_token'];
    }
    
    /**
     * Logiraj dejanje
     */
    public static function logiraj($dejanje, $uporabnikId = null) {
        $logVrstica = date('Y-m-d H:i:s') . " | " . ($uporabnikId ?? 'Anonimen') . " | " . $dejanje . "\n";
        file_put_contents(__DIR__ . '/codex_damiris_log.txt', $logVrstica, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Pripravi odgovor za API
     */
    public static function pripraviApiOdgovor($uspeh, $podatki = [], $napaka = null) {
        header('Content-Type: application/json');
        
        $odgovor = ['uspeh' => $uspeh];
        
        if ($uspeh) {
            $odgovor['podatki'] = $podatki;
        } else {
            $odgovor['napaka'] = $napaka;
        }
        
        echo json_encode($odgovor);
        exit;
    }
    
    /**
     * Validiraj vnos za vsebino
     */
    public static function validirajVsebino($naslov, $vsebina) {
        $napake = [];
        
        if (strlen(trim($naslov)) < 3) {
            $napake[] = 'Naslov mora vsebovati vsaj 3 znake';
        }
        
        if (strlen(trim($vsebina)) < 10) {
            $napake[] = 'Vsebina mora vsebovati vsaj 10 znakov';
        }
        
        return $napake;
    }
}
?>