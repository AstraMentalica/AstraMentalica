<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/strani/sistem/dokumentacija.php
 * v111 (27.5.2026 16:00)
 * ---------------------------------------------------------
 * OPIS: Dokumentacijska stran
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - Ko je tip 'dokumentacija'
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

$aktivniZavihek = $vsebina['zavihek'] ?? 'uvod';
?>

<div class="dokumentacija-stran">
    <div class="dokumentacija-glava">
        <h1 class="dokumentacija-naslov">📚 Dokumentacija</h1>
        <p class="dokumentacija-podnaslov">Navodila za uporabo sistema <?= IME_APLIKACIJE ?></p>
    </div>
    
    <div class="dokumentacija-vsebina">
        <aside class="dokumentacija-meni">
            <ul>
                <li class="<?= $aktivniZavihek === 'uvod' ? 'aktivno' : '' ?>">
                    <a href="?svet=GLOBALNO&amp;pot=dokumentacija&amp;zavihek=uvod">📖 Uvod</a>
                </li>
                <li class="<?= $aktivniZavihek === 'moduli' ? 'aktivno' : '' ?>">
                    <a href="?svet=GLOBALNO&amp;pot=dokumentacija&amp;zavihek=moduli">📦 Moduli</a>
                </li>
                <li class="<?= $aktivniZavihek === 'api' ? 'aktivno' : '' ?>">
                    <a href="?svet=GLOBALNO&amp;pot=dokumentacija&amp;zavihek=api">🔌 API</a>
                </li>
                <li class="<?= $aktivniZavihek === 'razvoj' ? 'aktivno' : '' ?>">
                    <a href="?svet=GLOBALNO&amp;pot=dokumentacija&amp;zavihek=razvoj">🛠️ Razvoj</a>
                </li>
                <li class="<?= $aktivniZavihek === 'pogosta_vprasanja' ? 'aktivno' : '' ?>">
                    <a href="?svet=GLOBALNO&amp;pot=dokumentacija&amp;zavihek=pogosta_vprasanja">❓ Pogosta vprašanja</a>
                </li>
            </ul>
        </aside>
        
        <div class="dokumentacija-clanek">
            <?php if ($aktivniZavihek === 'uvod'): ?>
                <h2>Uvod v <?= IME_APLIKACIJE ?></h2>
                <p><?= IME_APLIKACIJE ?> je večsvetovni runtime sistem, ki omogoča modularno razširjanje funkcionalnosti.</p>
                <h3>Ključne značilnosti</h3>
                <ul>
                    <li><strong>Večsvetovna arhitektura</strong> – GLOBALNO, UPORABNIKI, MODULI, ASTRA</li>
                    <li><strong>Modularen sistem</strong> – Izolirani moduli z lastnim manifestom</li>
                    <li><strong>Večkanalna podpora</strong> – Splet, API, Telegram, Facebook, AI, CLI</li>
                    <li><strong>Varnost</strong> – JWT, CSRF, RBAC, circuit breaker</li>
                    <li><strong>Asinhrono obdelavo</strong> – Čakalna vrsta (queue)</li>
                </ul>
                <h3>Struktura sistema</h3>
                <pre class="dokumentacija-koda">root/
├── pot.php          # SIDRO – edina definicija poti
├── ADAPTER/         # Boundary layer
├── SISTEM/          # Backend orchestration
├── GLOBALNO/        # Frontend platform
├── MODULI/          # Sandbox modulov
├── UPORABNIKI/      # User sandbox
├── PODATKI/         # Centralni storage
└── ASTRA/           # Admin world</pre>
            <?php elseif ($aktivniZavihek === 'moduli'): ?>
                <h2>Moduli</h2>
                <p>Moduli so izolirani bounded contexti, ki lahko razširjajo funkcionalnost sistema.</p>
                <h3>Struktura modula</h3>
                <pre class="dokumentacija-koda">MODULI/[KATEGORIJA]/[ImeModula]/
├── konfiguracija/manifest.json   # Obvezen
├── modul.php                     # Vstopna točka
├── logika/                       # Business logika
└── uporabnik/                    # Frontend (opcijsko)</pre>
                <h3>Manifest.json primer</h3>
                <pre class="dokumentacija-koda">{
    "ime": "Stelaris",
    "verzija": "1.0.0",
    "vloga": 20,
    "kanali": ["splet", "api"]
}</pre>
            <?php elseif ($aktivniZavihek === 'api'): ?>
                <h2>API dokumentacija</h2>
                <p>Sistem ponuja REST podoben API preko <code>api.php</code>.</p>
                <h3>Avtentikacija</h3>
                <p>API uporablja JWT žetone. V glavo dodajte:</p>
                <pre class="dokumentacija-koda">Authorization: Bearer {token}</pre>
                <h3>Endpointi</h3>
                <table class="tabela">
                    <tr><th>Endpoint</th><th>Metoda</th><th>Opis</th></tr>
                    <tr><td><code>/api/health</code></td><td>GET</td><td>Preverjanje stanja</td></tr>
                    <tr><td><code>/api/prijava</code></td><td>POST</td><td>Prijava uporabnika</td></tr>
                    <tr><td><code>/api/registracija</code></td><td>POST</td><td>Registracija uporabnika</td></tr>
                    <tr><td><code>/api/profil</code></td><td>GET</td><td>Pridobi profil</td></tr>
                    <tr><td><code>/api/moduli</code></td><td>GET</td><td>Seznam modulov</td></tr>
                </table>
            <?php elseif ($aktivniZavihek === 'razvoj'): ?>
                <h2>Razvoj</h2>
                <p>Za razvoj modulov uporabite <strong>Modul_Bridge</strong>.</p>
                <h3>Ustvari nov modul</h3>
                <pre class="dokumentacija-koda">php cli.php modul:generiraj MojModul</pre>
                <h3>Testiranje modula</h3>
                <pre class="dokumentacija-koda">php cli.php modul:test MojModul</pre>
                <h3>Pravila poimenovanja</h3>
                <ul>
                    <li>Funkcije: <code>podrocje_akcija()</code></li>
                    <li>Razredi: <code>PascalCase</code></li>
                    <li>Datoteke: <code>male_crke.php</code></li>
                    <li>CSS: <code>kebab-case</code>, slovensko</li>
                </ul>
            <?php elseif ($aktivniZavihek === 'pogosta_vprasanja'): ?>
                <h2>Pogosta vprašanja</h2>
                
                <details>
                    <summary>Kako namestim modul?</summary>
                    <p>Modul namestite preko ASTRA nadzorne plošče ali s CLI ukazom: <code>php cli.php modul:install ImeModula</code></p>
                </details>
                
                <details>
                    <summary>Kako dostopam do podatkov iz modula?</summary>
                    <p>Uporabite vgrajene funkcije: <code>baza_beri()</code>, <code>baza_zapisi()</code>, <code>cache_preberi()</code> itd.</p>
                </details>
                
                <details>
                    <summary>Kako pošljem asinhrono opravilo?</summary>
                    <p>Uporabite čakalno vrsto: <code>queue_dodaj(['akcija' => 'opravilo', 'podatki' => [...]])</code></p>
                </details>
                
                <details>
                    <summary>Kako dodam novo temo?</summary>
                    <p>Ustvarite mapo v <code>GLOBALNO/vmesnik/teme/</code> in dodajte <code>slog.css</code>.</p>
                </details>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.dokumentacija-stran {
    max-width: 1200px;
    margin: 0 auto;
}

.dokumentacija-glava {
    text-align: center;
    margin-bottom: 2rem;
}

.dokumentacija-naslov {
    font-size: 2rem;
    color: #e8c84a;
    margin-bottom: 0.5rem;
}

.dokumentacija-podnaslov {
    color: #aaa;
}

.dokumentacija-vsebina {
    display: flex;
    gap: 2rem;
}

.dokumentacija-meni {
    width: 250px;
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    padding: 1rem;
}

.dokumentacija-meni ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dokumentacija-meni li {
    margin-bottom: 0.5rem;
}

.dokumentacija-meni a {
    display: block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    color: #d4c5a9;
    text-decoration: none;
    transition: background 0.3s;
}

.dokumentacija-meni a:hover {
    background: rgba(232, 200, 74, 0.1);
}

.dokumentacija-meni li.aktivno a {
    background: rgba(232, 200, 74, 0.2);
    color: #e8c84a;
}

.dokumentacija-clanek {
    flex: 1;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    padding: 2rem;
}

.dokumentacija-clanek h2 {
    color: #e8c84a;
    margin-bottom: 1rem;
}

.dokumentacija-clanek h3 {
    color: #e8c84a;
    margin: 1.5rem 0 0.75rem 0;
}

.dokumentacija-clanek p {
    color: #d4c5a9;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.dokumentacija-clanek ul, .dokumentacija-clanek details {
    margin-bottom: 1rem;
}

.dokumentacija-koda {
    background: #0a0a1a;
    padding: 1rem;
    border-radius: 10px;
    overflow-x: auto;
    font-family: monospace;
    font-size: 0.85rem;
    margin: 1rem 0;
}

@media (max-width: 768px) {
    .dokumentacija-vsebina {
        flex-direction: column;
    }
    
    .dokumentacija-meni {
        width: 100%;
    }
}
</style>