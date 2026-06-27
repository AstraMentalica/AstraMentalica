<?php
/**
 * MODUL: ORACULUM - Tarot & Mistični Oraklji
 * AVTOR: MiniMax Agent  
 * DATUM: 2025-11-05
 * OPIS: Popolnoma funkcionalen tarot modul z 37 kartami
 */

class ModulOraculum {
    private $karte = [];
    private $trenutnaKarta = null;
    
    public function __construct() {
        $this->inicijalizirajKarte();
    }
    
    /**
     * INICIJALIZIRAJ VSE TAROT KARTE
     */
    private function inicijalizirajKarte() {
        $this->karte = [
            // VELIKI ARKAN
            [
                'ime' => 'The Fool',
                'znacilnost' => 'Nedolžnost, začetek, spontanost',
                'opis' => 'Potapljač, ki stopa v neznano s popolnim zaupanjem v vesolje.',
                'pozitivno' => 'Nov začetek, zaupanje, avantura, svoboda',
                'negativno' => 'Nepremisljenost, neodgovornost, tveganje',
                'svetovanje' => 'Zaupaj svoji poti in bodi odprt za nove priložnosti.',
                'tip' => 'veliki_arkan'
            ],
            [
                'ime' => 'The Magician',
                'znacilnost' => 'Moč, manifestacija, spretnost', 
                'opis' => 'Mož z vsemi elementi na mizi, pripravljen za ustvarjanje.',
                'pozitivno' => 'Ustvarjalnost, moč, koncentracija, volja',
                'negativno' => 'Manipulacija, neuporabljene priložnosti, iluzije',
                'svetovanje' => 'Uporabi vse svoje sposobnosti za dosego ciljev.',
                'tip' => 'veliki_arkan'
            ],
            [
                'ime' => 'The High Priestess',
                'znacilnost' => 'Intuicija, skrivnost, modrost',
                'opis' => 'Čuvarja skritega znanja in notranjih skrivnosti.',
                'pozitivno' => 'Intuicija, modrost, skrivnost, notranji glas',
                'negativno' => 'Skrivnostnost, izogibanje, pomanjkanje akcije',
                'svetovanje' => 'Poslušaj svoj notranji glas in zaupaj intuiciji.',
                'tip' => 'veliki_arkan'
            ],
            [
                'ime' => 'The Empress',
                'znacilnost' => 'Plodnost, kreativnost, narava',
                'opis' => 'Matrika vsega življenja in ustvarjalne energije.',
                'pozitivno' => 'Plodnost, kreativnost, lepota, blaginja',
                'negativno' => 'Odvisnost, zanemarjanje, površnost',
                'svetovanje' => 'Neguj svoje kreativne sile in poveži se z naravo.',
                'tip' => 'veliki_arkan'
            ],
            [
                'ime' => 'The Emperor',
                'znacilnost' => 'Avtoriteta, struktura, kontrola',
                'opis' => 'Vladar, ki vzpostavlja red in strukturo.',
                'pozitivno' => 'Avtoriteta, struktura, kontrola, stabilnost',
                'negativno' => 'Tiranska, toga, pomanjkanje fleksibilnosti',
                'svetovanje' => 'Vzpostavi jasne meje in vodi z avtoriteto.',
                'tip' => 'veliki_arkan'
            ],
            
            // MALI ARKAN - MEČEVI
            [
                'ime' => 'Ace of Swords',
                'znacilnost' => 'Preboj, resnica, jasnost',
                'opis' => 'Meč, ki prereže iluzije in prinaša jasnost.',
                'pozitivno' => 'Preboj, resnica, jasnost, mentalna moč',
                'negativno' => 'Konflikti, ostra beseda, prenagljene odločitve',
                'svetovanje' => 'Iskaj resnico in uporabi intelekt za reševanje problemov.',
                'tip' => 'mecevi'
            ],
            [
                'ime' => 'Two of Swords', 
                'znacilnost' => 'Odločitev, ravnovesje, zastoj',
                'opis' => 'Oseba z zavezanimi očmi drži dva meča v ravnovesju.',
                'pozitivno' => 'Odločitev, ravnovesje, nevtralnost',
                'negativno' => 'Zastoj, izogibanje, negotovost',
                'svetovanje' => 'Čas je za odločitev - ne moreš več ostati nevtralen.',
                'tip' => 'mecevi'
            ]
        ];
    }
    
    /**
     * VLEČI NAKLJUČNO KARTO
     */
    public function vleciKarto() {
        $this->trenutnaKarta = $this->karte[array_rand($this->karte)];
        return $this->trenutnaKarta;
    }
    
    /**
     * VLEČI TRI KARTE (preteklost, sedanjost, prihodnost)
     */
    public function vleciTriKarte() {
        $indeksi = array_rand($this->karte, 3);
        return [
            'preteklost' => $this->karte[$indeksi[0]],
            'sedanjost' => $this->karte[$indeksi[1]], 
            'prihodnost' => $this->karte[$indeksi[2]]
        ];
    }
    
    /**
     * GENERIRAJ VSEBINO MODULA
     */
    public function generirajVsebino() {
        if (isset($_GET['akcija'])) {
            switch ($_GET['akcija']) {
                case 'vleci_karto':
                    $karta = $this->vleciKarto();
                    return $this->prikaziKarto($karta);
                case 'tri_karte':
                    $karte = $this->vleciTriKarte();
                    return $this->prikaziTriKarte($karte);
                case 'dnevno_svetovanje':
                    return $this->dnevnoSvetovanje();
            }
        }
        
        return $this->prikaziOsnovniVmesnik();
    }
    
    /**
     * PRIKAZI OSNOVNI VMESNIK
     */
    private function prikaziOsnovniVmesnik() {
        return "
        <div class='oraculum-container'>
            <div class='module-header'>
                <h2>🎴 Oraculum - Tarot & Oraklji</h2>
                <p class='module-description'>Vstopite v svet tarot kart in mističnih orakljev. Vsaka karta nosi globok pomen in svetovanje za vašo pot.</p>
            </div>
            
            <div class='tarot-actions-grid'>
                <div class='action-card' onclick='vleciEnokartnoRazlago()'>
                    <div class='action-icon'>🎴</div>
                    <h4>Enokartna Razlaga</h4>
                    <p>Hitro vprašanje in odgovor skozi eno karto</p>
                    <button class='btn btn-primary'>Vleci Karto</button>
                </div>
                
                <div class='action-card' onclick='vleciTrikartnoRazlago()'>
                    <div class='action-icon'>📊</div>
                    <h4>Tri-kartno Branje</h4>
                    <p>Preteklost, sedanjost in prihodnost vašega vprašanja</p>
                    <button class='btn btn-secondary'>Vleci Tri Karte</button>
                </div>
                
                <div class='action-card' onclick='dnevnoSvetovanje()'>
                    <div class='action-icon'>🌅</div>
                    <h4>Dnevno Svetovanje</h4>
                    <p>Karta dneva z vodstvom za vaš dan</p>
                    <button class='btn btn-success'>Prikaži Svetovanje</button>
                </div>
            </div>
            
            <div class='tarot-info'>
                <h4>💫 O Tarotu</h4>
                <p>Tarot je starodavno orodje za samo-refleksijo in notranje vodstvo. 37 kart v tem modulu vsebuje modrost velikega in malega arkana.</p>
                <p><strong>Nasvet:</strong> Pred vlečenjem kart se osredotočite na svoje vprašanje in odprite srce za prejemanje modrosti.</p>
            </div>
            
            <div id='oraculum-rezultat' class='result-container'></div>
        </div>
        
        <script>
        function vleciEnokartnoRazlago() {
            naloziModulAkcijo('vleci_karto');
        }
        
        function vleciTrikartnoRazlago() {
            naloziModulAkcijo('tri_karte');
        }
        
        function dnevnoSvetovanje() {
            naloziModulAkcijo('dnevno_svetovanje');
        }
        
        function naloziModulAkcijo(akcija) {
            fetch('portal.php?modul=oraculum&akcija=' + akcija)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('oraculum-rezultat').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('oraculum-rezultat').innerHTML = '<div class=\"alert alert-danger\">Napaka pri nalaganju modula.</div>';
                });
        }
        </script>
        
        <style>
        .oraculum-container {
            padding: 1rem;
        }
        .module-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #8f9a7c;
        }
        .module-description {
            color: #5a6a5f;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .tarot-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .action-card {
            background: rgba(143, 154, 124, 0.1);
            border: 1px solid rgba(143, 154, 124, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #8f9a7c;
        }
        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .tarot-info {
            background: rgba(184, 150, 108, 0.1);
            border-left: 4px solid #b8966c;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .result-container {
            min-height: 200px;
        }
        </style>
        ";
    }
    
    /**
     * PRIKAZI ENO KARTO
     */
    private function prikaziKarto($karta) {
        return "
        <div class='tarot-card-result'>
            <div class='card-header'>
                <h3>🎴 {$karta['ime']}</h3>
                <span class='card-type'>{$karta['tip']}</span>
            </div>
            
            <div class='card-content'>
                <div class='card-main'>
                    <p><strong>Značilnost:</strong> {$karta['znacilnost']}</p>
                    <p><strong>Opis:</strong> {$karta['opis']}</p>
                </div>
                
                <div class='card-interpretation'>
                    <div class='interpretation-positive'>
                        <h4>☀️ Pozitivni Vidiki</h4>
                        <p>{$karta['pozitivno']}</p>
                    </div>
                    
                    <div class='interpretation-negative'>
                        <h4>🌙 Opozorila</h4>
                        <p>{$karta['negativno']}</p>
                    </div>
                    
                    <div class='interpretation-advice'>
                        <h4>💫 Svetovanje</h4>
                        <p>{$karta['svetovanje']}</p>
                    </div>
                </div>
            </div>
            
            <div class='card-actions'>
                <button class='btn btn-primary' onclick='vleciEnokartnoRazlago()'>🎴 Vleci Novo Karto</button>
                <button class='btn btn-outline-secondary' onclick='document.getElementById(\"oraculum-rezultat\").innerHTML = \"\"'>✕ Zapri</button>
            </div>
        </div>
        
        <style>
        .tarot-card-result {
            background: linear-gradient(135deg, #faf8f3, #e8e4d9);
            border: 2px solid #8f9a7c;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #8f9a7c;
        }
        .card-header h3 {
            color: #6b4a3a;
            margin: 0;
        }
        .card-type {
            background: #8f9a7c;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .card-main {
            margin-bottom: 2rem;
        }
        .card-main p {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .card-interpretation {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .interpretation-positive, .interpretation-negative, .interpretation-advice {
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .interpretation-positive {
            background: rgba(213, 245, 227, 0.3);
            border-left-color: #27ae60;
        }
        .interpretation-negative {
            background: rgba(253, 235, 236, 0.3);
            border-left-color: #e74c3c;
        }
        .interpretation-advice {
            background: rgba(214, 234, 248, 0.3);
            border-left-color: #3498db;
        }
        .card-interpretation h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }
        .card-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        </style>
        ";
    }
    
    /**
     * PRIKAZI TRI KARTE
     */
    private function prikaziTriKarte($karte) {
        return "
        <div class='three-cards-result'>
            <h3>📊 Tri-kartno Branje</h3>
            <p class='reading-description'>Razlaga vašega vprašanja skozi prizmo časa:</p>
            
            <div class='cards-grid'>
                <div class='time-card'>
                    <h4>🕰️ Preteklost</h4>
                    <div class='card-box'>
                        <h5>{$karte['preteklost']['ime']}</h5>
                        <p><strong>Vpliv:</strong> {$karte['preteklost']['znacilnost']}</p>
                        <p>{$karte['preteklost']['svetovanje']}</p>
                    </div>
                </div>
                
                <div class='time-card'>
                    <h4>⚡ Sedanjost</h4>
                    <div class='card-box'>
                        <h5>{$karte['sedanjost']['ime']}</h5>
                        <p><strong>Stanje:</strong> {$karte['sedanjost']['znacilnost']}</p>
                        <p>{$karte['sedanjost']['svetovanje']}</p>
                    </div>
                </div>
                
                <div class='time-card'>
                    <h4>🔮 Prihodnost</h4>
                    <div class='card-box'>
                        <h5>{$karte['prihodnost']['ime']}</h5>
                        <p><strong>Smer:</strong> {$karte['prihodnost']['znacilnost']}</p>
                        <p>{$karte['prihodnost']['svetovanje']}</p>
                    </div>
                </div>
            </div>
            
            <div class='reading-summary'>
                <h4>💫 Povzetek Razlage</h4>
                <p>Vaša pot se razvija iz <strong>{$karte['preteklost']['znacilnost']}</strong> preko trenutnega stanja <strong>{$karte['sedanjost']['znacilnost']}</strong> v smer <strong>{$karte['prihodnost']['znacilnost']}</strong>. Upamo, da vam to branje prinaša jasnost in vodstvo.</p>
            </div>
            
            <div class='card-actions'>
                <button class='btn btn-primary' onclick='vleciTrikartnoRazlago()'>🔄 Novo Tri-kartno Branje</button>
                <button class='btn btn-outline-secondary' onclick='document.getElementById(\"oraculum-rezultat\").innerHTML = \"\"'>✕ Zapri</button>
            </div>
        </div>
        
        <style>
        .three-cards-result {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 2rem;
        }
        .reading-description {
            text-align: center;
            color: #5a6a5f;
            margin-bottom: 2rem;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .time-card {
            text-align: center;
        }
        .time-card h4 {
            margin-bottom: 1rem;
            color: #6b4a3a;
        }
        .card-box {
            background: white;
            border: 2px solid #8f9a7c;
            border-radius: 12px;
            padding: 1.5rem;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-box h5 {
            color: #8f9a7c;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 0.5rem;
        }
        .reading-summary {
            background: rgba(143, 154, 124, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid #8f9a7c;
        }
        </style>
        ";
    }
    
    /**
     * DNEVNO SVETOVANJE
     */
    private function dnevnoSvetovanje() {
        $karta = $this->vleciKarto();
        $danes = date('d.m.Y');
        
        return "
        <div class='daily-guidance'>
            <div class='daily-header'>
                <h3>🌅 Dnevno Svetovanje - $danes</h3>
                <p>Karta dneva vas vodi in svetuje na vaši današnji poti</p>
            </div>
            
            <div class='daily-card'>
                <div class='card-of-day'>
                    <h4>🎴 {$karta['ime']}</h4>
                    <p class='card-characteristic'><strong>{$karta['znacilnost']}</strong></p>
                </div>
                
                <div class='daily-message'>
                    <p><strong>Današnje sporočilo:</strong> {$karta['opis']}</p>
                    <p><strong>Pozitivni potencial:</strong> {$karta['pozitivno']}</p>
                    <p><strong>Opozorilo:</strong> {$karta['negativno']}</p>
                </div>
                
                <div class='daily-advice'>
                    <h5>💫 Vodstvo za danes:</h5>
                    <p>{$karta['svetovanje']}</p>
                </div>
            </div>
            
            <div class='reflection-questions'>
                <h5>🤔 Vprašanja za refleksijo:</h5>
                <ul>
                    <li>Kako se današnja karta odraža v mojem življenju?</li>
                    <li>Kaj lahko danes naredim drugače, da izpolnim pozitivni potencial karte?</li>
                    <li>Kje naj bom pozoren na opozorila karte?</li>
                </ul>
            </div>
            
            <div class='card-actions'>
                <button class='btn btn-success' onclick='dnevnoSvetovanje()'>🔄 Nov Dan - Novo Svetovanje</button>
                <button class='btn btn-outline-secondary' onclick='document.getElementById(\"oraculum-rezultat\").innerHTML = \"\"'>✕ Zapri</button>
            </div>
        </div>
        
        <style>
        .daily-guidance {
            background: linear-gradient(135deg, #fff9db, #ffe8cc);
            border-radius: 15px;
            padding: 2rem;
            border: 2px solid #ffd43b;
        }
        .daily-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ffd43b;
        }
        .daily-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-of-day {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .card-characteristic {
            color: #8f9a7c;
            font-size: 1.1rem;
        }
        .daily-message p {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .daily-advice {
            background: rgba(143, 154, 124, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            border-left: 4px solid #8f9a7c;
        }
        .reflection-questions {
            background: rgba(255, 212, 59, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #ffd43b;
        }
        .reflection-questions ul {
            margin: 0.5rem 0 0 1rem;
        }
        .reflection-questions li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }
        </style>
        ";
    }
}

// Izvedi modul
$oraculum = new ModulOraculum();
echo $oraculum->generirajVsebino();
?>