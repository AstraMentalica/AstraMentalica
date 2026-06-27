<?php
/**
 * ============================================================
 *  POT: UPORABNIKI/prikaz/sistem/uporabnik_izvoz.php
 *  
 *  v112
 * ============================================================
 * 
 * 📦 NAMEN: Izvoz uporabniških podatkov (GDPR).
 * 
 * 🔧 FUNKCIJE:
 *     - Zahteva za izvoz podatkov
 *     - Prikaz statusa izvoza (pripravljen/obdelava)
 *     - Prenos JSON datoteke
 * 
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - $vsebina['status'], $vsebina['izvoz_id']
 * 
 * ⚠️ UPORABA: Ko uporabnik želi izvoziti svoje podatke.
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
$status = $vsebina['status'] ?? '';
$izvozId = $vsebina['izvoz_id'] ?? '';

if (!$uporabnik) {
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}
?>

<div class="izvoz-stran">
<div class="izvoz-vsebina">
    <div class="izvoz-glava">
        <h1 class="izvoz-naslov">📤 Izvoz podatkov</h1>
        <p class="izvoz-podnaslov">Izvozi vse svoje podatke v JSON formatu</p>
    </div>
    
    <?php if ($status === 'pripravljen' && $izvozId): ?>
        <div class="izvoz-pripravljen">
            <div class="izvoz-ikona">✅</div>
            <h2>Podatki pripravljeni za prenos</h2>
            <p>Vaši podatki so pripravljeni. Kliknite spodnji gumb za prenos.</p>
            <a href="api.php?akcija=izvoz_prenos&id=<?= urlencode($izvozId) ?>" class="gumb gumb-primaren" download>📥 Prenesi podatke (JSON)</a>
            <p class="izvoz-opozorilo">⚠️ Podatki vsebujejo osebne informacije. Hranite jih varno.</p>
        </div>
    <?php elseif ($status === 'obdelava'): ?>
        <div class="izvoz-obdelava">
            <div class="izvoz-ikona">⏳</div>
            <h2>Obdelava podatkov</h2>
            <p>Vaši podatki se pripravljajo. To lahko traja nekaj minut.</p>
            <div class="izvoz-progress">
                <div class="izvoz-progress-bar" style="width: 50%"></div>
            </div>
            <button class="gumb gumb-sekundaren" id="osveziStatus">Osveži</button>
        </div>
    <?php else: ?>
        <div class="izvoz-info">
            <div class="izvoz-ikona">📋</div>
            <h2>Kaj vse bo izvoženo?</h2>
            <ul>
                <li>Profilne podatke (ime, email, vloga, datum registracije)</li>
                <li>Vse vnose v dnevniku</li>
                <li>Vse zapise sanj</li>
                <li>Vse meditacije</li>
                <li>Zgodovino aktivnosti</li>
                <li>Nastavitve profila</li>
                <li>Podatke iz modulov</li>
            </ul>
            <div class="izvoz-opozorilo">
                <p>⚠️ <strong>POMEMBNO:</strong> Podatki so osebni in zaupni. Ne delite jih z nepooblaščenimi osebami.</p>
            </div>
            <form method="post" action="?svet=SISTEM&amp;akcija=izvoz_zahtevaj" class="izvoz-obrazec">
                <div class="obrazec-skupina obrazec-potrdilo">
                    <label class="obrazec-potrdilo-oznaka">
                        <input type="checkbox" name="potrditev" value="1" required>
                        <span>Zavedam se, da podatki vsebujejo osebne informacije in jih bom hranil varno.</span>
                    </label>
                </div>
                <div class="obrazec-skupina obrazec-potrdilo">
                    <label class="obrazec-potrdilo-oznaka">
                        <input type="checkbox" name="sprejemam" value="1" required>
                        <span>Strinjam se s pogoji izvoza podatkov.</span>
                    </label>
                </div>
                <button type="submit" class="gumb gumb-primaren">Zahtevaj izvoz podatkov</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
document.getElementById('osveziStatus')?.addEventListener('click', () => { location.reload(); });
</script>

<style>
.izvoz-stran { max-width: 600px; margin: 0 auto; padding: 2rem; }
.izvoz-vsebina { background: rgba(255, 255, 255, 0.03); border-radius: 20px; padding: 2rem; }
.izvoz-glava { text-align: center; margin-bottom: 2rem; }
.izvoz-naslov { color: #e8c84a; font-size: 1.8rem; }
.izvoz-podnaslov { color: #aaa; }
.izvoz-ikona { font-size: 4rem; text-align: center; margin-bottom: 1rem; }
.izvoz-info h2, .izvoz-pripravljen h2, .izvoz-obdelava h2 { color: #e8c84a; margin-bottom: 1rem; text-align: center; }
.izvoz-info ul { margin-left: 1.5rem; color: #aaa; margin-bottom: 1.5rem; }
.izvoz-info li { margin-bottom: 0.25rem; }
.izvoz-opozorilo { background: rgba(255, 193, 7, 0.1); border-left: 3px solid #ffc107; padding: 1rem; border-radius: 8px; margin: 1rem 0; color: #ffc107; }
.izvoz-progress { background: rgba(255, 255, 255, 0.1); border-radius: 10px; height: 10px; margin: 1rem 0; overflow: hidden; }
.izvoz-progress-bar { background: #e8c84a; height: 100%; border-radius: 10px; transition: width 0.5s; }
.izvoz-pripravljen { text-align: center; }
.izvoz-obrazec { margin-top: 1.5rem; }
.izvoz-obrazec .obrazec-potrdilo { margin-bottom: 1rem; }
</style>