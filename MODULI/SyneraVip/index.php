<?php
declare(strict_types=1);

/**
 * index.php - Moj index z glavo in nogo, vključuje Synera.php
 * Obeznostna datoteka: index.php
 */

class IndexStran {
    
    public static function zagon(): void {
        self::izrisiGlavo();
        self::izrisiVsebino();
        self::izrisiNogo();
    }
    
    public static function izrisiGlavo(string $naslov = "Synera Platforma"): void {
        ?>
        <!DOCTYPE html>
        <html lang="sl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($naslov) ?></title>
            <style>
                :root {
                    --primarna: #4FC3F7;
                    --primarna-temna: #29B6F6;
                    --sekundarna: #7986CB;
                    --ozadje: linear-gradient(135deg, #0f2b46 0%, #1a3a5f 100%);
                    --kartica-ozadje: rgba(255,255,255,0.08);
                    --kartica-obroba: rgba(255,255,255,0.15);
                }
                
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: var(--ozadje);
                    color: white;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    line-height: 1.6;
                }
                
                .glava {
                    background: rgba(255,255,255,0.1);
                    backdrop-filter: blur(15px);
                    padding: 1.5rem 2rem;
                    border-bottom: 2px solid rgba(79, 195, 247, 0.3);
                    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                }
                
                .glava-vsebina {
                    max-width: 1200px;
                    margin: 0 auto;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 1rem;
                }
                
                .glava-naslov {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                }
                
                .logo {
                    font-size: 3rem;
                    animation: utripanje 3s infinite;
                    filter: drop-shadow(0 0 10px rgba(79, 195, 247, 0.5));
                }
                
                @keyframes utripanje {
                    0%, 100% { transform: scale(1) rotate(0deg); opacity: 1; }
                    25% { transform: scale(1.05) rotate(5deg); }
                    50% { transform: scale(1.1) rotate(0deg); opacity: 0.9; }
                    75% { transform: scale(1.05) rotate(-5deg); }
                }
                
                .glava h1 {
                    font-size: 2.2rem;
                    font-weight: 300;
                    background: linear-gradient(135deg, var(--primarna), var(--primarna-temna));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-shadow: 0 2px 10px rgba(79, 195, 247, 0.3);
                }
                
                .navigacija {
                    display: flex;
                    gap: 1rem;
                    flex-wrap: wrap;
                }
                
                .navigacija-gumb {
                    background: rgba(79, 195, 247, 0.15);
                    color: var(--primarna);
                    border: 1px solid rgba(79, 195, 247, 0.3);
                    padding: 0.7rem 1.5rem;
                    border-radius: 25px;
                    text-decoration: none;
                    transition: all 0.3s ease;
                    font-weight: 500;
                    backdrop-filter: blur(10px);
                }
                
                .navigacija-gumb:hover {
                    background: rgba(79, 195, 247, 0.25);
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(79, 195, 247, 0.3);
                }
                
                .vsebina {
                    flex: 1;
                    padding: 2rem;
                    max-width: 1200px;
                    margin: 0 auto;
                    width: 100%;
                }
                
                .noga {
                    background: rgba(0,0,0,0.4);
                    padding: 2.5rem 2rem;
                    margin-top: 4rem;
                    border-top: 1px solid rgba(255,255,255,0.1);
                }
                
                .pozdrav {
                    text-align: center;
                    margin-bottom: 4rem;
                    padding: 4rem 2rem;
                    background: var(--kartica-ozadje);
                    border-radius: 25px;
                    backdrop-filter: blur(20px);
                    border: 1px solid var(--kartica-obroba);
                    position: relative;
                    overflow: hidden;
                }
                
                .pozdrav::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 3px;
                    background: linear-gradient(90deg, var(--primarna), var(--sekundarna));
                }
                
                .pozdrav h1 {
                    font-size: 3.5rem;
                    margin-bottom: 1.5rem;
                    background: linear-gradient(135deg, var(--primarna), #81D4FA);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-shadow: 0 4px 20px rgba(79, 195, 247, 0.3);
                }
                
                .pozdrav p {
                    font-size: 1.3rem;
                    opacity: 0.9;
                    max-width: 700px;
                    margin: 0 auto;
                    line-height: 1.7;
                }
                
                .sistemske-kartice {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                    gap: 2rem;
                    margin-bottom: 4rem;
                }
                
                .kartica {
                    background: var(--kartica-ozadje);
                    padding: 2.5rem;
                    border-radius: 20px;
                    backdrop-filter: blur(15px);
                    border: 1px solid var(--kartica-obroba);
                    transition: all 0.4s ease;
                    text-align: center;
                    position: relative;
                }
                
                .kartica::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 3px;
                    background: linear-gradient(90deg, var(--primarna), var(--sekundarna));
                    border-radius: 20px 20px 0 0;
                }
                
                .kartica:hover {
                    transform: translateY(-10px) scale(1.02);
                    background: rgba(255,255,255,0.12);
                    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                    border-color: rgba(79, 195, 247, 0.4);
                }
                
                .kartica h3 {
                    font-size: 1.6rem;
                    margin-bottom: 1.2rem;
                    color: var(--primarna);
                }
                
                .kartica p {
                    opacity: 0.8;
                    line-height: 1.7;
                    margin-bottom: 2rem;
                }
                
                .gumb {
                    background: linear-gradient(135deg, var(--primarna), var(--primarna-temna));
                    color: white;
                    border: none;
                    padding: 14px 28px;
                    border-radius: 12px;
                    cursor: pointer;
                    font-size: 1.1rem;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    width: 100%;
                    position: relative;
                    overflow: hidden;
                }
                
                .gumb::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                    transition: left 0.5s;
                }
                
                .gumb:hover {
                    background: linear-gradient(135deg, var(--primarna-temna), #0288D1);
                    transform: translateY(-3px);
                    box-shadow: 0 10px 25px rgba(41, 182, 246, 0.4);
                }
                
                .gumb:hover::before {
                    left: 100%;
                }
                
                .vgrajena-vsebina {
                    background: var(--kartica-ozadje);
                    border-radius: 20px;
                    padding: 2.5rem;
                    margin-top: 2rem;
                    backdrop-filter: blur(15px);
                    border: 1px solid var(--kartica-obroba);
                    min-height: 400px;
                }
                
                /* Synera specifični stili */
                .synera-vsebina {
                    padding: 2rem;
                }
                
                .komandna-plosca {
                    background: rgba(0,0,0,0.2);
                    padding: 2rem;
                    border-radius: 15px;
                    margin-bottom: 2rem;
                }
                
                .stanje-sistemov {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1rem;
                    margin: 1.5rem 0;
                }
                
                .sistem {
                    background: rgba(255,255,255,0.1);
                    padding: 1rem;
                    border-radius: 10px;
                    border-left: 4px solid var(--primarna);
                }
                
                .status-zelen { border-left-color: #4CAF50; }
                .status-rumen { border-left-color: #FF9800; }
                .status-rdeč { border-left-color: #F44336; }
                
                .komandne-moznosti {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 1rem;
                    margin-top: 1.5rem;
                }
                
                @media (max-width: 768px) {
                    .glava-vsebina {
                        flex-direction: column;
                        text-align: center;
                    }
                    
                    .navigacija {
                        justify-content: center;
                    }
                    
                    .pozdrav h1 {
                        font-size: 2.5rem;
                    }
                    
                    .sistemske-kartice {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <header class="glava">
                <div class="glava-vsebina">
                    <div class="glava-naslov">
                        <div class="logo">🔮</div>
                        <h1>Synera Platforma</h1>
                    </div>
                    <nav class="navigacija">
                        <a href="?akcija=prikaz" class="navigacija-gumb">🏠 Domov</a>
                        <a href="?akcija=komandna_plosca" class="navigacija-gumb">🎯 Komanda</a>
                        <a href="?akcija=statistika" class="navigacija-gumb">📈 Statistika</a>
                        <a href="?akcija=elementi" class="navigacija-gumb">🧩 Elementi</a>
                        <a href="?akcija=nastavitve" class="navigacija-gumb">⚙️ Nastavitve</a>
                    </nav>
                </div>
            </header>
            
            <main class="vsebina">
        <?php
    }
    
    public static function izrisiVsebino(): void {
        ?>
        <div class="pozdrav">
            <h1>🔮 Dobrodošli v Syneri</h1>
            <p>Napredna platforma za delo s simboli, runami in mantrami. AI-podprta analiza energije in personalizirani rituali za duhovno transformacijo.</p>
        </div>

        <div class="sistemske-kartice">
            <div class="kartica">
                <h3>🎯 Komandna Plošča</h3>
                <p>Upravljajte sisteme preko AI komand. Generirajte sigile, analizirajte energijo in aktivirajte sveto geometrijo.</p>
                <button onclick="odpriKomandnoPlosco()" class="gumb">Odpri Komandno Ploščo</button>
            </div>
            
            <div class="kartica">
                <h3>📚 Knjižnica Simbolov</h3>
                <p>Raziskujte rune, mantre in sveto geometrijo. Preučujte pomen, frekvence in energetske vzorce.</p>
                <button onclick="odpriKnjiznico()" class="gumb">Razišči Knjižnico</button>
            </div>
            
            <div class="kartica">
                <h3>🤖 AI Analiza</h3>
                <p>Inteligentna analiza energije in priporočila. Personalizirani rituali in energetski vpogledi.</p>
                <button onclick="izvediAIanalizo()" class="gumb">Zaženi Analizo</button>
            </div>
        </div>

        <div class="vgrajena-vsebina" id="vgrajena-vsebina">
            <?php
            // Vključimo Synera.php za prikaz sistemskih informacij
            if (file_exists(__DIR__ . '/Synera.php')) {
                require_once __DIR__ . '/Synera.php';
            } else {
                echo '<div style="text-align: center; padding: 3rem;">';
                echo '<h3 style="color: #4FC3F7; margin-bottom: 1rem;">🤖 Sistem se pripravlja</h3>';
                echo '<p style="opacity: 0.8;">Synera platforma se inicializira...</p>';
                echo '</div>';
            }
            ?>
        </div>
        <?php
    }
    
    public static function izrisiNogo(): void {
        ?>
            </main>
            
            <footer class="noga">
                <div style="max-width: 1200px; margin: 0 auto;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 3rem; text-align: left;">
                        <div>
                            <h3 style="color: #4FC3F7; margin-bottom: 1.5rem; font-size: 1.3rem;">Synera AI Platforma</h3>
                            <p style="opacity: 0.8; line-height: 1.7;">
                                Vodilna rešitev za delo s simboli, runami in mantrami. 
                                Združujemo starodavno modrost s sodobno AI tehnologijo.
                            </p>
                        </div>
                        <div>
                            <h3 style="color: #4FC3F7; margin-bottom: 1.5rem; font-size: 1.3rem;">Sistemske Komponente</h3>
                            <ul style="opacity: 0.8; list-style: none; line-height: 2;">
                                <li>✓ SyneraJedro - Osnovno jedro</li>
                                <li>✓ AI_Synera - Inteligentni sistem</li>
                                <li>✓ Generator Sigilov</li>
                                <li>✓ Sveta Geometrija</li>
                                <li>✓ Analizator Energije</li>
                            </ul>
                        </div>
                        <div>
                            <h3 style="color: #4FC3F7; margin-bottom: 1.5rem; font-size: 1.3rem;">Stanje Sistemov</h3>
                            <p style="color: #4CAF50; font-size: 1.1rem; margin-bottom: 0.5rem;">
                                🟢 Vsi sistemi delujejo optimalno
                            </p>
                            <p style="opacity: 0.8; font-size: 0.95rem;">
                                Zadnja posodobitev: <?= date('d.m.Y H:i:s') ?>
                            </p>
                            <p style="opacity: 0.8; font-size: 0.95rem;">
                                Verzija: 2.1.0
                            </p>
                        </div>
                    </div>
                    <div style="border-top: 1px solid rgba(255,255,255,0.2); margin-top: 3rem; padding-top: 2rem; text-align: center; opacity: 0.6;">
                        <p>&copy; 2024 Synera - Simboli, Rune, Mantre. Vse pravice pridržane.</p>
                        <p style="margin-top: 0.5rem; font-size: 0.9rem;">Razvito z ❤️ za duhovno rast in transformacijo</p>
                    </div>
                </div>
            </footer>
            
            <script>
                // Navigacijske funkcije
                function odpriKomandnoPlosco() {
                    window.location.href = '?akcija=komandna_plosca';
                }
                
                function odpriKnjiznico() {
                    window.location.href = '?akcija=knjiznica';
                }
                
                function izvediAIanalizo() {
                    window.location.href = '?akcija=analiza';
                }
                
                // AJAX komande za live posodabljanje
                function izvediKomando(komanda) {
                    fetch('Synera.php?api=1', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            akcija: 'komanda',
                            komanda: komanda
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.uspeh) {
                            alert('✅ ' + data.sporocilo);
                        } else {
                            alert('❌ ' + data.napaka);
                        }
                    })
                    .catch(error => {
                        alert('❌ Napaka pri povezavi: ' + error);
                    });
                }
                
                // Live posodabljanje statistike
                function osveziStatistiko() {
                    fetch('Synera.php?api=1&akcija=statistika')
                    .then(response => response.json())
                    .then(data => {
                        if (data.uspeh) {
                            console.log('Statistika osvežena:', data.statistika);
                        }
                    });
                }
                
                // Avtomatsko osveževanje vsakih 30 sekund
                setInterval(osveziStatistiko, 30000);
            </script>
        </body>
        </html>
        <?php
    }
}

// Zaženemo index stran
IndexStran::zagon();
?>