<?php
// Preusmeritev na splošno predlogo (če želite imeti enotno rešitev)
// V praksi bi to naredili drugače, za zdaj uporabimo direktno vsebino

/**
 * Samostojni portal za Codex modul
 * Omogoča neodvisno delovanje brez odvisnosti od glavnega sistema
 */

// Demo seja - za samostojno delovanje
session_start();

// Demo uporabnik (lahko se prijavite s poljubnim uporabniškim imenom in geslom)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uporabnisko_ime'])) {
    $_SESSION['uporabnik_id'] = 1;
    $_SESSION['uporabnisko_ime'] = $_POST['uporabnisko_ime'];
    $_SESSION['vloga'] = 'uporabnik';
    $_SESSION['jezik'] = 'sl';
}

// Preusmeri na odjavo, če je zahtevano
if (isset($_GET['odjava'])) {
    session_unset();
    session_destroy();
    header('Location: index.php?sporocilo=uspesno_odjavljen');
    exit();
}

// Preveri ali je uporabnik prijavljen
$je_prijavljen = isset($_SESSION['uporabnik_id']);

// Naloži manifest modula
$manifest = [];
if (file_exists('manifest.json')) {
    $manifest = json_decode(file_get_contents('manifest.json'), true);
}

// Privzete vrednosti, če manifest ne obstaja
$ime_modula = $manifest['ime'] ?? 'Codex';
$opis_modula = $manifest['opis'] ?? 'Živa knjiga modrosti';
$verzija = $manifest['različica'] ?? '1.0.0';
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ime_modula) ?> - AstraMentalica</title>
    
    <!-- Font Awesome ikone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Samostojni stili za modul -->
    <style>
        /* Osnovni reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary: #0a0a2a;
            --secondary: #12125a;
            --accent: #4a1c7a;
            --glow: #6a3da3;
            --text: #e6e6ff;
            --highlight: #ff57a0;
            --success: #00c864;
            --warning: #ffc800;
            --danger: #ff3860;
            --card-bg: rgba(10, 10, 42, 0.7);
            --card-border: rgba(106, 61, 163, 0.3);
        }
        
        body {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--text);
            min-height: 100vh;
            padding: 20px;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(106, 61, 163, 0.2) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(255, 87, 160, 0.2) 0%, transparent 40%);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Glava */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--text), var(--highlight));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .user-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--accent), var(--highlight));
            color: white;
        }
        
        /* Vsebina modula */
        .module-content {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid var(--card-border);
            backdrop-filter: blur(10px);
            margin-bottom: 2rem;
        }
        
        /* Prijava */
        .login-form {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 15px;
            border: 1px solid var(--card-border);
            backdrop-filter: blur(10px);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 5px;
            border: 1px solid var(--card-border);
            background: rgba(10, 10, 42, 0.5);
            color: var(--text);
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--glow);
            box-shadow: 0 0 10px rgba(106, 61, 163, 0.5);
        }
        
        /* Noga */
        footer {
            text-align: center;
            margin-top: 3rem;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(230, 230, 255, 0.6);
        }
        
        .module-info {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--card-border);
        }
        
        .module-info ul {
            list-style: none;
        }
        
        .module-info li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }
        
        .module-info li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--highlight);
        }
        
        .sporocilo {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .sporocilo-uspeh {
            background: rgba(0, 200, 100, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .codex-preview {
            margin: 2rem 0;
            padding: 1.5rem;
            background: rgba(106, 61, 163, 0.2);
            border-radius: 10px;
            border-left: 4px solid var(--highlight);
        }
    </style>
    
    <!-- Stili modula -->
    <link rel="stylesheet" href="slog.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">AstraMentalica - <?= htmlspecialchars($ime_modula) ?></div>
            <div class="user-actions">
                <?php if ($je_prijavljen): ?>
                    <span>Pozdravljen, <?= htmlspecialchars($_SESSION['uporabnisko_ime']) ?></span>
                    <a href="?odjava=1" class="btn"><i class="fas fa-sign-out-alt"></i> Odjava</a>
                <?php else: ?>
                    <span>Demo modul</span>
                <?php endif; ?>
            </div>
        </header>
        
        <main>
            <?php if (isset($_GET['sporocilo']) && $_GET['sporocilo'] === 'uspesno_odjavljen'): ?>
                <div class="sporocilo sporocilo-uspeh">
                    <i class="fas fa-check-circle"></i> Uspešno odjavljeni
                </div>
            <?php endif; ?>
            
            <?php if (!$je_prijavljen): ?>
                <!-- Prikaz obrazca za prijavo -->
                <div class="login-form">
                    <h2>Prijava v <?= htmlspecialchars($ime_modula) ?></h2>
                    <p>Vpiši poljubno uporabniško ime in geslo za demo dostop</p>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="uporabnisko_ime">Uporabniško ime:</label>
                            <input type="text" id="uporabnisko_ime" name="uporabnisko_ime" value="demo" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="geslo">Geslo:</label>
                            <input type="password" id="geslo" name="geslo" value="demo" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%">
                            <i class="fas fa-sign-in-alt"></i> Prijava
                        </button>
                    </form>
                    
                    <div style="text-align: center; margin-top: 1rem;">
                        <small>Namig: Uporabi "demo" / "demo" za hitro prijavo</small>
                    </div>
                </div>
            <?php else: ?>
                <!-- Prikaz vsebine modula -->
                <div class="module-content">
                    <h1>Dobrodošli v <?= htmlspecialchars($ime_modula) ?></h1>
                    <p><?= htmlspecialchars($opis_modula) ?></p>
                    
                    <div class="codex-preview">
                        <h3><i class="fas fa-lightbulb"></i> Hitri pregled</h3>
                        <p>Codex je živa knjiga modrosti, ki se nenehno razvija in prilagaja potrebam iskalcev resnice.</p>
                    </div>
                    
                    <div style="margin: 2rem 0; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="stran.php" class="btn btn-primary">
                            <i class="fas fa-book-open"></i> Odpri Codex
                        </a>
                        <a href="/main/index.php" class="btn">
                            <i class="fas fa-globe"></i> Glavni portal
                        </a>
                        <a href="/main/sistem/nadzor.php" class="btn">
                            <i class="fas fa-cog"></i> Nadzorna plošča
                        </a>
                    </div>
                    
                    <div class="module-info">
                        <h3>Informacije o modulu:</h3>
                        <ul>
                            <li><strong>Status:</strong> Aktiven</li>
                            <li><strong>Različica:</strong> <?= htmlspecialchars($verzija) ?></li>
                            <li><strong>Zadnja posodobitev:</strong> <?= date('Y-m-d') ?></li>
                            <li><strong>Način:</strong> Samostojni demo</li>
                            <li><strong>Poglavja:</strong> Ognjena, Ljubezenska, Mistična</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>© 2025 AstraMentalica - <?= htmlspecialchars($ime_modula) ?> | Samostojna različica</p>
        </footer>
    </div>

    <!-- JavaScript za modul -->
    <script src="uporaba.js"></script>
    
    <script>
        // Osnovne funkcije za samostojni modul
        document.addEventListener('DOMContentLoaded', function() {
            console.log('<?= htmlspecialchars($ime_modula) ?> modul naložen v samostojnem načinu');
        });
    </script>
</body>
</html>