<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/strani/uporabniki/profil.php
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Uporabniški profil
 * ---------------------------------------------------------
 */
declare(strict_types=1);

$uporabnik = $vsebina['uporabnik'] ?? [];
$sporocilo = $vsebina['sporocilo'] ?? '';
$napaka = $vsebina['napaka'] ?? '';
?>

<div class="profil">
    <div class="profil-vsebina">
        <h1>Moj profil</h1>
        
        <?php if ($sporocilo): ?>
            <div class="opozorilo opozorilo-uspeh"><?= htmlspecialchars($sporocilo) ?></div>
        <?php endif; ?>
        
        <?php if ($napaka): ?>
            <div class="opozorilo opozorilo-napaka"><?= htmlspecialchars($napaka) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="?svet=API&amp;akcija=posodobi_profil" class="obrazec" id="profil-obrazec">
            <div class="obrazec-skupina">
                <label class="obrazec-oznaka" for="ime">Ime</label>
                <input type="text" id="ime" name="ime" class="vnos" value="<?= htmlspecialchars($uporabnik['ime'] ?? '') ?>">
            </div>
            
            <div class="obrazec-skupina">
                <label class="obrazec-oznaka" for="email">Elektronski naslov</label>
                <input type="email" id="email" class="vnos" value="<?= htmlspecialchars($uporabnik['elektronski_naslov'] ?? '') ?>" disabled>
                <small>Emaila ni mogoče spremeniti</small>
            </div>
            
            <div class="obrazec-skupina">
                <label class="obrazec-oznaka" for="vloga">Vloga</label>
                <input type="text" id="vloga" class="vnos" value="<?= htmlspecialchars(RBAC_vloga_ime($uporabnik['vloga'] ?? 0)) ?>" disabled>
            </div>
            
            <div class="obrazec-skupina">
                <button type="submit" class="gumb gumb-primaren">Posodobi profil</button>
            </div>
        </form>
        
        <hr>
        
        <h2>Sprememba gesla</h2>
        
        <form method="POST" action="?svet=API&amp;akcija=spremeni_geslo" class="obrazec" id="geslo-obrazec">
            <div class="obrazec-skupina">
                <label class="obrazec-oznaka" for="staro_geslo">Staro geslo</label>
                <input type="password" id="staro_geslo" name="staro_geslo" class="vnos" required>
            </div>
            
            <div class="obrazec-skupina">
                <label class="obrazec-oznaka" for="novo_geslo">Novo geslo</label>
                <input type="password" id="novo_geslo" name="novo_geslo" class="vnos" required>
            </div>
            
            <div class="obrazec-skupina">
                <button type="submit" class="gumb">Spremeni geslo</button>
            </div>
        </form>
        
        <div class="profil-gumbi">
            <a href="?svet=API&amp;akcija=odjava" class="gumb gumb-odjava" onclick="return confirm('Ali ste prepričani?')">Odjava</a>
        </div>
    </div>
</div>

<style>
.profil {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}
.profil-vsebina {
    background: rgba(255,255,255,0.05);
    padding: 2rem;
    border-radius: 15px;
}
hr {
    margin: 2rem 0;
    border: none;
    border-top: 1px solid #2a2a4a;
}
.opozorilo {
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}
.opozorilo-uspeh {
    background: rgba(39,174,96,0.2);
    border: 1px solid #27ae60;
    color: #27ae60;
}
.opozorilo-napaka {
    background: rgba(231,76,60,0.2);
    border: 1px solid #e74c3c;
    color: #e74c3c;
}
.profil-gumbi {
    margin-top: 2rem;
    text-align: center;
}
.gumb-odjava {
    background: rgba(231,76,60,0.2);
    border-color: #e74c3c;
    color: #e74c3c;
}
.gumb-odjava:hover {
    background: #e74c3c;
    color: #fff;
}
</style>
