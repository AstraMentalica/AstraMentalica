<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/uporabnik_registracija.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Registracija uporabnikov z Google OAuth
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Preveri ali je že prijavljen
if (seja_je_prijavljen()) {
    header('Location: ?svet=UPORABNIKI&pot=profil');
    exit;
}

$napaka = '';
$uspeh = '';

// Obdelava Google OAuth callback
if (isset($_GET['google_callback'])) {
    // Tukaj bo implementiran Google OAuth callback
    // Za zdaj preusmeri na prijavno stran
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}

// Obdelava registracije
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $geslo = $_POST['geslo'] ?? '';
    $gesloPotrdi = $_POST['geslo_potrdi'] ?? '';
    
    // Validacija
    if (empty($ime) || empty($email) || empty($geslo)) {
        $napaka = 'Vsa polja so obvezna.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $napaka = 'Neveljaven e-poštni naslov.';
    } elseif (strlen($geslo) < 8) {
        $napaka = 'Geslo mora imeti vsaj 8 znakov.';
    } elseif ($geslo !== $gesloPotrdi) {
        $napaka = 'Gesli se ne ujemata.';
    } else {
        // Preveri ali email že obstaja
        $uporabniki = baza_beri('uporabniki');
        $emailObstaja = false;
        foreach ($uporabniki as $u) {
            if ($u['elektronski_naslov'] === $email) {
                $emailObstaja = true;
                break;
            }
        }
        
        if ($emailObstaja) {
            $napaka = 'Uporabnik s tem e-poštnim naslovom že obstaja.';
        } else {
            // Ustvari novega uporabnika
            $noviUporabnik = [
                'id' => uniqid('usr_', true),
                'ime' => $ime,
                'elektronski_naslov' => $email,
                'hash_gesla' => password_hash($geslo, PASSWORD_BCRYPT),
                'aktiviran' => true,
                'vloga' => 10, // Osnovna uporabniška vloga
                'ustvarjen' => time(),
                'google_oauth' => false
            ];
            
            baza_zapisi('uporabniki', $noviUporabnik);
            
            // Avtomatska prijava
            $_SESSION['uporabnik_id'] = $noviUporabnik['id'];
            $_SESSION['uporabnik_ime'] = $noviUporabnik['ime'];
            $_SESSION['uporabnik_email'] = $noviUporabnik['elektronski_naslov'];
            $_SESSION['uporabnik_vloga'] = $noviUporabnik['vloga'];
            
            dogodek_sprozi('uporabnik.registriran', [
                'uporabnik_id' => $noviUporabnik['id'],
                'email' => $email
            ]);
            
            header('Location: ?svet=UPORABNIKI&pot=nastavitve');
            exit;
        }
    }
}

// Google OAuth URL
$googleClientId = getenv('GOOGLE_CLIENT_ID') ?: 'VAŠ_GOOGLE_CLIENT_ID';
$googleRedirectUri = urlencode((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/?svet=UPORABNIKI&pot=google_callback');
$googleOAuthUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => $googleClientId,
    'redirect_uri' => $googleRedirectUri,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'offline',
    'prompt' => 'consent'
]);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija | AstraMentalica</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Glassmorphism background elements */
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

        .glass-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .glass-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .glass-header h1 {
            color: #fff;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .glass-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        .glass-form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .glass-input-group {
            position: relative;
        }

        .glass-input-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .glass-input-group input {
            width: 100%;
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
            width: 100%;
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

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.3);
        }

        .divider span {
            padding: 0 1rem;
        }

        .google-button {
            width: 100%;
            padding: 0.9rem;
            background: #fff;
            border: none;
            border-radius: 12px;
            color: #333;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .google-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .google-button svg {
            width: 20px;
            height: 20px;
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

        .glass-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .glass-footer a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        .glass-footer a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .glass-container {
                padding: 1.5rem;
            }
            
            .glass-header h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="glass-header">
            <h1>✨ AstraMentalica</h1>
            <p>Ustvari svoj račun</p>
        </div>

        <?php if ($napaka): ?>
            <div class="glass-error"><?= htmlspecialchars($napaka) ?></div>
        <?php endif; ?>

        <?php if ($uspeh): ?>
            <div class="glass-success"><?= htmlspecialchars($uspeh) ?></div>
        <?php endif; ?>

        <form method="post" class="glass-form">
            <div class="glass-input-group">
                <label for="ime">Ime</label>
                <input 
                    type="text" 
                    id="ime" 
                    name="ime" 
                    placeholder="Tvoje ime" 
                    required 
                    autofocus
                >
            </div>

            <div class="glass-input-group">
                <label for="email">E-pošta</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="tvoj@email.com" 
                    required
                >
            </div>

            <div class="glass-input-group">
                <label for="geslo">Geslo</label>
                <input 
                    type="password" 
                    id="geslo" 
                    name="geslo" 
                    placeholder="Najmanj 8 znakov" 
                    required
                    minlength="8"
                >
            </div>

            <div class="glass-input-group">
                <label for="geslo_potrdi">Potrdi geslo</label>
                <input 
                    type="password" 
                    id="geslo_potrdi" 
                    name="geslo_potrdi" 
                    placeholder="Ponovno vnesi geslo" 
                    required
                    minlength="8"
                >
            </div>

            <button type="submit" class="glass-button">
                Registracija
            </button>
        </form>

        <div class="divider">
            <span>ali</span>
        </div>

        <a href="<?= htmlspecialchars($googleOAuthUrl) ?>" class="google-button">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Nadaljuj z Google
        </a>

        <div class="glass-footer">
            Že imaš račun? <a href="?svet=UPORABNIKI&pot=prijava">Prijavi se</a>
        </div>
    </div>
</body>
</html>