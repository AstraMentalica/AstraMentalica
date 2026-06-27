<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/uporabnik_nastavitve_napredno.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Napredne nastavitve z več možnostmi
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Preveri prijavo
if (!seja_je_prijavljen()) {
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}

$uporabnikId = seja_pridobi('uporabnik_id');
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

// Naloži uporabnikove nastavitve
$nastavitve = [];
$nastavitveDatoteka = UPORABNIKI . '/' . $uporabnikId . '/nastavitve.json';
if (file_exists($nastavitveDatoteka)) {
    $nastavitve = json_decode(file_get_contents($nastavitveDatoteka), true) ?? [];
}

// Privzete nastavitve
$privzeteNastavitve = [
    'tema' => 'gradient',
    'glasnost_frekvenc' => 50,
    'avtomatski_predvajalnik' => false,
    'obvestila' => true,
    'zasebnost' => 'javen',
    'jezik' => 'sl',
    'casovna_zona' => 'Europe/Ljubljana',
    'dnevna_sporocila' => true,
    'tedenska_statistika' => true
];

// Združi z privzetimi
$nastavitve = array_merge($privzeteNastavitve, $nastavitve);

// Obdelava sprememb
$napaka = '';
$uspeh = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';
    
    if ($akcija === 'shrani_nastavitve') {
        $noveNastavitve = [
            'tema' => $_POST['tema'] ?? 'gradient',
            'glasnost_frekvenc' => (int)($_POST['glasnost_frekvenc'] ?? 50),
            'avtomatski_predvajalnik' => isset($_POST['avtomatski_predvajalnik']),
            'obvestila' => isset($_POST['obvestila']),
            'zasebnost' => $_POST['zasebnost'] ?? 'javen',
            'jezik' => $_POST['jezik'] ?? 'sl',
            'casovna_zona' => $_POST['casovna_zona'] ?? 'Europe/Ljubljana',
            'dnevna_sporocila' => isset($_POST['dnevna_sporocila']),
            'tedenska_statistika' => isset($_POST['tedenska_statistika'])
        ];
        
        // Shrani priljubljene frekvence če so bile spremenjene
        if (isset($_POST['priljubljene_frekvence'])) {
            $noveNastavitve['priljubljene_frekvence'] = $_POST['priljubljene_frekvence'];
        }
        
        file_put_contents($nastavitveDatoteka, json_encode($noveNastavitve, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $uspeh = 'Nastavitve uspešno shranjene!';
    }
    
    if ($akcija === 'dodaj_priljubljeno') {
        $frekvencaId = $_POST['frekvenca_id'] ?? '';
        $frekvencaIme = $_POST['frekvenca_ime'] ?? '';
        
        if (!isset($nastavitve['priljubljene_frekvence'])) {
            $nastavitve['priljubljene_frekvence'] = [];
        }
        
        // Preveri ali že obstaja
        $zeObstaja = false;
        foreach ($nastavitve['priljubljene_frekvence'] as $fav) {
            if ($fav['id'] === $frekvencaId) {
                $zeObstaja = true;
                break;
            }
        }
        
        if (!$zeObstaja && !empty($frekvencaId)) {
            $nastavitve['priljubljene_frekvence'][] = [
                'id' => $frekvencaId,
                'ime' => $frekvencaIme
            ];
            
            file_put_contents($nastavitveDatoteka, json_encode($nastavitve, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $uspeh = 'Frekvenca dodana med priljubljene!';
        }
    }
    
    if ($akcija === 'odstrani_priljubljeno') {
        $frekvencaId = $_POST['frekvenca_id'] ?? '';
        
        if (isset($nastavitve['priljubljene_frekvence'])) {
            $nastavitve['priljubljene_frekvence'] = array_filter(
                $nastavitve['priljubljene_frekvence'],
                fn($fav) => $fav['id'] !== $frekvencaId
            );
            
            file_put_contents($nastavitveDatoteka, json_encode($nastavitve, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $uspeh = 'Frekvenca odstranjena.';
        }
    }
}

// Seznam frekvenc za izbiro
$frekvence = [
    ['id' => '432', 'ime' => '432 Hz - Naravna'],
    ['id' => '528', 'ime' => '528 Hz - Zdravljenje'],
    ['id' => '639', 'ime' => '639 Hz - Ljubezen'],
    ['id' => '741', 'ime' => '741 Hz - Zaznava'],
    ['id' => '852', 'ime' => '852 Hz - Duhovnost'],
    ['id' => '963', 'ime' => '963 Hz - Vizija']
];

$priljubljene = $nastavitve['priljubljene_frekvence'] ?? [];
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Napredne nastavitve | AstraMentalica</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(0deg) rotate(0deg); }
            100% { transform: translateY(-50px) rotate(5deg); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2.5rem;
            margin-bottom: 2rem;
            color: #fff;
        }

        .glass-header h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .glass-header p {
            color: rgba(255, 255, 255, 0.8);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2rem;
            color: #fff;
        }

        .glass-card h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-group select,
        .form-group input[type="range"] {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group select {
            cursor: pointer;
        }

        .form-group select option {
            background: #1a1a2e;
            color: #fff;
        }

        .form-group input[type="range"] {
            padding: 0;
            height: 6px;
            -webkit-appearance: none;
            appearance: none;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 3px;
        }

        .form-group input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #667eea;
            cursor: pointer;
        }

        .form-group input[type="range"]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #667eea;
            cursor: pointer;
            border: none;
        }

        .range-value {
            text-align: center;
            color: #e8c84a;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            margin-bottom: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkbox-group:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .checkbox-group label {
            flex: 1;
            cursor: pointer;
            margin: 0;
        }

        .favorites-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .favorite-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .favorite-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .favorite-icon {
            font-size: 1.5rem;
        }

        .favorite-name {
            font-weight: 500;
        }

        .remove-button {
            background: rgba(244, 67, 54, 0.3);
            border: 1px solid rgba(244, 67, 54, 0.5);
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .remove-button:hover {
            background: rgba(244, 67, 54, 0.5);
        }

        .add-favorite {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .add-favorite select {
            flex: 1;
            padding: 0.7rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
        }

        .add-favorite select option {
            background: #1a1a2e;
            color: #fff;
        }

        .glass-button {
            padding: 0.9rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        .glass-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .glass-error {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid rgba(244, 67, 54, 0.4);
            border-radius: 12px;
            padding: 0.9rem;
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .glass-success {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.4);
            border-radius: 12px;
            padding: 0.9rem;
            color: #81c784;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.6);
            font-style: italic;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-header">
            <h1>⚙️ Napredne nastavitve</h1>
            <p>Prilagodite si izkušnjo po meri</p>
        </div>

        <?php if ($napaka): ?>
            <div class="glass-error"><?= htmlspecialchars($napaka) ?></div>
        <?php endif; ?>

        <?php if ($uspeh): ?>
            <div class="glass-success"><?= htmlspecialchars($uspeh) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="akcija" value="shrani_nastavitve">
            
            <div class="grid">
                <!-- Zvok in frekvence -->
                <div class="glass-card">
                    <h2>🎵 Zvok in frekvence</h2>
                    
                    <div class="form-group">
                        <label for="glasnost_frekvenc">Glasnost frekvenc (%)</label>
                        <input 
                            type="range" 
                            id="glasnost_frekvenc" 
                            name="glasnost_frekvenc" 
                            min="0" 
                            max="100" 
                            value="<?= $nastavitve['glasnost_frekvenc'] ?>"
                        >
                        <div class="range-value"><?= $nastavitve['glasnost_frekvenc'] ?>%</div>
                    </div>

                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            id="avtomatski_predvajalnik" 
                            name="avtomatski_predvajalnik"
                            <?= $nastavitve['avtomatski_predvajalnik'] ? 'checked' : '' ?>
                        >
                        <label for="avtomatski_predvajalnik">Avtomatsko predvajanje ob odprtju</label>
                    </div>

                    <div class="form-group">
                        <label>⭐ Priljubljene frekvence</label>
                        <div class="favorites-list">
                            <?php if (!empty($priljubljene)): ?>
                                <?php foreach ($priljubljene as $fav): ?>
                                    <div class="favorite-item">
                                        <div class="favorite-info">
                                            <span class="favorite-icon">🎵</span>
                                            <span class="favorite-name"><?= htmlspecialchars($fav['ime']) ?></span>
                                        </div>
                                        <button type="submit" name="akcija" value="odstrani_priljubljeno" 
                                                class="remove-button"
                                                onclick="document.getElementById('remove_<?= $fav['id'] ?>').value='<?= $fav['id'] ?>'">
                                            ✕
                                        </button>
                                        <input type="hidden" id="remove_<?= $fav['id'] ?>" name="frekvenca_id" value="">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">Še ni priljubljenih frekvenc</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="add-favorite">
                            <select name="frekvenca_id" id="dodajFrekvenco">
                                <option value="">-- Izberi frekvenco --</option>
                                <?php foreach ($frekvence as $frek): ?>
                                    <option value="<?= $frek['id'] ?>"><?= $frek['ime'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="akcija" value="dodaj_priljubljeno" class="glass-button" style="width: auto; padding: 0.7rem 1.2rem;">
                                + Dodaj
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Obvestila -->
                <div class="glass-card">
                    <h2>🔔 Obvestila</h2>
                    
                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            id="obvestila" 
                            name="obvestila"
                            <?= $nastavitve['obvestila'] ? 'checked' : '' ?>
                        >
                        <label for="obvestila">Omogoči obvestila</label>
                    </div>

                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            id="dnevna_sporocila" 
                            name="dnevna_sporocila"
                            <?= $nastavitve['dnevna_sporocila'] ? 'checked' : '' ?>
                        >
                        <label for="dnevna_sporocila">Dnevna sporočila od varuha</label>
                    </div>

                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            id="tedenska_statistika" 
                            name="tedenska_statistika"
                            <?= $nastavitve['tedenska_statistika'] ? 'checked' : '' ?>
                        >
                        <label for="tedenska_statistika">Tedenska statistika uporabe</label>
                    </div>
                </div>

                <!-- Zasebnost -->
                <div class="glass-card">
                    <h2>🔒 Zasebnost</h2>
                    
                    <div class="form-group">
                        <label for="zasebnost">Vidnost profila</label>
                        <select id="zasebnost" name="zasebnost">
                            <option value="javen" <?= $nastavitve['zasebnost'] === 'javen' ? 'selected' : '' ?>>Javen</option>
                            <option value="zaseben" <?= $nastavitve['zasebnost'] === 'zaseben' ? 'selected' : '' ?>>Zaseben</option>
                            <option value="prijatelji" <?= $nastavitve['zasebnost'] === 'prijatelji' ? 'selected' : '' ?>>Samo prijatelji</option>
                        </select>
                    </div>
                </div>

                <!-- Jezik in regija -->
                <div class="glass-card">
                    <h2>🌍 Jezik in regija</h2>
                    
                    <div class="form-group">
                        <label for="jezik">Jezik</label>
                        <select id="jezik" name="jezik">
                            <option value="sl" <?= $nastavitve['jezik'] === 'sl' ? 'selected' : '' ?>>Slovenščina</option>
                            <option value="en" <?= $nastavitve['jezik'] === 'en' ? 'selected' : '' ?>>English</option>
                            <option value="de" <?= $nastavitve['jezik'] === 'de' ? 'selected' : '' ?>>Deutsch</option>
                            <option value="hr" <?= $nastavitve['jezik'] === 'hr' ? 'selected' : '' ?>>Hrvatski</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="casovna_zona">Časovna zona</label>
                        <select id="casovna_zona" name="casovna_zona">
                            <option value="Europe/Ljubljana" <?= $nastavitve['casovna_zona'] === 'Europe/Ljubljana' ? 'selected' : '' ?>>Ljubljana (CET)</option>
                            <option value="Europe/Zagreb" <?= $nastavitve['casovna_zona'] === 'Europe/Zagreb' ? 'selected' : '' ?>>Zagreb (CET)</option>
                            <option value="Europe/Belgrade" <?= $nastavitve['casovna_zona'] === 'Europe/Belgrade' ? 'selected' : '' ?>>Beograd (CET)</option>
                            <option value="Europe/Vienna" <?= $nastavitve['casovna_zona'] === 'Europe/Vienna' ? 'selected' : '' ?>>Dunaj (CET)</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="glass-button">
                💾 Shrani nastavitve
            </button>
        </form>

        <a href="?svet=UPORABNIKI&pot=nastavitve" class="back-link">← Nazaj na osnovne nastavitve</a>
    </div>

    <script>
        // Posodobi prikaz vrednosti za range slider
        const rangeInput = document.getElementById('glasnost_frekvenc');
        const rangeValue = document.querySelector('.range-value');
        
        if (rangeInput && rangeValue) {
            rangeInput.addEventListener('input', function() {
                rangeValue.textContent = this.value + '%';
            });
        }

        // Potrdi odstranjevanje priljubljenih
        document.querySelectorAll('.remove-button').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Ali ste prepričani, da želite odstraniti?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>