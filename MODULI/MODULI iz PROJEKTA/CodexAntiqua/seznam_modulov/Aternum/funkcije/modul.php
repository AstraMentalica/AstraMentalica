<?php
/**
 * DATOTEKA: modul.php
 * NAMEN:    Glavna logika modula Aeternum — knjižnica z iskanjem, branjem, zapiski
 * NIVO:     2 (modulna logika)
 * ODVISNO:  SISTEM/sistem/baze/shramba.php, SISTEM/sistem/jedro/02_varnost.php
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

// ============================
// VSTOPNA TOČKA MODULA
// ============================

function modulAeternumAkcija(string $akcija, array $podatki = []): array {
    return match($akcija) {
        // 3a: Osnovna knjižnica
        'seznam' => aeternumSeznam($podatki),
        'preberi' => aeternumPreberi($podatki),
        'dodaj' => aeternumDodaj($podatki),
        'posodobi' => aeternumPosodobi($podatki),
        'izbrisi' => aeternumIzbrisi($podatki),
        
        // 3b: Iskanje
        'iskanje' => aeternumIskanje($podatki),
        
        // 3c + 3d: Glasovno
        'glas_shrani' => aeternumGlasShrani($podatki),
        'glas_prepis' => aeternumGlasPrepisi($podatki),
        'glas_beri' => aeternumGlasBeri($podatki),
        
		// Otroška verzija (3f)
'puerilis_seznam' => aeternumPuerilisSeznam($podatki),
'puerilis_zgodba_dneva' => aeternumPuerilisZgodbaDneva($podatki),
'puerilis_vecemi_nacin' => aeternumPuerilisVecemiNacin($podatki),
'puerilis_zvocna_slikica' => aeternumPuerilisZvocnaSlikica($podatki),

        // Zapisi uporabnikov
        'zapisek_dodaj' => aeternumZapisekDodaj($podatki),
        'zapisek_preberi' => aeternumZapisekPreberi($podatki),
        
        default => ['napaka' => 'Neznana akcija: ' . $akcija]
    };
}

// ============================
// POMOŽNE FUNKCIJE
// ============================

function aeternumPovezava() {
    $bazaPot = MODULI_POT . '/osnovni/Aeternum/.baza/aeternum.sqlite';
    
    // Ustvari mapo če ne obstaja
    $mapa = dirname($bazaPot);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    
    // Če baza ne obstaja, jo ustvari s shemo
    $jeNova = !file_exists($bazaPot);
    
    $pdo = new PDO('sqlite:' . $bazaPot);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    if ($jeNova) {
        $shema = file_get_contents(dirname($bazaPot) . '/shema.sql');
        $pdo->exec($shema);
    }
    
    return $pdo;
}

function aeternumSanitizirajVnos($vnos) {
    if (is_string($vnos)) {
        return htmlspecialchars(trim($vnos), ENT_QUOTES, 'UTF-8');
    }
    return $vnos;
}

// ============================
// 3a: OSNOVNA KNJIŽNICA
// ============================

function aeternumSeznam(array $podatki): array {
    $stran = max(1, (int)($podatki['stran'] ?? 1));
    $naStran = min(50, (int)($podatki['na_stran'] ?? 20));
    $kategorija = $podatki['kategorija'] ?? '';
    $offset = ($stran - 1) * $naStran;
    
    $pdo = aeternumPovezava();
    
    // Gradimo poizvedbo
    $sql = "SELECT id, naslov, povzetek, kategorija, podkategorija, 
                   ogledi, ustvarjeno, posodobljeno 
            FROM vsebina WHERE 1=1";
    $params = [];
    
    if (!empty($kategorija)) {
        $sql .= " AND kategorija = :kategorija";
        $params[':kategorija'] = $kategorija;
    }
    
    $sql .= " ORDER BY ustvarjeno DESC LIMIT :limit OFFSET :offset";
    $params[':limit'] = $naStran;
    $params[':offset'] = $offset;
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    $vnosi = $stmt->fetchAll();
    
    // Skupaj vnosov
    $sqlSkupaj = "SELECT COUNT(*) as skupaj FROM vsebina";
    if (!empty($kategorija)) {
        $sqlSkupaj .= " WHERE kategorija = :kategorija";
    }
    $stmtSkupaj = $pdo->prepare($sqlSkupaj);
    if (!empty($kategorija)) {
        $stmtSkupaj->execute([':kategorija' => $kategorija]);
    } else {
        $stmtSkupaj->execute();
    }
    $skupaj = $stmtSkupaj->fetch()['skupaj'];
    
    return [
        'uspeh' => true,
        'vnosi' => $vnosi,
        'stran' => $stran,
        'na_stran' => $naStran,
        'skupaj' => (int)$skupaj,
        'strani' => ceil($skupaj / $naStran)
    ];
}

function aeternumPreberi(array $podatki): array {
    $id = (int)($podatki['id'] ?? 0);
    
    if ($id <= 0) {
        return ['napaka' => 'Neveljaven ID.'];
    }
    
    $pdo = aeternumPovezava();
    
    // Povečamo števec ogledov
    $pdo->prepare("UPDATE vsebina SET ogledi = ogledi + 1 WHERE id = :id")
        ->execute([':id' => $id]);
    
    $stmt = $pdo->prepare("SELECT * FROM vsebina WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $vnos = $stmt->fetch();
    
    if (!$vnos) {
        return ['napaka' => 'Vnos ne obstaja.'];
    }
    
    // Pretvori tags iz JSON v array
    $vnos['tags'] = json_decode($vnos['tags'] ?? '[]', true);
    
    return [
        'uspeh' => true,
        'vnos' => $vnos
    ];
}

function aeternumDodaj(array $podatki): array {
    // Preveri pravice (samo admin ali AI)
    $uporabnikId = (int)($_SESSION['uporabnik_id'] ?? 0);
    $vloga = (int)($_SESSION['vloga'] ?? 0);
    
    if ($uporabnikId !== 1 && $vloga < 100) {
        return ['napaka' => 'Nimate pravic za dodajanje vsebine.'];
    }
    
    $naslov = trim($podatki['naslov'] ?? '');
    $vsebina = trim($podatki['vsebina'] ?? '');
    $kategorija = trim($podatki['kategorija'] ?? 'splosno');
    $povzetek = trim($podatki['povzetek'] ?? '');
    $tags = $podatki['tags'] ?? [];
    $starostnaOmejitev = (int)($podatki['starostna_omejitev'] ?? 0);
    
    if (empty($naslov) || empty($vsebina)) {
        return ['napaka' => 'Naslov in vsebina sta obvezna.'];
    }
    
    $pdo = aeternumPovezava();
    $zdaj = date('Y-m-d H:i:s');
    
    $stmt = $pdo->prepare("
        INSERT INTO vsebina (naslov, vsebina, povzetek, kategorija, tags, 
                             starostna_omejitev, ustvarjeno, posodobljeno)
        VALUES (:naslov, :vsebina, :povzetek, :kategorija, :tags, 
                :starostna, :zdaj, :zdaj)
    ");
    
    $stmt->execute([
        ':naslov' => $naslov,
        ':vsebina' => $vsebina,
        ':povzetek' => $povzetek,
        ':kategorija' => $kategorija,
        ':tags' => json_encode($tags, JSON_UNESCAPED_UNICODE),
        ':starostna' => $starostnaOmejitev,
        ':zdaj' => $zdaj
    ]);
    
    return [
        'uspeh' => true,
        'id' => $pdo->lastInsertId(),
        'sporocilo' => 'Vsebina dodana.'
    ];
}

function aeternumPosodobi(array $podatki): array {
    $id = (int)($podatki['id'] ?? 0);
    
    if ($id <= 0) {
        return ['napaka' => 'Neveljaven ID.'];
    }
    
    $pdo = aeternumPovezava();
    
    // Preveri če obstaja
    $stmt = $pdo->prepare("SELECT id FROM vsebina WHERE id = :id");
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetch()) {
        return ['napaka' => 'Vnos ne obstaja.'];
    }
    
    // Zberemo polja za posodobitev
    $polja = [];
    $params = [':id' => $id];
    
    $dovoljenaPolja = ['naslov', 'vsebina', 'povzetek', 'kategorija', 'tags', 'starostna_omejitev'];
    foreach ($dovoljenaPolja as $polje) {
        if (isset($podatki[$polje])) {
            $polja[] = "$polje = :$polje";
            $params[":$polje"] = $polje === 'tags' ? json_encode($podatki[$polje]) : $podatki[$polje];
        }
    }
    
    if (empty($polja)) {
        return ['napaka' => 'Ni podatkov za posodobitev.'];
    }
    
    $polja[] = "posodobljeno = :zdaj";
    $params[':zdaj'] = date('Y-m-d H:i:s');
    
    $sql = "UPDATE vsebina SET " . implode(', ', $polja) . " WHERE id = :id";
    $pdo->prepare($sql)->execute($params);
    
    return [
        'uspeh' => true,
        'sporocilo' => 'Vsebina posodobljena.'
    ];
}

function aeternumIzbrisi(array $podatki): array {
    $id = (int)($podatki['id'] ?? 0);
    
    if ($id <= 0) {
        return ['napaka' => 'Neveljaven ID.'];
    }
    
    $pdo = aeternumPovezava();
    $stmt = $pdo->prepare("DELETE FROM vsebina WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    return [
        'uspeh' => true,
        'sporocilo' => 'Vsebina izbrisana.'
    ];
}

// ============================
// 3b: ISKANJE
// ============================

function aeternumIskanje(array $podatki): array {
    $poizvedba = trim($podatki['q'] ?? '');
    $stran = max(1, (int)($podatki['stran'] ?? 1));
    $naStran = min(50, (int)($podatki['na_stran'] ?? 20));
    
    if (empty($poizvedba)) {
        return ['rezultati' => [], 'skupaj' => 0];
    }
    
    // Sanitizacija za FTS5
    $iskalniNiz = str_replace(['"', "'", '*'], '', $poizvedba);
    $offset = ($stran - 1) * $naStran;
    
    $pdo = aeternumPovezava();
    
    // Full-text iskanje
    $sql = "
        SELECT v.id, v.naslov, v.povzetek, v.kategorija, 
               highlight(vsebina_fts, 1, '<mark>', '</mark>') as izsek
        FROM vsebina_fts fts
        JOIN vsebina v ON fts.rowid = v.id
        WHERE vsebina_fts MATCH :poizvedba
        ORDER BY rank
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':poizvedba', $iskalniNiz);
    $stmt->bindValue(':limit', $naStran, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rezultati = $stmt->fetchAll();
    
    // Število vseh zadetkov
    $stmtSkupaj = $pdo->prepare("
        SELECT COUNT(*) as skupaj 
        FROM vsebina_fts 
        WHERE vsebina_fts MATCH :poizvedba
    ");
    $stmtSkupaj->bindValue(':poizvedba', $iskalniNiz);
    $stmtSkupaj->execute();
    $skupaj = $stmtSkupaj->fetch()['skupaj'];
    
    return [
        'uspeh' => true,
        'rezultati' => $rezultati,
        'poizvedba' => $poizvedba,
        'stran' => $stran,
        'na_stran' => $naStran,
        'skupaj' => (int)$skupaj,
        'strani' => ceil($skupaj / $naStran)
    ];
}

// ============================
// 3c + 3d: GLASOVNO
// ============================

function aeternumGlasShrani(array $podatki): array {
    // Shrani posnetek (audio blob)
    $uporabnikId = (int)($_SESSION['uporabnik_id'] ?? 0);
    
    if ($uporabnikId <= 0) {
        return ['napaka' => 'Za glasovno beleženje se morate prijaviti.'];
    }
    
    $audioData = $podatki['audio'] ?? '';
    if (empty($audioData)) {
        return ['napaka' => 'Ni posnetka.'];
    }
    
    // Shrani audio datoteko
    $mediaMapa = VSEBINA_POT . '/glas/';
    if (!is_dir($mediaMapa)) {
        mkdir($mediaMapa, 0755, true);
    }
    
    $imeDatoteke = 'glas_' . $uporabnikId . '_' . time() . '.webm';
    $pot = $mediaMapa . $imeDatoteke;
    
    // Decode base64 audio
    $audioBin = base64_decode(preg_replace('#^data:audio/[^;]+;base64,#', '', $audioData));
    file_put_contents($pot, $audioBin);
    
    // Shrani v bazo
    $pdo = aeternumPovezava();
    $stmt = $pdo->prepare("
        INSERT INTO glasovni_dnevniki (uporabnik_id, posnetek, trajanje, ustvarjeno)
        VALUES (:uid, :posnetek, :trajanje, :zdaj)
    ");
    
    $stmt->execute([
        ':uid' => $uporabnikId,
        ':posnetek' => $imeDatoteke,
        ':trajanje' => (int)($podatki['trajanje'] ?? 0),
        ':zdaj' => date('Y-m-d H:i:s')
    ]);
    
    return [
        'uspeh' => true,
        'posnetek' => $imeDatoteke,
        'id' => $pdo->lastInsertId()
    ];
}

function aeternumGlasPrepisi(array $podatki): array {
    // Prepis govora v tekst (mock — kasneje Web Speech API ali Whisper)
    $prepis = $podatki['prepis'] ?? '';
    $id = (int)($podatki['id'] ?? 0);
    
    if ($id > 0 && !empty($prepis)) {
        $pdo = aeternumPovezava();
        $pdo->prepare("UPDATE glasovni_dnevniki SET prepis = :prepis WHERE id = :id")
            ->execute([':prepis' => $prepis, ':id' => $id]);
    }
    
    return [
        'uspeh' => true,
        'prepis' => $prepis,
        'sporocilo' => 'Prepis shranjen.'
    ];
}

function aeternumGlasBeri(array $podatki): array {
    // Vrne tekst za branje (Web Speech API bo bral na frontendu)
    $id = (int)($podatki['id'] ?? 0);
    
    if ($id <= 0) {
        return ['napaka' => 'Manjka ID vsebine za branje.'];
    }
    
    $pdo = aeternumPovezava();
    $stmt = $pdo->prepare("SELECT naslov, vsebina FROM vsebina WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $vnos = $stmt->fetch();
    
    if (!$vnos) {
        return ['napaka' => 'Vsebina ne obstaja.'];
    }
    
    return [
        'uspeh' => true,
        'naslov' => $vnos['naslov'],
        'vsebina' => $vnos['vsebina'],
        'za_glas' => $vnos['naslov'] . '. ' . strip_tags($vnos['vsebina'])
    ];
}

// ============================
// ZAPISKI UPORABNIKOV
// ============================

function aeternumZapisekDodaj(array $podatki): array {
    $uporabnikId = (int)($_SESSION['uporabnik_id'] ?? 0);
    
    if ($uporabnikId <= 0) {
        return ['napaka' => 'Za dodajanje zapiskov se morate prijaviti.'];
    }
    
    $vsebinaId = (int)($podatki['vsebina_id'] ?? 0);
    $zapisek = trim($podatki['zapisek'] ?? '');
    
    if ($vsebinaId <= 0 || empty($zapisek)) {
        return ['napaka' => 'Manjkajo podatki.'];
    }
    
    $pdo = aeternumPovezava();
    
    // Preveri če že obstaja
    $stmt = $pdo->prepare("
        SELECT id FROM uporabniski_zapiski 
        WHERE uporabnik_id = :uid AND vsebina_id = :vid
    ");
    $stmt->execute([':uid' => $uporabnikId, ':vid' => $vsebinaId]);
    $obstojec = $stmt->fetch();
    
    if ($obstojec) {
        // Posodobi
        $stmt = $pdo->prepare("
            UPDATE uporabniski_zapiski 
            SET zapisek = :zapisek 
            WHERE uporabnik_id = :uid AND vsebina_id = :vid
        ");
    } else {
        // Nov
        $stmt = $pdo->prepare("
            INSERT INTO uporabniski_zapiski (uporabnik_id, vsebina_id, zapisek, ustvarjeno)
            VALUES (:uid, :vid, :zapisek, :zdaj)
        ");
        $stmt->bindValue(':zdaj', date('Y-m-d H:i:s'));
    }
    
    $stmt->bindValue(':uid', $uporabnikId);
    $stmt->bindValue(':vid', $vsebinaId);
    $stmt->bindValue(':zapisek', $zapisek);
    $stmt->execute();
    
    return [
        'uspeh' => true,
        'sporocilo' => 'Zapisek shranjen.'
    ];
}

function aeternumZapisekPreberi(array $podatki): array {
    $uporabnikId = (int)($_SESSION['uporabnik_id'] ?? 0);
    
    if ($uporabnikId <= 0) {
        return ['napaka' => 'Za branje zapiskov se morate prijaviti.'];
    }
    
    $vsebinaId = (int)($podatki['vsebina_id'] ?? 0);
    
    $pdo = aeternumPovezava();
    
    if ($vsebinaId > 0) {
        // En določen zapisek
        $stmt = $pdo->prepare("
            SELECT zapisek, ustvarjeno 
            FROM uporabniski_zapiski 
            WHERE uporabnik_id = :uid AND vsebina_id = :vid
        ");
        $stmt->execute([':uid' => $uporabnikId, ':vid' => $vsebinaId]);
        $zapisek = $stmt->fetch();
        
        return [
            'uspeh' => true,
            'zapisek' => $zapisek['zapisek'] ?? '',
            'obstaja' => !empty($zapisek)
        ];
    } else {
        // Vsi zapiski uporabnika
        $stmt = $pdo->prepare("
            SELECT z.*, v.naslov 
            FROM uporabniski_zapiski z
            JOIN vsebina v ON z.vsebina_id = v.id
            WHERE z.uporabnik_id = :uid
            ORDER BY z.ustvarjeno DESC
        ");
        $stmt->execute([':uid' => $uporabnikId]);
        
        return [
            'uspeh' => true,
            'zapiski' => $stmt->fetchAll()
        ];
    }
	
	// ============================
// 3f: OTROŠKA VERZIJA (Aeternum Puerilis)
// ============================

function aeternumPuerilisSeznam(array $podatki): array {
    $stran = max(1, (int)($podatki['stran'] ?? 1));
    $naStran = min(50, (int)($podatki['na_stran'] ?? 20));
    $starost = (int)($podatki['starost'] ?? 7);
    $kategorija = $podatki['kategorija'] ?? '';
    $offset = ($stran - 1) * $naStran;
    
    $pdo = aeternumPovezava();
    
    // Filtriraj: samo puerilis stil in primerna starost
    $sql = "SELECT id, naslov, povzetek, kategorija, starost_priporocilo, barva_tema, slika_pogled
            FROM vsebina 
            WHERE stil = 'puerilis' AND starost_priporocilo <= :starost";
    $params = [':starost' => $starost];
    
    if (!empty($kategorija)) {
        $sql .= " AND kategorija = :kategorija";
        $params[':kategorija'] = $kategorija;
    }
    
    $sql .= " ORDER BY starost_priporocilo ASC, ustvarjeno DESC LIMIT :limit OFFSET :offset";
    $params[':limit'] = $naStran;
    $params[':offset'] = $offset;
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    $vnosi = $stmt->fetchAll();
    
    // Skupaj
    $sqlSkupaj = "SELECT COUNT(*) as skupaj FROM vsebina WHERE stil = 'puerilis' AND starost_priporocilo <= :starost";
    if (!empty($kategorija)) {
        $sqlSkupaj .= " AND kategorija = :kategorija";
        $stmtSkupaj = $pdo->prepare($sqlSkupaj);
        $stmtSkupaj->execute(array_merge($params, [':kategorija' => $kategorija]));
    } else {
        $stmtSkupaj = $pdo->prepare($sqlSkupaj);
        $stmtSkupaj->execute([':starost' => $starost]);
    }
    $skupaj = $stmtSkupaj->fetch()['skupaj'];
    
    return [
        'uspeh' => true,
        'vnosi' => $vnosi,
        'stran' => $stran,
        'na_stran' => $naStran,
        'skupaj' => (int)$skupaj,
        'strani' => ceil($skupaj / $naStran),
        'verzija' => 'puerilis',
        'starost' => $starost,
        'sporocilo' => '🌙 Zgodbice za male raziskovalce.'
    ];
}

function aeternumPuerilisZgodbaDneva(array $podatki): array {
    $starost = (int)($podatki['starost'] ?? 7);
    $pdo = aeternumPovezava();
    
    // Naključna zgodba glede na starost
    $datum = date('Y-m-d');
    $seed = crc32($datum . $starost);
    
    $stmt = $pdo->prepare("
        SELECT id, naslov, vsebina, povzetek, starost_priporocilo, barva_tema, slika_pogled
        FROM vsebina 
        WHERE stil = 'puerilis' AND starost_priporocilo <= :starost
        ORDER BY (id * :seed) % 1000
        LIMIT 1
    ");
    $stmt->execute([':starost' => $starost, ':seed' => $seed]);
    $zgodba = $stmt->fetch();
    
    if (!$zgodba) {
        return ['napaka' => 'Ni zgodbic za to starost.'];
    }
    
    return [
        'uspeh' => true,
        'datum' => $datum,
        'zgodba' => $zgodba,
        'verzija' => 'puerilis',
        'uveceritev' => '⭐ Zgodba dneva za male srčke ⭐'
    ];
}

function aeternumPuerilisVecemiNacin(array $podatki): array {
    // Večerni način — mehke barve, zatemnitev, umiritev
    $vklopi = $podatki['vklopi'] ?? true;
    
    return [
        'uspeh' => true,
        'vecemi_nacin' => $vklopi,
        'css' => [
            'ozadje' => '#1a1a2e',
            'tekst' => '#c4b5fd',
            'kartice' => '#2d2d44',
            'gumbi' => '#6c63ff',
            'svetloba' => 'mehka'
        ],
        'sporocilo' => '🌜 Večerni način vklopljen. Lajša oči pred spanjem.'
    ];
}

function aeternumPuerilisZvocnaSlikica(array $podatki): array {
    // Zvočna slikica — ilustracija + glas
    $id = (int)($podatki['id'] ?? 0);
    
    if ($id <= 0) {
        return ['napaka' => 'Manjka ID slikice.'];
    }
    
    $pdo = aeternumPovezava();
    $stmt = $pdo->prepare("
        SELECT id, naslov, vsebina, slika_pogled, barva_tema
        FROM vsebina 
        WHERE id = :id AND stil = 'puerilis'
    ");
    $stmt->execute([':id' => $id]);
    $slikica = $stmt->fetch();
    
    if (!$slikica) {
        return ['napaka' => 'Zvočna slikica ne obstaja.'];
    }
    
    return [
        'uspeh' => true,
        'slikica' => $slikica,
        'audio_tekst' => $slikica['vsebina'],
        'barva' => $slikica['barva_tema'] ?? 'zlata',
        'emoji' => '🎨'
    ];
}
}