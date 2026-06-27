<?php
/**
 * DATOTEKA: puerilis_layout.php
 * NAMEN:    Otroška postavitev za Aeternum Puerilis
 * NIVO:     0 (frontend)
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo $naslov_strani ?? 'Aeternum Puerilis — Zgodbice za male srčke'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700&family=Nunito:wght@300;400;700&family=Quicksand:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo GLOBALNO_POT; ?>/slog/puerilis.css">
</head>
<body class="puerilis-body">
    <!-- Zabavna navigacija -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h1 class="puerilis-h1">📚 Aeternum Puerilis</h1>
                <p style="font-size: 0.9rem;">Zgodbice, ki ogrejejo srce</p>
            </div>
            <div class="puerilis-map">
                <a href="/?verzija=puerilis&starost=3" class="puerilis-map-item">
                    🐣 <span>3 leta</span>
                </a>
                <a href="/?verzija=puerilis&starost=5" class="puerilis-map-item">
                    🐰 <span>5 let</span>
                </a>
                <a href="/?verzija=puerilis&starost=7" class="puerilis-map-item">
                    🦊 <span>7 let</span>
                </a>
                <a href="/?verzija=puerilis&starost=9" class="puerilis-map-item">
                    🐺 <span>9 let</span>
                </a>
                <a href="/?verzija=puerilis&starost=12" class="puerilis-map-item">
                    🦅 <span>12 let</span>
                </a>
            </div>
        </div>
        
        <!-- Iskanje -->
        <div style="text-align: center; margin: 1rem 0;">
            <form method="GET" action="/" style="display: inline-flex; gap: 0.5rem;">
                <input type="hidden" name="verzija" value="puerilis">
                <input type="hidden" name="modul" value="Aeternum">
                <input type="text" name="q" placeholder="🔍 Išči zgodbico..." style="padding: 10px 20px; border-radius: 50px; border: none; font-size: 1rem; width: 250px;">
                <button type="submit" class="puerilis-btn puerilis-btn-blue">Išči</button>
            </form>
        </div>
    </div>
    
    <!-- Glavna vsebina -->
    <main style="max-width: 900px; margin: 0 auto; padding: 1rem;">
        <?php echo $vsebina ?? '<p class="puerilis-card">🌟 Izberi zgodbico zgoraj ali poišči nekaj čudovitega! 🌟</p>'; ?>
    </main>
    
    <!-- Noga z željami -->
    <footer style="text-align: center; padding: 2rem; margin-top: 2rem; border-top: 2px dotted var(--puerilis-primary);">
        <p>⭐ Naj bo tvoje srce polno zgodbic ⭐</p>
        <p style="font-size: 0.8rem;">Aeternum Puerilis — Zgodbe, ki rastejo s tabo</p>
    </footer>
    
    <script src="<?php echo GLOBALNO_POT; ?>/skripte/puerilis.js"></script>
    <script>
        // Dodamo male animacije ob kliku na kartice
        document.querySelectorAll('.puerilis-card').forEach(card => {
            card.addEventListener('click', () => {
                card.classList.add('puerilis-bounce');
                setTimeout(() => card.classList.remove('puerilis-bounce'), 500);
            });
        });
    </script>
</body>
</html>