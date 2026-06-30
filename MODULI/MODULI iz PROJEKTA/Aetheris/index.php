<?php
/**
 * INDEX.PHP - MOJ GLAVNI INDEX Z GLAVO IN NOGO
 * Vključuje aetheris.php in omogoča lep prikaz
 */

// Vključimo glavni controller
require_once 'aetheris.php';
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aetheris Forum - Duhovni Portal</title>
    <style>
        /* OSNOVNI STILI */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* GLAVA STILI */
        .glava {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            border-bottom: 3px solid #764ba2;
        }
        
        .logo-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 3.5rem;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .podnaslov {
            font-size: 1.3rem;
            color: #666;
            margin-bottom: 1rem;
        }
        
        .navigacija {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }
        
        .navigacija-gumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .navigacija-gumb:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* GLAVNA VSERINA STILI */
        .vsebina {
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
        }
        
        .dobrodoselca {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }
        
        .dobrodoselca h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .dobrodoselca p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .modul-okno {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 0;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            overflow: hidden;
        }
        
        .modul-glava {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .modul-glava h2 {
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modul-telo {
            padding: 2rem;
        }
        
        /* MREŽNI LAYOUT */
        .mreza {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 968px) {
            .mreza {
                grid-template-columns: 1fr;
            }
        }
        
        /* KARTICE STILI */
        .kartica {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .kartica h3 {
            color: #764ba2;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        /* FORME IN VNOSI */
        .vnos-skupina {
            margin-bottom: 1.5rem;
        }
        
        .vnos-skupina label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        
        .vnos-skupina input,
        .vnos-skupina select,
        .vnos-skupina textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .vnos-skupina input:focus,
        .vnos-skupina select:focus,
        .vnos-skupina textarea:focus {
            outline: none;
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
        }
        
        /* GUMBI STILI */
        .gumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .gumb:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .gumb:active {
            transform: translateY(0);
        }
        
        .gumb-polna-sirina {
            width: 100%;
            justify-content: center;
        }
        
        /* IZPIS OBMOČJA */
        .izpis-obmocje {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            max-height: 500px;
            overflow-y: auto;
            display: none;
        }
        
        .izpis-obmocje.prikazano {
            display: block;
        }
        
        /* SPOROČILA */
        .sporocilo {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
            font-weight: 600;
            display: none;
        }
        
        .sporocilo.prikazano {
            display: block;
        }
        
        .sporocilo-uspeh {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .sporocilo-napaka {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* INFORMACIJE */
        .info-seznam {
            list-style: none;
            padding: 0;
        }
        
        .info-seznam li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: between;
        }
        
        .info-seznam li:last-child {
            border-bottom: none;
        }
        
        /* NOGA STILI */
        .noga {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .noga-vsebina {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .noga a {
            color: #667eea;
            text-decoration: none;
        }
        
        .noga a:hover {
            text-decoration: underline;
        }
        
        /* ANIMACIJE */
        @keyframes pojavi {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animiran {
            animation: pojavi 0.6s ease-out;
        }
        
        /* POSEBNI ELEMENTI */
        .statistika-prikaz {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .statistika-element {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }
        
        .statistika-vrednost {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .statistika-opis {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- GLAVA -->
    <header class="glava">
        <div class="logo-container">
            <div class="logo">🌌 AETHERIS FORUM</div>
            <div class="podnaslov">Prostor za duhovne razprave in modrost</div>
            
            <nav class="navigacija">
                <button class="navigacija-gumb" onclick="preklopiModul('ai')">🔮 AI Orakelj</button>
                <button class="navigacija-gumb" onclick="preklopiModul('forum')">📚 Forum</button>
                <button class="navigacija-gumb" onclick="preklopiModul('statistika')">📊 Statistika</button>
                <button class="navigacija-gumb" onclick="preklopiModul('sistem')">⚙️ Sistem</button>
            </nav>
        </div>
    </header>

    <!-- GLAVNA VSERINA -->
    <main class="vsebina">
        <!-- DOBRODOŠLICA -->
        <section class="dobrodoselca animiran">
            <h1>Dobrodošli v Aetheris Forumu</h1>
            <p>Odprite vrata duhovnemu razumevanju z umetno inteligenco kot vašim vodnikom</p>
        </section>

        <!-- AI ORAKELJ MODUL -->
        <section id="modul-ai" class="modul-okno animiran">
            <div class="modul-glava">
                <h2>🔮 AI Orakelj</h2>
            </div>
            <div class="modul-telo">
                <div class="mreza">
                    <div class="kartica">
                        <h3>💭 Postavite Vprašanje</h3>
                        <div class="vnos-skupina">
                            <label>👤 Izberite Uporabnika:</label>
                            <select id="uporabnik-ai">
                                <option value="gost">Gost (Raven 0)</option>
                                <option value="registriran">Janez Novak (Raven 1)</option>
                                <option value="napredni">Marija Horvat (Raven 2)</option>
                                <option value="upravitelj">Admin Aetheris (Raven 3)</option>
                            </select>
                        </div>
                        
                        <div class="vnos-skupina">
                            <label>❓ Vaše Vprašanje:</label>
                            <textarea id="vprasanje-ai" rows="4" placeholder="Vnesite svoje duhovno vprašanje..."></textarea>
                        </div>
                        
                        <button class="gumb gumb-polna-sirina" onclick="vprasajAI()">
                            🎯 Pošljite Vprašanje AI-ju
                        </button>
                    </div>
                    
                    <div class="kartica">
                        <h3>📜 Zadnji Odgovori</h3>
                        <div id="ai-zgodovina" class="izpis-obmocje">
                            Tukaj se bodo prikazali odgovori AI-ja...
                        </div>
                        <button class="gumb" onclick="nalagajAIZgodovino()" style="margin-top: 1rem;">
                            📖 Naloži Zgodovino
                        </button>
                    </div>
                </div>
                
                <div id="ai-rezultat" class="izpis-obmocje">
                    <!-- AI odgovor se prikaže tukaj -->
                </div>
            </div>
        </section>

        <!-- FORUM MODUL -->
        <section id="modul-forum" class="modul-okno" style="display: none;">
            <div class="modul-glava">
                <h2>📚 Upravljanje Foruma</h2>
            </div>
            <div class="modul-telo">
                <div class="mreza">
                    <div class="kartica">
                        <h3>➕ Nova Tema</h3>
                        <div class="vnos-skupina">
                            <label>📝 Naslov Teme:</label>
                            <input type="text" id="naslov-teme" placeholder="Vnesite naslov teme...">
                        </div>
                        
                        <div class="vnos-skupina">
                            <label>📄 Vsebina Teme:</label>
                            <textarea id="vsebina-teme" rows="3" placeholder="Vnesite vsebino teme..."></textarea>
                        </div>
                        
                        <div class="vnos-skupina">
                            <label>📂 Tematski Sklop:</label>
                            <select id="sklop-teme">
                                <option value="1">🔮 Ezoterika</option>
                                <option value="2">✨ Eterika</option>
                                <option value="3">⚡ Magija</option>
                                <option value="4">🔒 Skrita Soba</option>
                            </select>
                        </div>
                        
                        <button class="gumb gumb-polna-sirina" onclick="ustvariTemo()">
                            📝 Ustvari Novo Tema
                        </button>
                    </div>
                    
                    <div class="kartica">
                        <h3>👁️ Pregled Tem</h3>
                        <div class="vnos-skupina">
                            <label>🔍 Filtriraj po Sklopu:</label>
                            <select id="filter-sklop" onchange="nalagajTeme()">
                                <option value="">Vsi Sklopi</option>
                                <option value="1">🔮 Ezoterika</option>
                                <option value="2">✨ Eterika</option>
                                <option value="3">⚡ Magija</option>
                            </select>
                        </div>
                        
                        <div id="seznam-tem" class="izpis-obmocje">
                            Tukaj se bodo prikazale teme...
                        </div>
                        
                        <button class="gumb" onclick="nalagajTeme()" style="margin-top: 1rem;">
                            🔄 Osveži Teme
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATISTIKA MODUL -->
        <section id="modul-statistika" class="modul-okno" style="display: none;">
            <div class="modul-glava">
                <h2>📊 Statistika Sistema</h2>
            </div>
            <div class="modul-telo">
                <div class="statistika-prikaz" id="statistika-vnosno">
                    <!-- Statistika se dinamično naloži -->
                </div>
                
                <div class="kartica">
                    <h3>📈 Podrobna Statistika</h3>
                    <button class="gumb" onclick="nalagajStatistiko()">
                        📊 Naloži Celotno Statistiko
                    </button>
                    <div id="podrobna-statistika" class="izpis-obmocje">
                        Kliknite gumb za prikaz statistike...
                    </div>
                </div>
            </div>
        </section>

        <!-- SISTEM MODUL -->
        <section id="modul-sistem" class="modul-okno" style="display: none;">
            <div class="modul-glava">
                <h2>⚙️ Sistemske Nastavitve</h2>
            </div>
            <div class="modul-telo">
                <div class="mreza">
                    <div class="kartica">
                        <h3>🔐 Testiranje Dostopov</h3>
                        <div class="vnos-skupina">
                            <label>👤 Uporabnik:</label>
                            <select id="test-uporabnik">
                                <option value="gost">Gost</option>
                                <option value="registriran">Registriran</option>
                                <option value="napredni">Napredni</option>
                                <option value="upravitelj">Upravitelj</option>
                            </select>
                        </div>
                        
                        <div class="vnos-skupina">
                            <label>🎯 Zahtevana Raven:</label>
                            <select id="test-raven">
                                <option value="0">Raven 0 (Gost)</option>
                                <option value="1">Raven 1 (Registriran)</option>
                                <option value="2">Raven 2 (Napredni)</option>
                                <option value="3">Raven 3 (Upravitelj)</option>
                            </select>
                        </div>
                        
                        <button class="gumb gumb-polna-sirina" onclick="testirajDostop()">
                            🔍 Testiraj Dostop
                        </button>
                        
                        <div id="rezultat-dostopa" class="izpis-obmocje" style="margin-top: 1rem; display: none;">
                        </div>
                    </div>
                    
                    <div class="kartica">
                        <h3>🔄 Sistemski Podatki</h3>
                        <button class="gumb gumb-polna-sirina" onclick="nalagajOsnovnePodatke()">
                            📋 Naloži Osnovne Podatke
                        </button>
                        
                        <div id="osnovni-podatki" class="izpis-obmocje" style="margin-top: 1rem; display: none;">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SPOROČILA -->
        <div id="sporocila">
            <!-- Dinamična sporočila se prikažejo tukaj -->
        </div>
    </main>

    <!-- NOGA -->
    <footer class="noga">
        <div class="noga-vsebina">
            <p><strong>Aetheris Forum</strong> - Integriran sistem za duhovne razprave</p>
            <p>🌌 Frontend z glavo in nogo + Backend API 🌌</p>
            <p style="margin-top: 1rem; font-size: 0.9rem;">
                &copy; 2024 Aetheris Sistem | Vse pravice pridržane | 
                <a href="aetheris.php" target="_blank">API Dostop</a>
            </p>
        </div>
    </footer>

    <script>
        // GLOBALNE SPREMENLJIVKE
        let trenutniModul = 'ai';
        
        // INICIALIZACIJA
        document.addEventListener('DOMContentLoaded', function() {
            prikaziModul('ai');
            nalagajOsnovnoStatistiko();
        });
        
        // MODUL FUNKCIJE
        function preklopiModul(modul) {
            prikaziModul(modul);
            trenutniModul = modul;
        }
        
        function prikaziModul(modul) {
            // Skrij vse module
            document.querySelectorAll('.modul-okno').forEach(m => {
                m.style.display = 'none';
            });
            
            // Prikaži izbrani modul
            const izbraniModul = document.getElementById(`modul-${modul}`);
            if (izbraniModul) {
                izbraniModul.style.display = 'block';
            }
        }
        
        // API FUNKCIJE
        async function kliciAetherisApi(akcija, dodatniPodatki = {}) {
            try {
                pokaziNalaganje();
                
                const podatki = new URLSearchParams({
                    akcija: akcija,
                    ...dodatniPodatki
                });
                
                const odziv = await fetch('aetheris.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: podatki
                });
                
                const rezultat = await odziv.json();
                obdelajApiRezultat(rezultat);
                return rezultat;
                
            } catch (napaka) {
                pokaziSporocilo('Napaka v omrežju: ' + napaka.message, 'napaka');
                return { status: 'napaka', sporocilo: 'Omrežna napaka' };
            }
        }
        
        function obdelajApiRezultat(rezultat) {
            if (rezultat.status === 'uspeh') {
                pokaziSporocilo(rezultat.sporocilo || 'Operacija uspešna!', 'uspeh');
            } else {
                pokaziSporocilo(rezultat.sporocilo || 'Prišlo je do napake!', 'napaka');
            }
        }
        
        // FUNKCIJE ZA MODULE
        async function vprasajAI() {
            const vprasanje = document.getElementById('vprasanje-ai').value;
            const uporabnik = document.getElementById('uporabnik-ai').value;
            
            if (!vprasanje.trim()) {
                pokaziSporocilo('Vnesite vprašanje!', 'napaka');
                return;
            }
            
            const rezultat = await kliciAetherisApi('vprasaj_ai', {
                vprasanje: vprasanje,
                uporabnik: uporabnik
            });
            
            if (rezultat.status === 'uspeh') {
                const aiRezultat = document.getElementById('ai-rezultat');
                aiRezultat.textContent = rezultat.odgovor;
                aiRezultat.classList.add('prikazano');
                
                // Počisti polje
                document.getElementById('vprasanje-ai').value = '';
            }
        }
        
        async function nalagajAIZgodovino() {
            const rezultat = await kliciAetherisApi('pridobi_ai_zgodovino', { stevilo: 5 });
            
            if (rezultat.status === 'uspeh') {
                const zgodovinaElement = document.getElementById('ai-zgodovina');
                zgodovinaElement.textContent = JSON.stringify(rezultat.zgodovina, null, 2);
                zgodovinaElement.classList.add('prikazano');
            }
        }
        
        async function ustvariTemo() {
            const naslov = document.getElementById('naslov-teme').value;
            const vsebina = document.getElementById('vsebina-teme').value;
            const uporabnik = document.getElementById('uporabnik-ai').value;
            const sklop = document.getElementById('sklop-teme').value;
            
            if (!naslov.trim() || !vsebina.trim()) {
                pokaziSporocilo('Vnesite naslov in vsebino!', 'napaka');
                return;
            }
            
            await kliciAetherisApi('ustvari_temo', {
                naslov: naslov,
                vsebina: vsebina,
                uporabnik: uporabnik,
                sklop: sklop
            });
            
            // Počisti polja
            document.getElementById('naslov-teme').value = '';
            document.getElementById('vsebina-teme').value = '';
        }
        
        async function nalagajTeme() {
            const sklop = document.getElementById('filter-sklop').value;
            const podatki = {};
            
            if (sklop) {
                podatki.sklop_id = sklop;
            }
            
            const rezultat = await kliciAetherisApi('pridobi_teme', podatki);
            
            if (rezultat.status === 'uspeh') {
                const temeElement = document.getElementById('seznam-tem');
                temeElement.textContent = JSON.stringify(rezultat.teme, null, 2);
                temeElement.classList.add('prikazano');
            }
        }
        
        async function nalagajStatistiko() {
            const rezultat = await kliciAetherisApi('pridobi_statistiko');
            
            if (rezultat.status === 'uspeh') {
                const statistikaElement = document.getElementById('podrobna-statistika');
                statistikaElement.textContent = JSON.stringify(rezultat, null, 2);
                statistikaElement.classList.add('prikazano');
            }
        }
        
        async function nalagajOsnovnoStatistiko() {
            const rezultat = await kliciAetherisApi('pridobi_statistiko');
            
            if (rezultat.status === 'uspeh') {
                const statistika = rezultat.statistika;
                const vnosno = document.getElementById('statistika-vnosno');
                
                vnosno.innerHTML = `
                    <div class="statistika-element">
                        <div class="statistika-vrednost">${statistika.stevilo_tem}</div>
                        <div class="statistika-opis">Teme</div>
                    </div>
                    <div class="statistika-element">
                        <div class="statistika-vrednost">${statistika.stevilo_vprasanj}</div>
                        <div class="statistika-opis">Vprašanja</div>
                    </div>
                    <div class="statistika-element">
                        <div class="statistika-vrednost">${statistika.stevilo_odgovorov}</div>
                        <div class="statistika-opis">Odgovori</div>
                    </div>
                    <div class="statistika-element">
                        <div class="statistika-vrednost">${statistika.uporabniki}</div>
                        <div class="statistika-opis">Uporabniki</div>
                    </div>
                `;
            }
        }
        
        async function testirajDostop() {
            const uporabnik = document.getElementById('test-uporabnik').value;
            const raven = document.getElementById('test-raven').value;
            
            const rezultat = await kliciAetherisApi('testiraj_dostop', {
                uporabnik: uporabnik,
                raven: raven
            });
            
            if (rezultat.status === 'uspeh') {
                const dostopElement = document.getElementById('rezultat-dostopa');
                dostopElement.textContent = `Dostop: ${rezultat.dostop ? '✅ DOVOLJEN' : '❌ ZAVRNJEN'}\nUporabnik: ${rezultat.uporabnik}\nZahtevana raven: ${rezultat.zahtevana_raven}`;
                dostopElement.classList.add('prikazano');
            }
        }
        
        async function nalagajOsnovnePodatke() {
            const rezultat = await kliciAetherisApi('pridobi_osnovne_podatke');
            
            if (rezultat.status === 'uspeh') {
                const podatkiElement = document.getElementById('osnovni-podatki');
                podatkiElement.textContent = JSON.stringify(rezultat, null, 2);
                podatkiElement.classList.add('prikazano');
            }
        }
        
        // POMOŽNE FUNKCIJE
        function pokaziSporocilo(sporocilo, tip) {
            const sporocilaElement = document.getElementById('sporocila');
            const sporociloElement = document.createElement('div');
            
            sporociloElement.className = `sporocilo sporocilo-${tip} prikazano`;
            sporociloElement.textContent = sporocilo;
            
            sporocilaElement.appendChild(sporociloElement);
            
            // Samodejno odstrani po 5 sekundah
            setTimeout(() => {
                sporociloElement.remove();
            }, 5000);
        }
        
        function pokaziNalaganje() {
            pokaziSporocilo('Nalagam...', 'uspeh');
        }
    </script>
</body>
</html>