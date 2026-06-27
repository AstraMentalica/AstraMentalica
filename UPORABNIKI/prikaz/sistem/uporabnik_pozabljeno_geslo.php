<?php
/**
 * ============================================================
 *  POT: UPORABNIKI/prikaz/sistem/uporabnik_pozabljeno_geslo.php
 *  
 *  v112
 * ============================================================
 * 
 * 📦 NAMEN: Obnovitev pozabljenega gesla.
 * 
 * 🔧 FUNKCIJE:
 *     - Obrazec za vnos e-pošte
 *     - Pošiljanje navodil za ponastavitev
 * 
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - $vsebina['sporocilo'], $vsebina['napaka'], $vsebina['email'], $vsebina['poslano']
 * 
 * ⚠️ UPORABA: Ko uporabnik pozabi geslo.
 * 
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez direktnega branja PODATKI/
 *     - Brez require_once MODULI/
 *     - Brez require_once UPORABNIKI/
 * 
 * 
 */

declare(strict_types=1);

$sporocilo = $vsebina['sporocilo'] ?? '';
$napaka = $vsebina['napaka'] ?? '';
$email = $vsebina['email'] ?? '';
$poslano = $vsebina['poslano'] ?? false;
?>

<div class="pozabljeno-geslo-stran">
<div class="pozabljeno-geslo-vsebina">
    <div class="pozabljeno-geslo-okvir">
        <div class="pozabljeno-geslo-glava">
            <div class="pozabljeno-geslo-ikona">🔑</div>
            <h1 class="pozabljeno-geslo-naslov">Pozabljeno geslo</h1>
            <p class="pozabljeno-geslo-podnaslov">Pošljite si navodila za ponastavitev gesla</p>
        </div>
        
        <?php if ($poslano): ?>
            <div class="pozabljeno-geslo-poslano">
                <div class="poslano-ikona">📧</div>
                <h2>Navodila poslana</h2>
                <p>Poslali smo vam navodila za ponastavitev gesla na elektronski naslov <strong><?= htmlspecialchars($email) ?></strong>.</p>
                <p>Preverite tudi mapo z neželeno pošto.</p>
                <a href="?svet=UPORABNIKI&pot=prijava" class="gumb gumb-primaren">← Nazaj na prijavo</a>
            </div>
        <?php else: ?>
            <?php if ($napaka): ?>
                <div class="pozabljeno-geslo-napaka"><?= htmlspecialchars($napaka) ?></div>
            <?php endif; ?>
            <?php if ($sporocilo): ?>
                <div class="pozabljeno-geslo-sporocilo"><?= htmlspecialchars($sporocilo) ?></div>
            <?php endif; ?>
            
            <form method="post" action="?svet=SISTEM&amp;akcija=pozabljeno_geslo" class="pozabljeno-geslo-obrazec">
                <div class="obrazec-skupina">
                    <label for="email" class="obrazec-oznaka">Elektronski naslov</label>
                    <input type="email" id="email" name="email" class="obrazec-vnos" 
                           value="<?= htmlspecialchars($email) ?>" required autofocus
                           placeholder="ime@example.com">
                </div>
                <button type="submit" class="gumb gumb-primaren">Pošlji navodila</button>
                
                <div class="pozabljeno-geslo-povezave">
                    <a href="?svet=UPORABNIKI&pot=prijava">← Nazaj na prijavo</a>
                    <a href="?svet=UPORABNIKI&pot=registracija">Še nimate računa? Registrirajte se</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
</div>

<style>
.pozabljeno-geslo-stran { display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 200px); padding: 2rem; }
.pozabljeno-geslo-vsebina { width: 100%; max-width: 450px; }
.pozabljeno-geslo-okvir { background: rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); }
.pozabljeno-geslo-glava { text-align: center; margin-bottom: 2rem; }
.pozabljeno-geslo-ikona { font-size: 3rem; margin-bottom: 0.5rem; }
.pozabljeno-geslo-naslov { color: #e8c84a; font-size: 1.8rem; margin-bottom: 0.25rem; }
.pozabljeno-geslo-podnaslov { color: #888; font-size: 0.9rem; }
.pozabljeno-geslo-napaka { background: rgba(244, 67, 54, 0.2); border-left: 3px solid #f44336; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; color: #f44336; }
.pozabljeno-geslo-sporocilo { background: rgba(76, 175, 80, 0.2); border-left: 3px solid #4caf50; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; color: #4caf50; }
.pozabljeno-geslo-poslano { text-align: center; }
.poslano-ikona { font-size: 4rem; margin-bottom: 1rem; }
.pozabljeno-geslo-poslano h2 { color: #e8c84a; margin-bottom: 1rem; }
.pozabljeno-geslo-poslano p { color: #aaa; margin-bottom: 0.5rem; }
.pozabljeno-geslo-povezave { display: flex; justify-content: space-between; margin-top: 1.5rem; font-size: 0.85rem; }
.pozabljeno-geslo-povezave a { color: #888; text-decoration: none; }
.pozabljeno-geslo-povezave a:hover { color: #e8c84a; }
</style>