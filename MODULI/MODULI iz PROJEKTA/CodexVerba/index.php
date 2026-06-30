<?php
/**
 * Codex Damiris - Glavni Index z Glavo in Nogo
 * Lokacija: /var/www/html/codex-damiris/index.php
 */

// Začni sejo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vključi vse potrebne datoteke
require_once 'CodexDamirisJedro.php';
require_once 'CodexDamirisFunkcije.php';
require_once 'AI_CodexDamiris.php';
require_once 'CodexDamiris.php';

// HTML glava
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codex Damiris - Živa Knjiga Znanja</title>
    <style>
        /* Globalni slogi */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .codex-glava {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .codex-glava-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .codex-logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
        }
        
        .codex-navigacija {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .codex-navigacija a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .codex-navigacija a:hover {
            color: #667eea;
        }
        
        .codex-vsebina {
            min-height: calc(100vh - 140px);
            padding: 2rem 0;
        }
        
        .codex-noga {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .codex-noga-container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <header class="codex-glava">
        <div class="codex-glava-container">
            <a href="/codex-damiris/" class="codex-logo">
                📖 Codex Damiris
            </a>
            <nav class="codex-navigacija">
                <a href="/codex-damiris/">Domov</a>
                <a href="/codex-damiris/?akcija=iskanje">Iskanje</a>
                <a href="/codex-damiris/?akcija=prikaziRegistracija">Registracija</a>
                <a href="/codex-damiris/?akcija=prikaziPrijava">Prijava</a>
            </nav>
        </div>
    </header>

    <main class="codex-vsebina">
        <?php
        // Zaženi Codex Damiris
        try {
            $codexDamiris = new CodexDamiris();
            $codexDamiris->obdelajZahtevo();
            
        } catch (Exception $e) {
            echo '<div class="codex-sporocilo codex-sporocilo-napaka">';
            echo 'Napaka v sistemu: ' . CodexDamirisFunkcije::varniIzhod($e->getMessage());
            echo '</div>';
            
            // Logiraj napako
            CodexDamirisFunkcije::logiraj('SISTEMSKA_NAPAKA: ' . $e->getMessage());
        }
        ?>
    </main>

    <footer class="codex-noga">
        <div class="codex-noga-container">
            <p>&copy; 2024 Codex Damiris - Živa Knjiga Znanja. Vse pravice pridržane.</p>
            <p>Razvito z ljubeznijo do modrosti in znanja.</p>
        </div>
    </footer>
</body>
</html>'CodexDamirisJedro.php';
require_once 'CodexDamirisFunkcije.php';
require_once 'AI_CodexDamiris.php';
require_once 'CodexDamiris.php';

// HTML glava
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codex Damiris - Živa Knjiga Znanja</title>
    <style>
        /* Globalni slogi */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .codex-glava {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .codex-glava-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .codex-logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
        }
        
        .codex-navigacija {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .codex-navigacija a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .codex-navigacija a:hover {
            color: #667eea;
        }
        
        .codex-vsebina {
            min-height: calc(100vh - 140px);
            padding: 2rem 0;
        }
        
        .codex-noga {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .codex-noga-container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <header class="codex-glava">
        <div class="codex-glava-container">
            <a href="/codex-damiris/" class="codex-logo">
                📖 Codex Damiris
            </a>
            <nav class="codex-navigacija">
                <a href="/codex-damiris/">Domov</a>
                <a href="/codex-damiris/?akcija=iskanje">Iskanje</a>
                <a href="/codex-damiris/?akcija=prikaziRegistracija">Registracija</a>
                <a href="/codex-damiris/?akcija=prikaziPrijava">Prijava</a>
            </nav>
        </div>
    </header>

    <main class="codex-vsebina">
        <?php
        // Zaženi Codex Damiris
        try {
            $codexDamiris = new CodexDamiris();
            $codexDamiris->obdelajZahtevo();
            
        } catch (Exception $e) {
            echo '<div class="codex-sporocilo codex-sporocilo-napaka">';
            echo 'Napaka v sistemu: ' . CodexDamirisFunkcije::varniIzhod($e->getMessage());
            echo '</div>';
            
            // Logiraj napako
            CodexDamirisFunkcije::logiraj('SISTEMSKA_NAPAKA: ' . $e->getMessage());
        }
        ?>
    </main>

    <footer class="codex-noga">
        <div class="codex-noga-container">
            <p>&copy; 2024 Codex Damiris - Živa Knjiga Znanja. Vse pravice pridržane.</p>
            <p>Razvito z ljubeznijo do modrosti in znanja.</p>
        </div>
    </footer>
</body>
</html>