<?php
/**
 * ============================================================
 *  POT: UPORABNIKI/prikaz/sistem/uporabnik_nastavitve.php
 *  
 *  v112
 * ============================================================
 * 
 * 📦 NAMEN: Uporabniške nastavitve (jezik, tema, obvestila).
 * 
 * 🔧 FUNKCIJE:
 *     - Prikaz nastavitev profila
 *     - Shranjevanje jezika, teme, obvestil
 *     - Dvofaktorska avtentikacija
 * 
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - $vsebina['uporabnik'], $vsebina['nastavitve']
 * 
 * ⚠️ UPORABA: Ko uporabnik ureja svoje nastavitve.
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

$uporabnik = $vsebina['uporabnik'] ?? null;
$nastavitve = $vsebina['nastavitve'] ?? [];
$sporocilo = $vsebina['sporocilo'] ?? '';
$napaka = $vsebina['napaka'] ?? '';

if (!$uporabnik) {
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}

$jezik = $nastavitve['jezik'] ?? 'sl';
$tema = $nastavitve['tema'] ?? 'standard';
$obvestila = $nastavitve['obvestila'] ?? true;
$emailObvestila = $nastavitve['email_obvestila'] ?? true;
$dvofaktorska = $nastavitve['dvofaktorska'] ?? false;
?>

<div class="nastavitve-stran">
<div class="nastavitve-vsebina">
    <div class="nastavitve-glava">
        <h1 class="nastavitve-naslov">⚙️ Nastavitve</h1>
        <p class="nastavitve-podnaslov">Prilagodite svojo izkušnjo</p>
    </div>
    
    <?php if ($sporocilo): ?>
        <div class="nastavitve-sporocilo"><?= htmlspecialchars($sporocilo) ?></div>
    <?php endif; ?>
    <?php if ($napaka): ?>
        <div class="nastavitve-napaka"><?= htmlspecialchars($napaka) ?></div>
    <?php endif; ?>
    
    <form method="post" action="?svet=SISTEM&amp;akcija=nastavitve_posodobi" class="nastavitve-obrazec">
        <div class="nastavitve-sekcija">
            <h2>🌐 Jezik in regija</h2>
            <div class="obrazec-skupina">
                <label for="jezik" class="obrazec-oznaka">Jezik</label>
                <select id="jezik" name="jezik" class="obrazec-vnos">
                    <option value="sl" <?= $jezik === 'sl' ? 'selected' : '' ?>>Slovenščina</option>
                    <option value="en" <?= $jezik === 'en' ? 'selected' : '' ?>>English</option>
                    <option value="de" <?= $jezik === 'de' ? 'selected' : '' ?>>Deutsch</option>
                    <option value="fr" <?= $jezik === 'fr' ? 'selected' : '' ?>>Français</option>
                    <option value="it" <?= $jezik === 'it' ? 'selected' : '' ?>>Italiano</option>
                    <option value="hr" <?= $jezik === 'hr' ? 'selected' : '' ?>>Hrvatski</option>
                    <option value="sr" <?= $jezik === 'sr' ? 'selected' : '' ?>>Srpski</option>
                </select>
            </div>
        </div>
        
        <div class="nastavitve-sekcija">
            <h2>🎨 Izgled</h2>
            <div class="obrazec-skupina">
                <label for="tema" class="obrazec-oznaka">Tema</label>
                <select id="tema" name="tema" class="obrazec-vnos">
                    <option value="standard" <?= $tema === 'standard' ? 'selected' : '' ?>>Standardna</option>
                    <option value="minimal" <?= $tema === 'minimal' ? 'selected' : '' ?>>Minimalna</option>
                    <option value="mystic" <?= $tema === 'mystic' ? 'selected' : '' ?>>Mistična</option>
                    <option value="astra" <?= $tema === 'astra' ? 'selected' : '' ?>>Astra</option>
                </select>
            </div>
        </div>
        
        <div class="nastavitve-sekcija">
            <h2>🔔 Obvestila</h2>
            <div class="obrazec-skupina obrazec-potrdilo">
                <label class="obrazec-potrdilo-oznaka">
                    <input type="checkbox" name="obvestila" value="1" <?= $obvestila ? 'checked' : '' ?>>
                    <span>Sistemska obvestila</span>
                </label>
            </div>
            <div class="obrazec-skupina obrazec-potrdilo">
                <label class="obrazec-potrdilo-oznaka">
                    <input type="checkbox" name="email_obvestila" value="1" <?= $emailObvestila ? 'checked' : '' ?>>
                    <span>E-poštna obvestila</span>
                </label>
            </div>
        </div>
        
        <div class="nastavitve-sekcija">
            <h2>🔒 Varnost</h2>
            <div class="obrazec-skupina obrazec-potrdilo">
                <label class="obrazec-potrdilo-oznaka">
                    <input type="checkbox" name="dvofaktorska" value="1" <?= $dvofaktorska ? 'checked' : '' ?>>
                    <span>Dvofaktorska avtentikacija</span>
                </label>
            </div>
        </div>
        
        <div class="nastavitve-akcije">
            <button type="submit" class="gumb gumb-primaren">Shrani nastavitve</button>
            <button type="reset" class="gumb gumb-sekundaren">Počisti</button>
        </div>
    </form>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
document.getElementById('tema')?.addEventListener('change', (e) => {
    document.body.setAttribute('data-tema-predogled', e.target.value);
    setTimeout(() => document.body.removeAttribute('data-tema-predogled'), 3000);
});
</script>

<style>
.nastavitve-stran { max-width: 600px; margin: 0 auto; padding: 2rem; }
.nastavitve-vsebina { background: rgba(255, 255, 255, 0.03); border-radius: 20px; padding: 2rem; }
.nastavitve-glava { text-align: center; margin-bottom: 2rem; }
.nastavitve-naslov { color: #e8c84a; font-size: 1.8rem; }
.nastavitve-podnaslov { color: #aaa; }
.nastavitve-sporocilo { background: rgba(76, 175, 80, 0.2); border-left: 3px solid #4caf50; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; color: #4caf50; }
.nastavitve-napaka { background: rgba(244, 67, 54, 0.2); border-left: 3px solid #f44336; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; color: #f44336; }
.nastavitve-sekcija { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
.nastavitve-sekcija h2 { color: #e8c84a; font-size: 1.2rem; margin-bottom: 1rem; }
.nastavitve-akcije { display: flex; gap: 1rem; margin-top: 2rem; }
</style>