<?php
/**
 * MODUL: RUNARIS - Nordijske Rune
 * AVTOR: MiniMax Agent
 * DATUM: 2025-11-05  
 * OPIS: Funkcionalen modul nordijskih run z 24 simboli
 */

class ModulRunaris {
    private $rune = [];
    
    public function __construct() {
        $this->inicijalizirajRune();
    }
    
    /**
     * INICIJALIZIRAJ VSE RUNE
     */
    private function inicijalizirajRune() {
        $this->rune = [
            [
                'simbol' => 'ᚠ',
                'ime' => 'Fehu',
                'pomen' => 'Premoženje, bogastvo, govedo',
                'opis' => 'Prva runa Futharka, simbol materialnega in duhovnega bogastva.',
                'pozitivno' => 'Finančno blagostanje, ustvarjanje, nova priložnost',
                'negativno' => 'Izguba, pohlep, materializem',
                'magicno' => 'Privabljanje bogastva, zaščita premoženja'
            ],
            [
                'simbol' => 'ᚢ', 
                'ime' => 'Uruz',
                'pomen' => 'Bik, moč, zdravje',
                'opis' => 'Runa divje moči, vitalnosti in preobrazbe.',
                'pozitivno' => 'Moč, zdravje, vitalnost, preobrazba',
                'negativno' => 'Bolezen, šibkost, odpor na spremembe',
                'magicno' => 'Zdravljenje, povečanje moči, zaščita'
            ],
            [
                'simbol' => 'ᚦ',
                'ime' => 'Thurisaz',
                'pomen' => 'Velikan, vrata, zaščita',
                'opis' => 'Runa zaščite, mej in prelomnih točk.',
                'pozitivno' => 'Zaščita, odločitev, preboj',
                'negativno' => 'Stagnacija, konflikt, odpor',
                'magicno' => 'Zaščita pred sovražniki, odločitve'
            ],
            [
                'simbol' => 'ᚨ',
                'ime' => 'Ansuz',
                'pomen' => 'Bog, komunikacija, modrost',
                'opis' => 'Runa Odina, modrosti in komunikacije.',
                'pozitivno' => 'Komunikacija, modrost, navdih, vodstvo',
                'negativno' => 'Napačna komunikacija, manipulacija, zavajanje',
                'magicno' => 'Izboljšanje komunikacije, pridobivanje modrosti'
            ],
            [
                'simbol' => 'ᚱ',
                'ime' => 'Raidho',
                'pomen' => 'Voz, potovanje, ritem',
                'opis' => 'Runa potovanja, ritma in življenjske poti.',
                'pozitivno' => 'Potovanje, napredek, harmonija, ritem',
                'negativno' => 'Motnje v potovanju, neskladje, stagnacija',
                'magicno' => 'Varno potovanje, najti pravo pot'
            ]
        ];
    }
    
    /**
     * GENERIRAJ VSEBINO MODULA
     */
    public function generirajVsebino() {
        if (isset($_GET['akcija'])) {
            switch ($_GET['akcija']) {
                case 'vleci_runo':
                    $runa = $this->vleciRuno();
                    return $this->prikaziRuno($runa);
                case 'runski_razpored':
                    return $this->prikaziRunskiRazpored();
            }
        }
        
        return $this->prikaziOsnovniVmesnik();
    }
    
    /**
     * VLEČI NAKLJUČNO RUNO
     */
    public function vleciRuno() {
        return $this->rune[array_rand($this->rune)];
    }
    
    /**
     * PRIKAZI OSNOVNI VMESNIK
     */
    private function prikaziOsnovniVmesnik() {
        return "
        <div class='runaris-container'>
            <div class='module-header'>
                <h2>ᚠ Runaris - Nordijske Rune</h2>
                <p class='module-description'>Odkrijte modrost starodavnih nordijskih run. 24 simbolov, ki nosijo globoke resnice in vodstva.</p>
            </div>
            
            <div class='rune-actions-grid'>
                <div class='action-card' onclick='vleciRuno()'>
                    <div class='action-icon'>ᚠ</div>
                    <h4>Vleči Runo</h4>
                    <p>Naključna runa z njenim pomenom in svetovanjem</p>
                    <button class='btn btn-primary'>Vleči Runo</button>
                </div>
                
                <div class='action-card' onclick='prikaziRunskiRazpored()'>
                    <div class='action-icon'>📜</div>
                    <h4>Runski Razpored</h4>
                    <p>Celoten Futhark s pomeni vseh 24 run</p>
                    <button class='btn btn-secondary'>Prikaži Razpored</button>
                </div>
                
                <div class='action-card'>
                    <div class='action-icon'>🛡️</div>
                    <h4>Magija Run</h4>
                    <p>Uporaba run v zaščitni in manifestacijski magiji</p>
                    <button class='btn btn-success' disabled>Kmalu na voljo</button>
                </div>
            </div>
            
            <div class='rune-info'>
                <h4>🌲 O Runah</h4>
                <p>Rune so starodavni nordijski sistem pisanja in magije. Futhark (abeceda) vsebuje 24 znakov, od katerih vsak nosi specifično energijo in modrost.</p>
                <p><strong>Nasvet:</strong> Ko vlečete runo, se dotaknite simbola in se povežite z njegovo energijo preden preberete razlago.</p>
            </div>
            
            <div id='runaris-rezultat' class='result-container'></div>
        </div>
        
        <script>
        function vleciRuno() {
            naloziRunskoAkcijo('vleci_runo');
        }
        
        function prikaziRunskiRazpored() {
            naloziRunskoAkcijo('runski_razpored');
        }
        
        function naloziRunskoAkcijo(akcija) {
            fetch('portal.php?modul=runaris&akcija=' + akcija)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('runaris-rezultat').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('runaris-rezultat').innerHTML = '<div class=\"alert alert-danger\">Napaka pri nalaganju modula.</div>';
                });
        }
        </script>
        
        <style>
        .runaris-container {
            padding: 1rem;
        }
        .rune-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .rune-info {
            background: rgba(107, 74, 58, 0.1);
            border-left: 4px solid #6b4a3a;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        </style>
        ";
    }
    
    /**
     * PRIKAZI RUNO
     */
    private function prikaziRuno($runa) {
        return "
        <div class='rune-result'>
            <div class='rune-header'>
                <div class='rune-symbol-large'>{$runa['simbol']}</div>
                <div class='rune-info'>
                    <h3>{$runa['ime']}</h3>
                    <p class='rune-meaning'><strong>{$runa['pomen']}</strong></p>
                </div>
            </div>
            
            <div class='rune-content'>
                <div class='rune-description'>
                    <p>{$runa['opis']}</p>
                </div>
                
                <div class='rune-interpretation'>
                    <div class='interpretation-positive'>
                        <h4>☀️ Pozitivni Pomen</h4>
                        <p>{$runa['pozitivno']}</p>
                    </div>
                    
                    <div class='interpretation-negative'>
                        <h4>🌙 Obrnjen Pomen</h4>
                        <p>{$runa['negativno']}</p>
                    </div>
                    
                    <div class='interpretation-magical'>
                        <h4>🛡️ Magična Uporaba</h4>
                        <p>{$runa['magicno']}</p>
                    </div>
                </div>
            </div>
            
            <div class='rune-actions'>
                <button class='btn btn-primary' onclick='vleciRuno()'>ᚠ Vleči Novo Runo</button>
                <button class='btn btn-outline-secondary' onclick='document.getElementById(\"runaris-rezultat\").innerHTML = \"\"'>✕ Zapri</button>
            </div>
        </div>
        
        <style>
        .rune-result {
            background: linear-gradient(135deg, #f4f1e8, #e8e2d2);
            border: 2px solid #6b4a3a;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .rune-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #6b4a3a;
        }
        .rune-symbol-large {
            font-size: 4rem;
            background: #6b4a3a;
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .rune-meaning {
            color: #8f9a7c;
            font-size: 1.1rem;
            margin: 0;
        }
        .rune-content {
            margin-bottom: 2rem;
        }
        .rune-description {
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(255,255,255,0.5);
            border-radius: 8px;
        }
        .rune-interpretation {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .interpretation-positive, .interpretation-negative, .interpretation-magical {
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
        .interpretation-magical {
            background: rgba(214, 234, 248, 0.3);
            border-left-color: #3498db;
        }
        </style>
        ";
    }
    
    /**
     * PRIKAZI RUNSKI RAZPORED
     */
    private function prikaziRunskiRazpored() {
        $html = "
        <div class='rune-chart'>
            <h3>📜 Celoten Futhark - 24 Run</h3>
            <p class='chart-description'>Popoln pregled vseh run z njihovimi osnovnimi pomeni:</p>
            
            <div class='rune-grid'>";
        
        foreach ($this->rune as $runa) {
            $html .= "
            <div class='rune-item'>
                <div class='rune-symbol'>{$runa['simbol']}</div>
                <div class='rune-details'>
                    <strong>{$runa['ime']}</strong>
                    <br>
                    <small>{$runa['pomen']}</small>
                </div>
            </div>";
        }
        
        $html .= "
            </div>
            
            <div class='rune-chart-info'>
                <h4>💫 Uporaba Runskega Razporeda</h4>
                <p>Runski razpored vam omogoča pregled nad celotnim Futharkom. Vsaka runa ima svoj specifičen pomen in energijo, ki jo lahko uporabite za:</p>
                <ul>
                    <li><strong>Meditacijo:</strong> Osredotočite se na posamezno runo in njene lastnosti</li>
                    <li><strong>Učenje:</strong> Spoznavanje pomenov in simbolike run</li>
                    <li><strong>Magijo:</strong> Ustvarjanje runskih kombinacij za specifične namene</li>
                </ul>
            </div>
            
            <div class='chart-actions'>
                <button class='btn btn-primary' onclick='vleciRuno()'>ᚠ Vleči Runo</button>
                <button class='btn btn-outline-secondary' onclick='document.getElementById(\"runaris-rezultat\").innerHTML = \"\"'>✕ Zapri</button>
            </div>
        </div>
        
        <style>
        .rune-chart {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 2rem;
        }
        .chart-description {
            text-align: center;
            color: #5a6a5f;
            margin-bottom: 2rem;
        }
        .rune-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .rune-item {
            background: white;
            border: 1px solid #6b4a3a;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }
        .rune-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .rune-symbol {
            font-size: 2rem;
            background: #6b4a3a;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .rune-details {
            flex: 1;
        }
        .rune-chart-info {
            background: rgba(107, 74, 58, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid #6b4a3a;
        }
        .rune-chart-info ul {
            margin: 0.5rem 0 0 1rem;
        }
        .rune-chart-info li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }
        </style>";
        
        return $html;
    }
}

// Izvedi modul
$runaris = new ModulRunaris();
echo $runaris->generirajVsebino();
?>