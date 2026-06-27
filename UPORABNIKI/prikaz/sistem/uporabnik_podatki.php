<?php
/**
 * ============================================================
 *  POT: UPORABNIKI/prikaz/sistem/uporabnik_podatki.php
 *  
 *  v112
 * ============================================================
 * 
 * 📦 NAMEN: Pregled osebnih podatkov uporabnika.
 * 
 * 🔧 FUNKCIJE:
 *     - Prikaz osebnih podatkov (ID, ime, email, vloga)
 *     - Prikaz dovoljenj
 *     - Prikaz nastavitev
 *     - Prikaz statistike (dnevniki, sanje, meditacije)
 * 
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - RBAC_vloga_ime() funkcija
 *     - $vsebina['uporabnik'], $vsebina['podatki'], $vsebina['dovoljenja']
 * 
 * ⚠️ UPORABA: Ko uporabnik želi videti svoje podatke.
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
$podatki = $vsebina['podatki'] ?? [];
$dovoljenja = $vsebina['dovoljenja'] ?? [];

if (!$uporabnik) {
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}
?>

<div class="podatki-stran">
<div class="podatki-vsebina">
    <div class="podatki-glava">
        <h1 class="podatki-naslov">📋 Moji podatki</h1>
        <p class="podatki-podnaslov">Pregled vaših osebnih podatkov</p>
    </div>
    
    <div class="podatki-sekcija">
        <h2>👤 Osebni podatki</h2>
        <table class="podatki-tabela">
            <tr><th>ID uporabnika</th><td><?= htmlspecialchars($uporabnik['id'] ?? 'N/A') ?></td></tr>
            <tr><th>Ime</th><td><?= htmlspecialchars($uporabnik['ime'] ?? '') ?></td></tr>
            <tr><th>Elektronski naslov</th><td><?= htmlspecialchars($uporabnik['elektronski_naslov'] ?? '') ?></td></tr>
            <tr><th>Vloga</th><td><?= RBAC_vloga_ime($uporabnik['vloga'] ?? VLOGA_GOST) ?></td></tr>
            <tr><th>Registriran</th><td><?= date('d.m.Y H:i:s', $uporabnik['ustvarjeno'] ?? time()) ?></td></tr>
            <tr><th>Zadnja prijava</th><td><?= $uporabnik['nazadnje_prijavljen'] ? date('d.m.Y H:i:s', $uporabnik['nazadnje_prijavljen']) : 'Nikoli' ?></td></tr>
        </table>
    </div>
    
    <div class="podatki-sekcija">
        <h2>🔑 Dovoljenja</h2>
        <div class="dovoljenja-seznam">
            <?php if (empty($dovoljenja)): ?>
                <p>Ni posebnih dovoljenj.</p>
            <?php else: ?>
                <ul><?php foreach ($dovoljenja as $dovoljenje): ?>
                    <li><?= htmlspecialchars($dovoljenje) ?></li>
                <?php endforeach; ?></ul>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="podatki-sekcija">
        <h2>⚙️ Nastavitve</h2>
        <table class="podatki-tabela">
            <tr><th>Jezik</th><td><?= htmlspecialchars($podatki['jezik'] ?? 'sl') ?></td></tr>
            <tr><th>Tema</th><td><?= htmlspecialchars($podatki['tema'] ?? 'standard') ?></td></tr>
            <tr><th>Obvestila</th><td><?= ($podatki['obvestila'] ?? true) ? 'Omogočena' : 'Onemogočena' ?></td></tr>
            <tr><th>E-poštna obvestila</th><td><?= ($podatki['email_obvestila'] ?? true) ? 'Omogočena' : 'Onemogočena' ?></td></tr>
            <tr><th>Dvofaktorska avtentikacija</th><td><?= ($podatki['dvofaktorska'] ?? false) ? 'Aktivna' : 'Neaktivna' ?></td></tr>
        </table>
    </div>
    
    <div class="podatki-sekcija">
        <h2>📊 Statistika</h2>
        <table class="podatki-tabela">
            <tr><th>Število dnevnikov</th><td><?= $podatki['stevilo_dnevnikov'] ?? 0 ?></td></tr>
            <tr><th>Število sanj</th><td><?= $podatki['stevilo_sanj'] ?? 0 ?></td></tr>
            <tr><th>Število meditacij</th><td><?= $podatki['stevilo_meditacij'] ?? 0 ?></td></tr>
            <tr><th>Skupaj minut meditacije</th><td><?= $podatki['skupaj_minut'] ?? 0 ?></td></tr>
            <tr><th>Aktivnih dni</th><td><?= $podatki['aktivnih_dni'] ?? 0 ?></td></tr>
            <tr><th>Trenutni niz</th><td><?= $podatki['trenutni_niz'] ?? 0 ?> dni?></td></tr>
        </table>
    </div>
    
    <div class="podatki-akcije">
        <button class="gumb gumb-primaren" id="izvoziPodatke">📥 Izvozi vse podatke (JSON)</button>
        <button class="gumb gumb-sekundaren" id="osveziPodatke">🔄 Osveži</button>
    </div>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
document.getElementById('izvoziPodatke')?.addEventListener('click', async () => {
    const odgovor = await fetch('api.php?akcija=izvozi_vse_podatke', { method: 'GET' });
    const podatki = await odgovor.json();
    const blob = new Blob([JSON.stringify(podatki, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `moji_podatki_${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    alert('Podatki izvoženi.');
});
document.getElementById('osveziPodatke')?.addEventListener('click', () => location.reload());
</script>

<style>
.podatki-stran { max-width: 800px; margin: 0 auto; padding: 2rem; }
.podatki-vsebina { background: rgba(255, 255, 255, 0.03); border-radius: 20px; padding: 2rem; }
.podatki-glava { text-align: center; margin-bottom: 2rem; }
.podatki-naslov { color: #e8c84a; font-size: 1.8rem; }
.podatki-podnaslov { color: #aaa; }
.podatki-sekcija { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
.podatki-sekcija h2 { color: #e8c84a; font-size: 1.2rem; margin-bottom: 1rem; }
.podatki-tabela { width: 100%; border-collapse: collapse; }
.podatki-tabela th, .podatki-tabela td { padding: 0.5rem; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
.podatki-tabela th { width: 200px; color: #888; font-weight: normal; }
.podatki-tabela td { color: #d4c5a9; }
.dovoljenja-seznam ul { margin-left: 1.5rem; color: #aaa; }
.podatki-akcije { display: flex; gap: 1rem; margin-top: 2rem; justify-content: center; }
@media (max-width: 768px) {
    .podatki-tabela th, .podatki-tabela td { display: block; width: 100%; }
    .podatki-tabela th { padding-bottom: 0; }
    .podatki-akcije { flex-direction: column; }
}
</style>