<?php
/**
 * Codex Damiris - Glavni Frontend Controller + API
 * Lokacija: /var/www/html/codex-damiris/CodexDamiris.php
 */

class CodexDamiris {
    private $pdo;
    private $ai;
    private $trenutniUporabnik;
    
    public function __construct() {
        $this->poveziZBazo();
        $this->ai = new AI_CodexDamiris($this->pdo);
        $this->naloziTrenutnegaUporabnika();
    }
    
    /**
     * Vzpostavi povezavo z bazo
     */
    private function poveziZBazo() {
        $nastavitve = CodexDamirisJedro::pridobiNastavitve();
        $baza = $nastavitve['baza'];
        
        try {
            $this->pdo = new PDO(
                CodexDamirisJedro::pridobiBazaDsn(),
                $baza['uporabnik'],
                $baza['geslo'],
                CodexDamirisJedro::pridobiBazaNastavitve()
            );
        } catch (PDOException $e) {
            die("Napaka pri povezavi z bazo: " . $e->getMessage());
        }
    }
    
    /**
     * Naloži trenutnega uporabnika
     */
    private function naloziTrenutnegaUporabnika() {
        if (isset($_SESSION['codex_uporabnik_id'])) {
            $uporabnik = $this->pridobiUporabnika($_SESSION['codex_uporabnik_id']);
            if ($uporabnik && $uporabnik['aktiviran']) {
                $this->trenutniUporabnik = $uporabnik;
                return;
            }
        }
        
        // Nastavi gosta
        $this->trenutniUporabnik = [
            'id' => 0,
            'nivo' => CodexDamirisJedro::GOST,
            'vzdevek' => 'Gost'
        ];
    }
    
    /**
     * Glavna metoda za obdelavo zahtev
     */
    public function obdelajZahtevo() {
        $akcija = $_GET['akcija'] ?? $_POST['akcija'] ?? 'prikaziDomov';
        $jeApi = strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
        
        if ($jeApi) {
            $this->obdelajApiZahtevo($akcija);
        } else {
            $this->obdelajFrontendZahtevo($akcija);
        }
    }
    
    /**
     * Obdelaj API zahtevo
     */
    private function obdelajApiZahtevo($akcija) {
        CodexDamirisFunkcije::preveriCSRF();
        
        switch ($akcija) {
            case 'prijava':
                $this->apiPrijava();
                break;
                
            case 'registracija':
                $this->apiRegistracija();
                break;
                
            case 'dodajVsebino':
                $this->apiDodajVsebino();
                break;
                
            case 'dodajZaznamek':
                $this->apiDodajZaznamek();
                break;
                
            case 'aiChat':
                $this->apiAiChat();
                break;
                
            case 'iskanje':
                $this->apiIskanje();
                break;
                
            default:
                CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Neznana API akcija');
        }
    }
    
    /**
     * Obdelaj frontend zahtevo
     */
    private function obdelajFrontendZahtevo($akcija) {
        switch ($akcija) {
            case 'prikaziDomov':
                $this->prikaziDomov();
                break;
                
            case 'prikaziPrijava':
                $this->prikaziPrijavaObrazec();
                break;
                
            case 'prikaziRegistracija':
                $this->prikaziRegistracijaObrazec();
                break;
                
            case 'obdelajPrijava':
                $this->obdelajPrijava();
                break;
                
            case 'obdelajRegistracija':
                $this->obdelajRegistracija();
                break;
                
            case 'odjava':
                $this->obdelajOdjava();
                break;
                
            case 'aktivacija':
                $this->obdelajAktivacija();
                break;
                
            case 'iskanje':
                $this->prikaziRezultateIskanja();
                break;
                
            default:
                $this->prikaziDomov();
        }
    }
    
    /**
     * API: Prijava uporabnika
     */
    private function apiPrijava() {
        $email = $_POST['email'] ?? '';
        $geslo = $_POST['geslo'] ?? '';
        
        if (empty($email) || empty($geslo)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Manjkajoči podatki');
        }
        
        $uporabnik = $this->pridobiUporabnikaPoEmail($email);
        
        if (!$uporabnik || !$uporabnik['aktiviran']) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Nepravilen e-poštni naslov ali račun ni aktiviran');
        }
        
        if (!password_verify($geslo, $uporabnik['geslo'])) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Nepravilno geslo');
        }
        
        // Uspešna prijava
        $_SESSION['codex_uporabnik_id'] = $uporabnik['id'];
        $_SESSION['codex_uporabnik_nivo'] = $uporabnik['nivo'];
        
        CodexDamirisFunkcije::pripraviApiOdgovor(true, [
            'uporabnik' => [
                'id' => $uporabnik['id'],
                'vzdevek' => $uporabnik['vzdevek'],
                'nivo' => $uporabnik['nivo']
            ]
        ]);
    }
    
    /**
     * API: Registracija uporabnika
     */
    private function apiRegistracija() {
        $email = $_POST['email'] ?? '';
        $geslo = $_POST['geslo'] ?? '';
        $vzdevek = $_POST['vzdevek'] ?? '';
        
        if (empty($email) || empty($geslo) || empty($vzdevek)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Manjkajoči podatki');
        }
        
        if (!CodexDamirisFunkcije::preveriEmail($email)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Neveljaven e-poštni naslov');
        }
        
        if (!CodexDamirisFunkcije::preveriGeslo($geslo)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Geslo mora vsebovati vsaj 8 znakov');
        }
        
        // Preveri ali email že obstaja
        if ($this->pridobiUporabnikaPoEmail($email)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'E-poštni naslov je že v uporabi');
        }
        
        // Ustvari uporabnika
        $hashGeslo = password_hash($geslo, PASSWORD_DEFAULT);
        $aktivacijskaKoda = CodexDamirisFunkcije::generirajToken();
        
        $stmt = $this->pdo->prepare(
            "INSERT INTO uporabniki (email, geslo, vzdevek, aktivacijska_koda, nivo) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        try {
            $stmt->execute([
                $email, $hashGeslo, $vzdevek, $aktivacijskaKoda, CodexDamirisJedro::OSNOVNI
            ]);
            
            $uporabnikId = $this->pdo->lastInsertId();
            
            CodexDamirisFunkcije::pripraviApiOdgovor(true, [
                'uporabnik_id' => $uporabnikId,
                'sporocilo' => 'Registracija uspešna. Preverite e-pošto za aktivacijo.'
            ]);
            
        } catch (Exception $e) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Napaka pri registraciji: ' . $e->getMessage());
        }
    }
    
    /**
     * API: Dodaj vsebino
     */
    private function apiDodajVsebino() {
        if (!$this->jePrijavljen()) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Za dodajanje vsebin se morate prijaviti');
        }
        
        $naslov = $_POST['naslov'] ?? '';
        $vsebina = $_POST['vsebina'] ?? '';
        $kategorija = $_POST['kategorija'] ?? '';
        
        $napake = CodexDamirisFunkcije::validirajVsebino($naslov, $vsebina);
        if (!empty($napake)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], implode(', ', $napake));
        }
        
        // Pridobi ID kategorije
        $kategorijaRow = $this->pdo->prepare(
            "SELECT id FROM kategorije WHERE ime = ?"
        );
        $kategorijaRow->execute([$kategorija]);
        $kategorijaId = $kategorijaRow->fetchColumn();
        
        if (!$kategorijaId) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Neveljavna kategorija');
        }
        
        $stmt = $this->pdo->prepare(
            "INSERT INTO vsebine (kategorija_id, naslov, vsebina, avtor_id, status) 
             VALUES (?, ?, ?, ?, 'objavljeno')"
        );
        
        try {
            $stmt->execute([
                $kategorijaId, $naslov, $vsebina, $this->trenutniUporabnik['id']
            ]);
            
            $vsebinaId = $this->pdo->lastInsertId();
            
            CodexDamirisFunkcije::pripraviApiOdgovor(true, [
                'vsebina_id' => $vsebinaId,
                'sporocilo' => 'Vsebina uspešno dodana'
            ]);
            
        } catch (Exception $e) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Napaka pri dodajanju vsebine: ' . $e->getMessage());
        }
    }
    
    /**
     * API: AI Chat
     */
    private function apiAiChat() {
        if (!$this->jePrijavljen()) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Za AI chat se morate prijaviti');
        }
        
        $sporocilo = $_POST['sporocilo'] ?? '';
        
        if (empty($sporocilo)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Prazno sporočilo');
        }
        
        $rezultat = $this->ai->chat($sporocilo, $this->trenutniUporabnik['nivo']);
        
        if ($rezultat['uspeh']) {
            CodexDamirisFunkcije::pripraviApiOdgovor(true, [
                'odgovor' => $rezultat['odgovor']
            ]);
        } else {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], $rezultat['napaka']);
        }
    }
    
    /**
     * API: Iskanje
     */
    private function apiIskanje() {
        $poizvedba = $_GET['q'] ?? '';
        $kategorija = $_GET['kategorija'] ?? null;
        
        if (empty($poizvedba)) {
            CodexDamirisFunkcije::pripraviApiOdgovor(false, [], 'Prazna poizvedba');
        }
        
        $rezultati = $this->iskanje($poizvedba, $kategorija);
        
        CodexDamirisFunkcije::pripraviApiOdgovor(true, [
            'rezultati' => $rezultati,
            'stevilo' => count($rezultati)
        ]);
    }
    
    /**
     * Prikaži domačo stran
     */
    public function prikaziDomov() {
        $priljubljene = $this->pridobiPriljubljeneVsebine(6);
        $vsebine = $this->pridobiVsebine(null, 12);
        $kategorije = CodexDamirisJedro::pridobiKategorije();
        
        $this->prikaziFrontend('domaca', [
            'priljubljene' => $priljubljene,
            'vsebine' => $vsebine,
            'kategorije' => $kategorije
        ]);
    }
    
    /**
     * Prikaži rezultate iskanja
     */
    public function prikaziRezultateIskanja() {
        $poizvedba = $_GET['q'] ?? '';
        $kategorija = $_GET['kategorija'] ?? null;
        
        $rezultati = [];
        if (!empty($poizvedba)) {
            $rezultati = $this->iskanje($poizvedba, $kategorija);
        }
        
        $kategorije = CodexDamirisJedro::pridobiKategorije();
        
        $this->prikaziFrontend('iskanje', [
            'poizvedba' => $poizvedba,
            'rezultati' => $rezultati,
            'kategorije' => $kategorije,
            'izbranaKategorija' => $kategorija
        ]);
    }
    
    /**
     * Pomožne metode za delo z bazo
     */
    private function pridobiUporabnika($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM uporabniki WHERE id = ? AND aktiviran = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    private function pridobiUporabnikaPoEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM uporabniki WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function jePrijavljen() {
        return $this->trenutniUporabnik['id'] > 0;
    }
    
    public function pridobiTrenutnegaUporabnika() {
        return $this->trenutniUporabnik;
    }
    
    private function pridobiPriljubljeneVsebine($limit = 10) {
        $sql = "
            SELECT v.*, k.ime as kategorija_ime, u.vzdevek as avtor_ime,
                   COUNT(p.id) as stevilo_pogledov
            FROM vsebine v 
            LEFT JOIN kategorije k ON v.kategorija_id = k.id 
            LEFT JOIN uporabniki u ON v.avtor_id = u.id 
            LEFT JOIN pogledi p ON v.id = p.vsebina_id
            WHERE v.status = 'objavljeno'
            GROUP BY v.id
            ORDER BY stevilo_pogledov DESC
            LIMIT ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    private function pridobiVsebine($kategorija = null, $limit = 50) {
        $sql = "
            SELECT v.*, k.ime as kategorija_ime, u.vzdevek as avtor_ime
            FROM vsebine v 
            LEFT JOIN kategorije k ON v.kategorija_id = k.id 
            LEFT JOIN uporabniki u ON v.avtor_id = u.id 
            WHERE v.status = 'objavljeno'
        ";
        
        $parametri = [];
        
        if ($kategorija) {
            $sql .= " AND k.ime = ?";
            $parametri[] = $kategorija;
        }
        
        $sql .= " ORDER BY v.datum_posodobitve DESC LIMIT ?";
        $parametri[] = $limit;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametri);
        return $stmt->fetchAll();
    }
    
    private function iskanje($poizvedba, $kategorija = null) {
        $sql = "
            SELECT v.*, k.ime as kategorija_ime, u.vzdevek as avtor_ime,
                   MATCH(v.naslov, v.vsebina) AGAINST(? IN NATURAL LANGUAGE MODE) as ujemanje
            FROM vsebine v 
            LEFT JOIN kategorije k ON v.kategorija_id = k.id 
            LEFT JOIN uporabniki u ON v.avtor_id = u.id 
            WHERE (v.naslov LIKE ? OR v.vsebina LIKE ? OR k.ime LIKE ?)
            AND v.status = 'objavljeno'
        ";
        
        $parametri = [
            $poizvedba,
            "%$poizvedba%",
            "%$poizvedba%",
            "%$poizvedba%"
        ];
        
        if ($kategorija) {
            $sql .= " AND k.ime = ?";
            $parametri[] = $kategorija;
        }
        
        $sql .= " ORDER BY ujemanje DESC LIMIT 50";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametri);
        return $stmt->fetchAll();
    }
    
    /**
     * Prikaži frontend
     */
    private function prikaziFrontend($pogled, $podatki = []) {
        extract($podatki);
        
        // Vključi frontend datoteko
        $frontendPot = __DIR__ . '/Elementi/frontend.php';
        if (file_exists($frontendPot)) {
            include $frontendPot;
        } else {
            // Privzeti frontend
            $this->prikaziPrivzetiFrontend($pogled, $podatki);
        }
    }
    
    /**
     * Privzeti frontend
     */
    private function prikaziPrivzetiFrontend($pogled, $podatki) {
        extract($podatki);
        $trenutniUporabnik = $this->trenutniUporabnik;
        $csrfToken = CodexDamirisFunkcije::generirajCSRF();
        $sporocila = CodexDamirisFunkcije::pridobiSporocila();
        ?>
        
        <!-- CODEX DAMIRIS FRONTEND -->
        <div class="codex-damiris-container">
            
            <!-- Sporočila -->
            <?php foreach ($sporocila as $sporocilo): ?>
                <div class="codex-sporocilo codex-sporocilo-<?= $sporocilo['tip'] ?>">
                    <?= CodexDamirisFunkcije::varniIzhod($sporocilo['vsebina']) ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Iskalnik -->
            <div class="codex-iskanje">
                <form action="" method="get">
                    <input type="hidden" name="akcija" value="iskanje">
                    <div class="codex-iskanje-vrstica">
                        <input type="text" name="q" value="<?= $poizvedba ?? '' ?>" 
                               placeholder="Išči modrost v Codexu..." class="codex-iskanje-polje">
                        <select name="kategorija" class="codex-iskanje-kategorija">
                            <option value="">Vse kategorije</option>
                            <?php foreach ($kategorije as $kljuc => $kat): ?>
                                <option value="<?= $kljuc ?>" <?= ($kategorija ?? '') == $kljuc ? 'selected' : '' ?>>
                                    <?= $kat['ime'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="codex-iskanje-gumb">Išči</button>
                    </div>
                </form>
            </div>
            
            <?php if ($pogled === 'iskanje'): ?>
                
                <!-- Rezultati iskanja -->
                <div class="codex-rezultati">
                    <h2>Rezultati iskanja za "<?= CodexDamirisFunkcije::varniIzhod($poizvedba) ?>"</h2>
                    
                    <?php if (empty($rezultati)): ?>
                        <p>Ni najdenih rezultatov.</p>
                    <?php else: ?>
                        <div class="codex-vsebine-seznam">
                            <?php foreach ($rezultati as $vsebina): ?>
                                <div class="codex-vsebina-predogled">
                                    <h3><?= CodexDamirisFunkcije::varniIzhod($vsebina['naslov']) ?></h3>
                                    <div class="codex-vsebina-meta">
                                        <span class="codex-kategorija"><?= $vsebina['kategorija_ime'] ?></span>
                                        <span class="codex-avtor"><?= $vsebina['avtor_ime'] ?></span>
                                        <span class="codex-datum"><?= CodexDamirisFunkcije::lepDatum($vsebina['datum_posodobitve']) ?></span>
                                    </div>
                                    <p><?= CodexDamirisFunkcije::skrajsajBesedilo($vsebina['vsebina']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php else: ?>
                
                <!-- Domača stran -->
                <div class="codex-domaca-stran">
                    
                    <!-- Priljubljene vsebine -->
                    <?php if (!empty($priljubljene)): ?>
                        <section class="codex-priljubljene">
                            <h2>Priljubljena znanja</h2>
                            <div class="codex-vsebine-mreza">
                                <?php foreach ($priljubljene as $vsebina): ?>
                                    <div class="codex-vsebina-kartica">
                                        <h3><?= CodexDamirisFunkcije::varniIzhod($vsebina['naslov']) ?></h3>
                                        <div class="codex-vsebina-meta">
                                            <span class="codex-kategorija"><?= $vsebina['kategorija_ime'] ?></span>
                                        </div>
                                        <p><?= CodexDamirisFunkcije::skrajsajBesedilo($vsebina['vsebina'], 100) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- Vse kategorije -->
                    <section class="codex-kategorije">
                        <h2>Razišči kategorije</h2>
                        <div class="codex-kategorije-mreza">
                            <?php foreach ($kategorije as $kljuc => $kategorija): ?>
                                <div class="codex-kategorija-kartica" style="border-left-color: <?= $kategorija['barva'] ?>">
                                    <h3><?= $kategorija['ime'] ?></h3>
                                    <p><?= $kategorija['opis'] ?></p>
                                    <a href="?akcija=iskanje&kategorija=<?= $kljuc ?>" 
                                       class="codex-raziskuj-gumb">
                                        Raziskuj
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    
                </div>
                
            <?php endif; ?>
            
        </div>
        
        <style>
        .codex-damiris-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .codex-sporocilo {
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        
        .codex-sporocilo-uspeh { background: #d4edda; color: #155724; }
        .codex-sporocilo-napaka { background: #f8d7da; color: #721c24; }
        .codex-sporocilo-opozorilo { background: #fff3cd; color: #856404; }
        .codex-sporocilo-info { background: #d1ecf1; color: #0c5460; }
        
        .codex-iskanje {
            margin: 30px 0;
        }
        
        .codex-iskanje-vrstica {
            display: flex;
            gap: 10px;
        }
        
        .codex-iskanje-polje {
            flex: 1;
            padding: 10px;
            border: 2px solid #667eea;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .codex-iskanje-kategorija {
            padding: 10px;
            border: 2px solid #667eea;
            border-radius: 5px;
            background: white;
        }
        
        .codex-iskanje-gumb {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .codex-vsebine-mreza {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .codex-vsebina-kartica {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .codex-kategorije-mreza {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .codex-kategorija-kartica {
            border-left: 4px solid #667eea;
            padding: 15px;
            background: white;
            border-radius: 0 5px 5px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .codex-vsebina-meta {
            font-size: 0.9em;
            color: #666;
            margin: 5px 0;
        }
        
        .codex-vsebina-meta span {
            margin-right: 10px;
        }
        
        .codex-raziskuj-gumb {
            display: inline-block;
            padding: 5px 15px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 10px;
        }
        </style>
        <?php
    }
    
    // Preostale metode za frontend...
    private function prikaziPrijavaObrazec() { /* Implementacija */ }
    private function prikaziRegistracijaObrazec() { /* Implementacija */ }
    private function obdelajPrijava() { /* Implementacija */ }
    private function obdelajRegistracija() { /* Implementacija */ }
    private function obdelajOdjava() { /* Implementacija */ }
    private function obdelajAktivacija() { /* Implementacija */ }
    private function apiDodajZaznamek() { /* Implementacija */ }
}
?>