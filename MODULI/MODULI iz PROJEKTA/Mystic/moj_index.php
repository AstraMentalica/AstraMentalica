<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul Mystica - Magicni Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .header {
            grid-column: 1 / -1;
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #4cc9f0;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5em;
            background: linear-gradient(45deg, #4cc9f0, #4361ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .panel {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .panel h2 {
            color: #4cc9f0;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #a9d6e5;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #4361ee;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }
        
        input::placeholder {
            color: #a9d6e5;
        }
        
        button {
            background: linear-gradient(45deg, #4361ee, #4cc9f0);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .console {
            background: #000;
            color: #0f0;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            height: 300px;
            overflow-y: auto;
            font-size: 12px;
        }
        
        .status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: rgba(76, 201, 240, 0.1);
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .magic-bar {
            height: 10px;
            background: linear-gradient(90deg, #4361ee, #4cc9f0);
            border-radius: 5px;
            margin-top: 5px;
        }
        
        .response {
            grid-column: 1 / -1;
        }
        
        pre {
            background: #1a1a2e;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            color: #a9d6e5;
        }
        
        .token-display {
            word-break: break-all;
            font-size: 12px;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔮 Modul Mystica</h1>
            <p>Magicni portal za skrivne izkusnje</p>
        </div>
        
        <!-- Panel za prijavo -->
        <div class="panel">
            <h2>🔑 Prijava v Portal</h2>
            <div class="form-group">
                <label for="uporabnisko_ime">Uporabnisko ime:</label>
                <input type="text" id="uporabnisko_ime" placeholder="Vnesite uporabnisko ime">
            </div>
            <div class="form-group">
                <label for="geslo">Geslo:</label>
                <input type="password" id="geslo" placeholder="Vnesite geslo">
            </div>
            <button onclick="prijaviUporabnika()">Prijava v Portal</button>
            <div id="tokenDisplay" class="token-display" style="display: none;">
                Token: <span id="tokenValue"></span>
            </div>
        </div>
        
        <!-- Panel za magicne dogodke -->
        <div class="panel">
            <h2>✨ Magicni Dogodki</h2>
            <div class="form-group">
                <label for="tip_dogodka">Tip dogodka:</label>
                <select id="tip_dogodka">
                    <option value="nakljucni">Nakljucni dogodek</option>
                    <option value="ritual">Ritual</option>
                </select>
            </div>
            <div class="form-group">
                <label for="lokacija">Lokacija:</label>
                <select id="lokacija">
                    <option value="osrednja_dvorana">Osrednja dvorana</option>
                    <option value="knjiznica">Knjiznica</option>
                    <option value="vrt_alhemije">Vrt alhemije</option>
                </select>
            </div>
            <button onclick="zazeniMagicniDogodek()">Aktiviraj Dogodek</button>
        </div>
        
        <!-- Panel za stanje sistema -->
        <div class="panel">
            <h2>📊 Stanje Sistema</h2>
            <div class="status">
                <span>Magicna moc:</span>
                <span id="magicMoc">100%</span>
            </div>
            <div class="magic-bar" style="width: 100%"></div>
            <button onclick="pridobiStanje()">Osvezi Stanje</button>
        </div>
        
        <!-- Panel za cron naloge -->
        <div class="panel">
            <h2>⏰ Cron Naloge</h2>
            <div class="form-group">
                <label for="cron_tip">Tip naloge:</label>
                <select id="cron_tip">
                    <option value="redna">Redna naloga</option>
                    <option value="posebna">Posebna naloga</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cron_interval">Interval (sekunde):</label>
                <input type="number" id="cron_interval" value="3600" min="60">
            </div>
            <div class="form-group">
                <label for="cron_ukaz">Ukaz:</label>
                <input type="text" id="cron_ukaz" placeholder="pridobi_stanje">
            </div>
            <button onclick="nastaviCron()">Nastavi Cron</button>
        </div>
        
        <!-- Panel za komunikacijo -->
        <div class="panel">
            <h2>💬 Komunikacija</h2>
            <div class="form-group">
                <label for="sporocilo">Sporocilo:</label>
                <textarea id="sporocilo" rows="3" placeholder="Vnesite vase sporocilo..."></textarea>
            </div>
            <div class="form-group">
                <label for="prejemnik">Prejemnik:</label>
                <select id="prejemnik">
                    <option value="sistem">Sistem</option>
                    <option value="portal">Portal</option>
                    <option value="magicni_svet">Magicni svet</option>
                </select>
            </div>
            <button onclick="posljiSporocilo()">Poslji Sporocilo</button>
        </div>
        
        <!-- Konzola za odzive -->
        <div class="panel response">
            <h2>📝 Odziv Sistema</h2>
            <div class="console" id="console">
&gt; Sistem pripravljen. Modul Mystica caka na ukaze...
            </div>
            <button onclick="ocistiKonzolo()" style="margin-top: 10px;">Ocisti Konzolo</button>
        </div>
    </div>

    <script>
        let trenutniToken = '';
        
        function dodajVKonzolo(tekst) {
            const konzola = document.getElementById('console');
            konzola.innerHTML += '\n&gt; ' + tekst;
            konzola.scrollTop = konzola.scrollHeight;
        }
        
        function prikaziToken(token) {
            trenutniToken = token;
            document.getElementById('tokenDisplay').style.display = 'block';
            document.getElementById('tokenValue').textContent = token;
        }
        
        async function posljiZahtevo(ukaz, podatki = {}) {
            if (trenutniToken && ukaz !== 'prijava') {
                podatki.token = trenutniToken;
            }
            
            const zahteva = {
                ukaz: ukaz,
                podatki: podatki
            };
            
            try {
                const odgovor = await fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(zahteva)
                });
                
                const podatki = await odgovor.json();
                return podatki;
            } catch (napaka) {
                return {
                    uspeh: false,
                    napaka: 'Omrezna napaka: ' + napaka.message
                };
            }
        }
        
        async function prijaviUporabnika() {
            const uporabniskoIme = document.getElementById('uporabnisko_ime').value;
            const geslo = document.getElementById('geslo').value;
            
            if (!uporabniskoIme || !geslo) {
                dodajVKonzolo('Napaka: Vnesite uporabnisko ime in geslo');
                return;
            }
            
            dodajVKonzolo('Prijavljam uporabnika: ' + uporabniskoIme);
            
            const odgovor = await posljiZahtevo('prijava', {
                uporabnisko_ime: uporabniskoIme,
                geslo: geslo
            });
            
            if (odgovor.uspeh) {
                dodajVKonzolo('Uspešna prijava! Dobrodosli, ' + odgovor.uporabnik.uporabnisko_ime);
                prikaziToken(odgovor.token);
                posodobiMagicnoMoc(odgovor.stanje_portala.energija);
            } else {
                dodajVKonzolo('Napaka pri prijavi: ' + odgovor.napaka);
            }
        }
        
        async function zazeniMagicniDogodek() {
            if (!trenutniToken) {
                dodajVKonzolo('Napaka: Najprej se prijavite');
                return;
            }
            
            const tipDogodka = document.getElementById('tip_dogodka').value;
            const lokacija = document.getElementById('lokacija').value;
            
            dodajVKonzolo('Zaganjam magicni dogodek: ' + tipDogodka + ' v ' + lokacija);
            
            const odgovor = await posljiZahtevo('magicni_dogodek', {
                tip: tipDogodka,
                lokacija: lokacija
            });
            
            if (odgovor.uspeh) {
                dodajVKonzolo('Dogodek uspesno aktiviran: ' + odgovor.dogodek.opis);
                dodajVKonzolo('Magicna moc dogodka: ' + odgovor.dogodek.magicna_moc + '%');
            } else {
                dodajVKonzolo('Napaka pri dogodku: ' + odgovor.napaka);
            }
        }
        
        async function pridobiStanje() {
            dodajVKonzolo('Pridobivam stanje sistema...');
            
            const odgovor = await posljiZahtevo('pridobi_stanje');
            
            if (odgovor.uspeh) {
                dodajVKonzolo('Stanje sistema uspesno pridobljeno');
                posodobiMagicnoMoc(odgovor.stanje.magicna_moc);
            } else {
                dodajVKonzolo('Napaka pri pridobivanju stanja: ' + odgovor.napaka);
            }
        }
        
        async function nastaviCron() {
            if (!trenutniToken) {
                dodajVKonzolo('Napaka: Najprej se prijavite');
                return;
            }
            
            const tip = document.getElementById('cron_tip').value;
            const interval = document.getElementById('cron_interval').value;
            const ukaz = document.getElementById('cron_ukaz').value;
            
            if (!ukaz) {
                dodajVKonzolo('Napaka: Vnesite ukaz za cron nalogo');
                return;
            }
            
            dodajVKonzolo('Nastavljam cron nalogo: ' + ukaz);
            
            const odgovor = await posljiZahtevo('nastavi_cron', {
                tip: tip,
                interval: parseInt(interval),
                ukaz: ukaz
            });
            
            if (odgovor.uspeh) {
                dodajVKonzolo('Cron naloga uspesno nastavljena: ' + odgovor.cron_naloga.id);
            } else {
                dodajVKonzolo('Napaka pri nastavljanju cron: ' + odgovor.napaka);
            }
        }
        
        async function posljiSporocilo() {
            if (!trenutniToken) {
                dodajVKonzolo('Napaka: Najprej se prijavite');
                return;
            }
            
            const sporocilo = document.getElementById('sporocilo').value;
            const prejemnik = document.getElementById('prejemnik').value;
            
            if (!sporocilo) {
                dodajVKonzolo('Napaka: Vnesite sporocilo');
                return;
            }
            
            dodajVKonzolo('Posiljam sporocilo za: ' + prejemnik);
            
            const odgovor = await posljiZahtevo('komunikacija', {
                sporocilo: sporocilo,
                prejemnik: prejemnik
            });
            
            if (odgovor.uspeh) {
                dodajVKonzolo('Odziv: ' + odgovor.odgovor);
            } else {
                dodajVKonzolo('Napaka pri komunikaciji: ' + odgovor.napaka);
            }
        }
        
        function posodobiMagicnoMoc(moc) {
            const mocStevilka = parseInt(moc);
            document.getElementById('magicMoc').textContent = moc + '%';
            document.querySelector('.magic-bar').style.width = moc + '%';
            
            // Spremeni barvo glede na moc
            const magicBar = document.querySelector('.magic-bar');
            if (mocStevilka > 70) {
                magicBar.style.background = 'linear-gradient(90deg, #4361ee, #4cc9f0)';
            } else if (mocStevilka > 40) {
                magicBar.style.background = 'linear-gradient(90deg, #f72585, #4361ee)';
            } else {
                magicBar.style.background = 'linear-gradient(90deg, #7209b7, #f72585)';
            }
        }
        
        function ocistiKonzolo() {
            document.getElementById('console').innerHTML = '&gt; Konzola ociscena...';
        }
        
        // Samodejno pridobi stanje ob zagonu
        window.addEventListener('load', function() {
            dodajVKonzolo('Sistem Modul Mystica je pripravljen');
            dodajVKonzolo('Uporabite prijavo za dostop do magicnih funkcij');
        });
    </script>
</body>
</html>