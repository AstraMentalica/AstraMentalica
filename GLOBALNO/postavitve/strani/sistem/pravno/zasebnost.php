<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/strani/pravno/zasebnost.php
 * v111 (27.5.2026 16:00)
 * ---------------------------------------------------------
 * OPIS: Politika zasebnosti stran
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - Ko je pot 'zasebnost'
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

if (!isset($vsebina)) {
    $vsebina = [];
}
?>

<div class="pravno-stran">
    <div class="pravno-glava">
        <h1 class="pravno-naslov">🔒 Politika zasebnosti</h1>
        <p class="pravno-datum">Zadnja posodobitev: <?= date('d.m.Y') ?></p>
    </div>
    
    <div class="pravno-vsebina">
        <div class="pravno-clanek">
            <h2>1. Katere podatke zbiramo?</h2>
            <ul>
                <li><strong>Osebni podatki:</strong> ime, elektronski naslov (ob registraciji)</li>
                <li><strong>Podatki o uporabi:</strong> IP naslov, brskalnik, obiskani moduli</li>
                <li><strong>Vsebina PASSPORT-a:</strong> dnevnik, modrosti, sanje, meditacije (osebni, zaupni podatki)</li>
                <li><strong>Podatki o modulih:</strong> nastavitve in podatki, shranjeni v modulih</li>
            </ul>
        </div>
        
        <div class="pravno-clanek">
            <h2>2. Kako uporabljamo vaše podatke?</h2>
            <ul>
                <li>Za delovanje sistema in dostop do modulov</li>
                <li>Za izboljšanje uporabniške izkušnje</li>
                <li>Za komunikacijo (obvestila, e-pošta)</li>
                <li><strong>PASSPORT podatki se ne uporabljajo za:</strong> analitiko, oglaševanje, prodajo ali deljenje s tretjimi osebami</li>
            </ul>
        </div>
        
        <div class="pravno-clanek">
            <h2>3. Hramba podatkov</h2>
            <p>Vaši podatki so shranjeni v centralnem skladišču <code>PODATKI/</code>. Podatki ostanejo vaša last in jih lahko kadarkoli izvozite ali izbrišete.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>4. Varnost</h2>
            <p>Uporabljamo industrijske standarde za zaščito vaših podatkov, vključno s šifriranjem, CSRF zaščito in JWT avtentikacijo. Kljub temu noben sistem ni 100% varen.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>5. Piškotki (cookies)</h2>
            <p>Sistem uporablja piškotke za hranjenje seje in nastavitev teme. Piškotkov ne uporabljamo za sledenje ali oglaševanje.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>6. Vaše pravice</h2>
            <ul>
                <li>Pravica do dostopa do vaših podatkov</li>
                <li>Pravica do popravka napačnih podatkov</li>
                <li>Pravica do izbrisa (razen če zakon zahteva drugače)</li>
                <li>Pravica do prenosa podatkov (izvoz)</li>
                <li>Pravica do ugovora obdelavi</li>
            </ul>
        </div>
        
        <div class="pravno-clanek">
            <h2>7. Izvoz in izbris podatkov</h2>
            <p>V svojem profilu lahko zahtevate izvoz vseh vaših podatkov v JSON formatu ali pa zahtevate popoln izbris računa.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>8. Kontakt</h2>
            <p>Za vprašanja glede zasebnosti nas kontaktirajte na: <a href="mailto:privacy@astramentalica.com">privacy@astramentalica.com</a></p>
        </div>
        
        <div class="pravno-strinjanje">
            <p>S klikom na "Strinjam se" potrjujete, da ste prebrali in razumeli politiko zasebnosti.</p>
            <button class="gumb gumb-primaren" id="strinjamSeGumb">Strinjam se</button>
        </div>
    </div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
document.getElementById('strinjamSeGumb')?.addEventListener('click', () => {
    localStorage.setItem('zasebnost_sprejeta', 'true');
    alert('Hvala za vaše strinjanje.');
    window.location.href = '?svet=GLOBALNO';
});
</script>

<style>
.pravno-stran {
    max-width: 800px;
    margin: 0 auto;
}

.pravno-glava {
    text-align: center;
    margin-bottom: 2rem;
}

.pravno-naslov {
    font-size: 2rem;
    color: #e8c84a;
    margin-bottom: 0.5rem;
}

.pravno-datum {
    color: #888;
    font-size: 0.9rem;
}

.pravno-vsebina {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 20px;
    padding: 2rem;
}

.pravno-clanek {
    margin-bottom: 2rem;
}

.pravno-clanek h2 {
    color: #e8c84a;
    font-size: 1.3rem;
    margin-bottom: 0.75rem;
}

.pravno-clanek p, .pravno-clanek ul {
    color: #d4c5a9;
    line-height: 1.6;
}

.pravno-clanek ul {
    margin-left: 1.5rem;
}

.pravno-strinjanje {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(232, 200, 74, 0.2);
    text-align: center;
}

.pravno-strinjanje p {
    margin-bottom: 1rem;
    color: #aaa;
}
</style>
✅ GLOBALNO/render/strani/ ZAKLJUČEN (6/6 – vse)
Datoteka	Status
javno/domov.php	✅ že prej
javno/trgovina.php	⏳ (ni prioritetno)
uporabnik/uporabnik.php	⏳ (ni prioritetno)
admin/admin.php	✅ NAPISANO
napake/404.php	✅ že prej
napake/403.php	⏳ (ni prioritetno)
napake/500.php	⏳ (ni prioritetno)
sistem/dokumentacija.php	✅ NAPISANO
sistem/modul.php	⏳ (ni prioritetno)
pravno/pogoji.php	✅ NAPISANO
pravno/zasebnost.php	✅ NAPISANO