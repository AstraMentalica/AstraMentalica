<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/uporabnik_nastavitve.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Nastavitve uporabnika z predvajanjem frekvenc
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

// Obdelava sprememb profila
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcija'])) {
    if ($_POST['akcija'] === 'posodobi_profil') {
        $ime = trim($_POST['ime'] ?? '');
        if (!empty($ime)) {
            baza_posodobi('uporabniki', $uporabnikId, ['ime' => $ime]);
            $_SESSION['uporabnik_ime'] = $ime;
            $uspeh = 'Profil uspešno posodobljen.';
        }
    }
    
    if ($_POST['akcija'] === 'spremeni_geslo') {
        $staroGeslo = $_POST['staro_geslo'] ?? '';
        $novoGeslo = $_POST['novo_geslo'] ?? '';
        $potrdiGeslo = $_POST['potrdi_geslo'] ?? '';
        
        if (password_verify($staroGeslo, $uporabnik['hash_gesla'])) {
            if (strlen($novoGeslo) >= 8) {
                if ($novoGeslo === $potrdiGeslo) {
                    baza_posodobi('uporabniki', $uporabnikId, [
                        'hash_gesla' => password_hash($novoGeslo, PASSWORD_BCRYPT)
                    ]);
                    $uspeh = 'Geslo uspešno spremenjeno.';
                } else {
                    $napaka = 'Novi gesli se ne ujemata.';
                }
            } else {
                $napaka = 'Novo geslo mora imeti vsaj 8 znakov.';
            }
        } else {
            $napaka = 'Staro geslo ni pravilno.';
        }
    }
}

// Naloži uporabnikove nastavitve
$nastavitve = [];
$nastavitveDatoteka = UPORABNIKI . '/' . $uporabnikId . '/nastavitve.json';
if (file_exists($nastavitveDatoteka)) {
    $nastavitve = json_decode(file_get_contents($nastavitveDatoteka), true) ?? [];
}

$napaka = $napaka ?? '';
$uspeh = $uspeh ?? '';
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nastavitve | AstraMentalica</title>
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
            padding: 2rem;
            margin-bottom: 2rem;
            color: #fff;
        }

        .glass-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .glass-header p {
            color: rgba(255, 255, 255, 0.8);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
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

        .glass-form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .glass-input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .glass-input-group label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .glass-input-group input {
            padding: 0.9rem 1rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .glass-input-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .glass-input-group input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
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
        }

        .glass-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .glass-button:active {
            transform: translateY(0);
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

        /* Audio Player Styles */
        .audio-player {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .frequency-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .frequency-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .frequency-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .frequency-card.active {
            background: rgba(102, 126, 234, 0.4);
            border-color: rgba(102, 126, 234, 0.6);
        }

        .frequency-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .frequency-name {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .frequency-hz {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .player-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .control-button {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .control-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .control-button.play {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .volume-slider {
            width: 100%;
            margin-top: 1rem;
        }

        .volume-slider input[type="range"] {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.2);
            outline: none;
            -webkit-appearance: none;
        }

        .volume-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #667eea;
            cursor: pointer;
        }

        .volume-slider input[type="range"]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #667eea;
            cursor: pointer;
            border: none;
        }

        .now-playing {
            text-align: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-top: 1rem;
        }

        .now-playing-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.3rem;
        }

        .now-playing-name {
            font-size: 1.1rem;
            font-weight: 600;
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
            <h1>⚙️ Nastavitve</h1>
            <p>Upravljaj svoj račun in preferencami</p>
        </div>

        <?php if ($napaka): ?>
            <div class="glass-error"><?= htmlspecialchars($napaka) ?></div>
        <?php endif; ?>

        <?php if ($uspeh): ?>
            <div class="glass-success"><?= htmlspecialchars($uspeh) ?></div>
        <?php endif; ?>

        <div class="grid">
            <!-- Profil -->
            <div class="glass-card">
                <h2>👤 Profil</h2>
                <form method="post" class="glass-form">
                    <input type="hidden" name="akcija" value="posodobi_profil">
                    
                    <div class="glass-input-group">
                        <label for="ime">Ime</label>
                        <input 
                            type="text" 
                            id="ime" 
                            name="ime" 
                            value="<?= htmlspecialchars($uporabnik['ime'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="glass-input-group">
                        <label>E-pošta</label>
                        <input 
                            type="email" 
                            value="<?= htmlspecialchars($uporabnik['elektronski_naslov'] ?? '') ?>"
                            disabled
                            style="opacity: 0.6;"
                        >
                    </div>

                    <button type="submit" class="glass-button">
                        Posodobi profil
                    </button>
                </form>
            </div>

            <!-- Geslo -->
            <div class="glass-card">
                <h2>🔒 Geslo</h2>
                <form method="post" class="glass-form">
                    <input type="hidden" name="akcija" value="spremeni_geslo">
                    
                    <div class="glass-input-group">
                        <label for="staro_geslo">Staro geslo</label>
                        <input 
                            type="password" 
                            id="staro_geslo" 
                            name="staro_geslo" 
                            required
                        >
                    </div>

                    <div class="glass-input-group">
                        <label for="novo_geslo">Novo geslo</label>
                        <input 
                            type="password" 
                            id="novo_geslo" 
                            name="novo_geslo" 
                            required
                            minlength="8"
                        >
                    </div>

                    <div class="glass-input-group">
                        <label for="potrdi_geslo">Potrdi novo geslo</label>
                        <input 
                            type="password" 
                            id="potrdi_geslo" 
                            name="potrdi_geslo" 
                            required
                            minlength="8"
                        >
                    </div>

                    <button type="submit" class="glass-button">
                        Spremeni geslo
                    </button>
                </form>
            </div>

            <!-- Frekvence in Ambientna glasba -->
            <div class="glass-card" style="grid-column: 1 / -1;">
                <h2>🎵 Frekvence in Ambientna glasba</h2>
                
                <div class="audio-player">
                    <div class="now-playing">
                        <div class="now-playing-label">Trenutno predvajam</div>
                        <div class="now-playing-name" id="currentTrack">Izberi frekvenco</div>
                    </div>

                    <div class="player-controls">
                        <button class="control-button" id="prevBtn">⏮️</button>
                        <button class="control-button play" id="playBtn">▶️</button>
                        <button class="control-button" id="nextBtn">⏭️</button>
                    </div>

                    <div class="volume-slider">
                        <label style="color: rgba(255,255,255,0.9); font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">
                    🔊 Glasnost
                        </label>
                        <input 
                            type="range" 
                            id="volumeSlider" 
                            min="0" 
                            max="100" 
                            value="50"
                        >
                    </div>
                </div>

                <h3 style="margin-top: 2rem; margin-bottom: 1rem;">🧘 Meditacijske frekvence</h3>
                <div class="frequency-grid">
                    <div class="frequency-card" data-frequency="432" data-name="432 Hz - Naravna frekvenca">
                        <div class="frequency-icon">🌍</div>
                        <div class="frequency-name">432 Hz</div>
                        <div class="frequency-hz">Naravna frekvenca</div>
                    </div>

                    <div class="frequency-card" data-frequency="528" data-name="528 Hz - Zdravljenje">
                        <div class="frequency-icon">💚</div>
                        <div class="frequency-name">528 Hz</div>
                        <div class="frequency-hz">Zdravljenje</div>
                    </div>

                    <div class="frequency-card" data-frequency="639" data-name="639 Hz - Ljubezen">
                        <div class="frequency-icon">❤️</div>
                        <div class="frequency-name">639 Hz</div>
                        <div class="frequency-hz">Ljubezen</div>
                    </div>

                    <div class="frequency-card" data-frequency="741" data-name="741 Hz - Zaznava">
                        <div class="frequency-icon">🔮</div>
                        <div class="frequency-name">741 Hz</div>
                        <div class="frequency-hz">Zaznava</div>
                    </div>

                    <div class="frequency-card" data-frequency="852" data-name="852 Hz - Duhovnost">
                        <div class="frequency-icon">✨</div>
                        <div class="frequency-name">852 Hz</div>
                        <div class="frequency-hz">Duhovnost</div>
                    </div>

                    <div class="frequency-card" data-frequency="963" data-name="963 Hz - Vizija">
                        <div class="frequency-icon">👁️</div>
                        <div class="frequency-name">963 Hz</div>
                        <div class="frequency-hz">Vizija</div>
                    </div>
                </div>

                <h3 style="margin-top: 2rem; margin-bottom: 1rem;">🌊 Ambientna glasba</h3>
                <div class="frequency-grid">
                    <div class="frequency-card" data-ambient="rain" data-name="Dež">
                        <div class="frequency-icon">🌧️</div>
                        <div class="frequency-name">Dež</div>
                        <div class="frequency-hz">Naravni zvoki</div>
                    </div>

                    <div class="frequency-card" data-ambient="ocean" data-name="Ocean">
                        <div class="frequency-icon">🌊</div>
                        <div class="frequency-name">Ocean</div>
                        <div class="frequency-hz">Valovi</div>
                    </div>

                    <div class="frequency-card" data-ambient="forest" data-name="Gozd">
                        <div class="frequency-icon">🌲</div>
                        <div class="frequency-name">Gozd</div>
                        <div class="frequency-hz">Ptice in veter</div>
                    </div>

                    <div class="frequency-card" data-ambient="fire" data-name="Ogenj">
                        <div class="frequency-icon">🔥</div>
                        <div class="frequency-name">Ogenj</div>
                        <div class="frequency-hz">Krepčujoče</div>
                    </div>

                    <div class="frequency-card" data-ambient="wind" data-name="Veter">
                        <div class="frequency-icon">💨</div>
                        <div class="frequency-name">Veter</div>
                        <div class="frequency-hz">Tišina</div>
                    </div>

                    <div class="frequency-card" data-ambient="singing-bowl" data-name="Zvočna skleda">
                        <div class="frequency-icon">🎵</div>
                        <div class="frequency-name">Zvočna skleda</div>
                        <div class="frequency-hz">Meditacija</div>
                    </div>
                </div>
            </div>
        </div>

        <a href="?svet=UPORABNIKI&pot=profil" class="back-link">← Nazaj na profil</a>
    </div>

    <script>
        // Audio Context za generiranje frekvenc
        let audioContext = null;
        let currentOscillator = null;
        let currentGain = null;
        let isPlaying = false;
        let currentFrequency = null;
        let currentAmbient = null;

        // Inicializiraj Audio Context ob interakciji
        function initAudioContext() {
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioContext.state === 'suspended') {
                audioContext.resume();
            }
        }

        // Ustvari oscillator za frekvenco
        function playFrequency(frequency) {
            initAudioContext();
            stopAudio();

            currentOscillator = audioContext.createOscillator();
            currentGain = audioContext.createGain();

            currentOscillator.type = 'sine';
            currentOscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);

            const volume = document.getElementById('volumeSlider').value / 100;
            currentGain.gain.setValueAtTime(volume * 0.3, audioContext.currentTime);

            currentOscillator.connect(currentGain);
            currentGain.connect(audioContext.destination);

            currentOscillator.start();
            currentFrequency = frequency;
            isPlaying = true;
            updatePlayButton();
        }

        // Ustvari ambientni zvok (preprost šum)
        function playAmbient(type) {
            initAudioContext();
            stopAudio();

            // Ustvari šum za ambientne zvoke
            const bufferSize = 2 * audioContext.sampleRate;
            const noiseBuffer = audioContext.createBuffer(1, bufferSize, audioContext.sampleRate);
            const output = noiseBuffer.getChannelData(0);

            for (let i = 0; i < bufferSize; i++) {
                output[i] = Math.random() * 2 - 1;
            }

            const whiteNoise = audioContext.createBufferSource();
            whiteNoise.buffer = noiseBuffer;
            whiteNoise.loop = true;

            // Filter za različne zvoke
            const filter = audioContext.createBiquadFilter();
            const volume = document.getElementById('volumeSlider').value / 100;

            switch(type) {
                case 'rain':
                    filter.type = 'highpass';
                    filter.frequency.setValueAtTime(1000, audioContext.currentTime);
                    break;
                case 'ocean':
                    filter.type = 'lowpass';
                    filter.frequency.setValueAtTime(500, audioContext.currentTime);
                    break;
                case 'forest':
                    filter.type = 'bandpass';
                    filter.frequency.setValueAtTime(2000, audioContext.currentTime);
                    filter.Q.setValueAtTime(1, audioContext.currentTime);
                    break;
                case 'fire':
                    filter.type = 'bandpass';
                    filter.frequency.setValueAtTime(400, audioContext.currentTime);
                    filter.Q.setValueAtTime(2, audioContext.currentTime);
                    break;
                case 'wind':
                    filter.type = 'lowpass';
                    filter.frequency.setValueAtTime(800, audioContext.currentTime);
                    break;
                case 'singing-bowl':
                    filter.type = 'bandpass';
                    filter.frequency.setValueAtTime(800, audioContext.currentTime);
                    filter.Q.setValueAtTime(10, audioContext.currentTime);
                    break;
            }

            currentGain = audioContext.createGain();
            currentGain.gain.setValueAtTime(volume * 0.2, audioContext.currentTime);

            whiteNoise.connect(filter);
            filter.connect(currentGain);
            currentGain.connect(audioContext.destination);

            whiteNoise.start();
            currentAmbient = type;
            isPlaying = true;
            updatePlayButton();
        }

        // Ustavi zvok
        function stopAudio() {
            if (currentOscillator) {
                currentOscillator.stop();
                currentOscillator.disconnect();
                currentOscillator = null;
            }
            if (currentGain) {
                currentGain.disconnect();
                currentGain = null;
            }
            currentFrequency = null;
            currentAmbient = null;
            isPlaying = false;
            updatePlayButton();
        }

        // Posodobi gumb predvajanja
        function updatePlayButton() {
            const playBtn = document.getElementById('playBtn');
            playBtn.textContent = isPlaying ? '⏸️' : '▶️';
        }

        // Dogodki za frekvenčne kartice
        document.querySelectorAll('.frequency-card[data-frequency]').forEach(card => {
            card.addEventListener('click', function() {
                const frequency = parseInt(this.dataset.frequency);
                const name = this.dataset.name;

                // Odstrani active class iz vseh
                document.querySelectorAll('.frequency-card').forEach(c => c.classList.remove('active'));
                document.querySelectorAll('.frequency-card[data-ambient]').forEach(c => c.classList.remove('active'));

                // Dodaj active class na izbrano
                this.classList.add('active');

                document.getElementById('currentTrack').textContent = name;
                playFrequency(frequency);
            });
        });

        // Dogodki za ambientne kartice
        document.querySelectorAll('.frequency-card[data-ambient]').forEach(card => {
            card.addEventListener('click', function() {
                const ambient = this.dataset.ambient;
                const name = this.dataset.name;

                // Odstrani active class iz vseh
                document.querySelectorAll('.frequency-card').forEach(c => c.classList.remove('active'));
                document.querySelectorAll('.frequency-card[data-frequency]').forEach(c => c.classList.remove('active'));

                // Dodaj active class na izbrano
                this.classList.add('active');

                document.getElementById('currentTrack').textContent = name;
                playAmbient(ambient);
            });
        });

        // Gumb predvajanja/pavze
        document.getElementById('playBtn').addEventListener('click', function() {
            if (isPlaying) {
                stopAudio();
                document.getElementById('currentTrack').textContent = 'Izberi frekvenco';
                document.querySelectorAll('.frequency-card').forEach(c => c.classList.remove('active'));
            }
        });

        // Gumbi naprej/nazaj
        document.getElementById('prevBtn').addEventListener('click', function() {
            const cards = Array.from(document.querySelectorAll('.frequency-card[data-frequency]'));
            if (cards.length === 0) return;

            const activeCard = document.querySelector('.frequency-card[data-frequency].active');
            const currentIndex = activeCard ? cards.indexOf(activeCard) : -1;
            const prevIndex = currentIndex <= 0 ? cards.length - 1 : currentIndex - 1;
            cards[prevIndex].click();
        });

        document.getElementById('nextBtn').addEventListener('click', function() {
            const cards = Array.from(document.querySelectorAll('.frequency-card[data-frequency]'));
            if (cards.length === 0) return;

            const activeCard = document.querySelector('.frequency-card[data-frequency].active');
            const currentIndex = activeCard ? cards.indexOf(activeCard) : -1;
            const nextIndex = currentIndex >= cards.length - 1 ? 0 : currentIndex + 1;
            cards[nextIndex].click();
        });

        // Glašnost
        document.getElementById('volumeSlider').addEventListener('input', function() {
            if (currentGain) {
                const volume = this.value / 100;
                currentGain.gain.setValueAtTime(volume * 0.3, audioContext.currentTime);
            }
        });
    </script>
</body>
</html>