<?php
/**
 * ---------------------------------------------------------
 * POT: VSEBINA/navodila/uporabniska_navodila.php
 * v111 (27.5.2026 23:15)
 * ---------------------------------------------------------
 * OPIS: Uporabniška navodila za sistem
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - Admin dokumentacija
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump (razen HTML)
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 56 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

session_start();

if (!isset($_SESSION['astra_uporabnik']) || $_SESSION['astra_uporabnik']['vloga'] < VLOGA_ADMIN) {
header('Location: ?svet=ASTRA&pot=prijava');
exit;
}

$uporabnik = $_SESSION['astra_uporabnik'];
?>

<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Uporabniška navodila | ASTRA</title>
<style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{background:#0a0a1a;color:#d4c5a9;font-family:system-ui}
    .astra-glava{background:#1a1a2e;padding:1rem 2rem;display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem}
    .astra-logotip{font-size:1.5rem;font-weight:bold;color:#e8c84a}
    .astra-uporabnik{display:flex;align-items:center;gap:1rem}
    .astra-odjava{color:#f44336;text-decoration:none}
    .astra-vsebina{max-width:800px;margin:0 auto;padding:2rem}
    .nazaj{display:inline-block;margin-bottom:1rem;color:#e8c84a;text-decoration:none}
    .sekcija{background:rgba(255,255,255,0.03);border-radius:15px;padding:1.5rem;margin-bottom:1.5rem}
    .sekcija h2{color:#e8c84a;margin-bottom:1rem;border-bottom:1px solid rgba(232,200,74,0.2);padding-bottom:0.5rem}
    .navodilo{margin-bottom:1rem}
    .navodilo-naslov{font-weight:bold;color:#e8c84a;margin-bottom:0.25rem}
    .navodilo-opis{color:#aaa;margin-left:1rem}
    .koda{background:#0a0a1a;padding:0.5rem;border-radius:5px;font-family:monospace;font-size:0.8rem;margin:0.5rem 0 0.5rem 1rem}
    @media(max-width:768px){.navodilo-opis{margin-left:0}}
</style>
</head>
<body>
<div class="astra-glava">
    <div class="astra-logotip">🛠️ ASTRA Admin</div>
    <div class="astra-uporabnik">
        <span>👤 <?= htmlspecialchars($uporabnik['ime'] ?? 'Administrator') ?></span>
        <a href="?svet=ASTRA&pot=odjava" class="astra-odjava">Odjava</a>
    </div>
</div>

<div class="astra-vsebina">
    <a href="?svet=ASTRA&pot=nadzorna_plosca" class="nazaj">← Nazaj na nadzorno ploščo</a>
    <h1>📖 Uporabniška navodila</h1>
    
    <div class="sekcija">
        <h2>1. Prvi koraki</h2>
        <div class="navodilo">
            <div class="navodilo-naslov">1.1 Namestitev sistema</div>
            <div class="navodilo-opis">Zaženite install.php na strežniku. Skripta bo ustvarila vse mape in osnovne datoteke.</div>
            <div class="koda">php install.php</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">1.2 Zagon strežnika</div>
            <div class="navodilo-opis">Za testiranje lokalno uporabite PHP vgrajeni strežnik.</div>
            <div class="koda">php -S localhost:8000</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">1.3 Prijava v ASTRA admin</div>
            <div class="navodilo-opis">Odprite http://localhost:8000/?svet=ASTRA&pot=prijava. Privzeto geslo: admin2024</div>
        </div>
    </div>
    
    <div class="sekcija">
        <h2>2. Upravljanje modulov</h2>
        <div class="navodilo">
            <div class="navodilo-naslov">2.1 Namestitev modula</div>
            <div class="navodilo-opis">Preko ASTRA nadzorne plošče ali CLI ukaza.</div>
            <div class="koda">php cli.php modul:install ImeModula</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">2.2 Aktivacija modula</div>
            <div class="navodilo-opis">V ASTRA nadzorni plošči kliknite "Aktiviraj" pri želenem modulu.</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">2.3 Ustvarjanje novega modula</div>
            <div class="navodilo-opis">Ustvarite mapo v MODULI/[KATEGORIJA]/[ImeModula]/ z manifest.json in modul.php.</div>
        </div>
    </div>
    
    <div class="sekcija">
        <h2>3. Uporaba čakalne vrste</h2>
        <div class="navodilo">
            <div class="navodilo-naslov">3.1 Dodajanje paketa</div>
            <div class="koda">queue_dodaj(['akcija' => 'email', 'podatki' => [...]], 'elektronska_posta');</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">3.2 Zagon workerja</div>
            <div class="koda">php cli.php worker</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">3.3 Zagon demona</div>
            <div class="koda">php cli.php daemon</div>
        </div>
    </div>
    
    <div class="sekcija">
        <h2>4. CLI ukazi</h2>
        <div class="navodilo">
            <div class="navodilo-naslov">4.1 Seznam ukazov</div>
            <div class="navodilo-opis">Prikaže vse razpoložljive CLI ukaze.</div>
            <div class="koda">php cli.php pomoc</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">4.2 Cron zagon</div>
            <div class="koda">php cli.php cron</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">4.3 Čiščenje sistema</div>
            <div class="koda">php cli.php cleanup</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">4.4 Ponovno nalaganje</div>
            <div class="koda">php cli.php reload</div>
        </div>
    </div>
    
    <div class="sekcija">
        <h2>5. API uporaba</h2>
        <div class="navodilo">
            <div class="navodilo-naslov">5.1 Prijava</div>
            <div class="koda">POST /api.php?akcija=prijava { "email": "admin@astra.com", "geslo": "admin2024" }</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">5.2 Health check</div>
            <div class="koda">GET /api.php?akcija=zdravje</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">5.3 Modul izvedba</div>
            <div class="koda">POST /api.php?akcija=modul_izvedi { "modul": "Stelaris", "akcija": "horoskop" }</div>
        </div>
    </div>
    
    <div class="sekcija">
        <h2>6. Reševanje težav</h2>
        <div class="navodilo">
            <div class="navodilo-naslov">6.1 Dnevniki</div>
            <div class="navodilo-opis">Dnevniki se nahajajo v PODATKI/sistem/dnevnik/.</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">6.2 Debug način</div>
            <div class="navodilo-opis">Nastavite RAZVOJNI_NACIN = true v pot.php za podrobne napake.</div>
        </div>
        <div class="navodilo">
            <div class="navodilo-naslov">6.3 Obnovitev sistema</div>
            <div class="koda">php cli.php recovery</div>
        </div>
    </div>
</div>
</body>
</html>