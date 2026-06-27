<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Kozmično vesolje — Aeternum</title>
    <link rel="stylesheet" href="<?php echo GLOBALNO_POT; ?>/3d/css/universe.css">
    <style>
        /* Dodatni starodavni elementi za UI */
        .universe-title {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            color: #e8c84a;
            font-family: 'Palatino', serif;
            text-shadow: 0 0 10px rgba(0,0,0,0.5);
            pointer-events: none;
        }
        .universe-title h1 {
            font-size: 1.5rem;
            letter-spacing: 4px;
            margin: 0;
        }
        .universe-title p {
            font-size: 0.7rem;
            margin: 0;
            color: #b8960c;
        }
        .back-to-classic {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            background: rgba(10,8,6,0.8);
            border: 1px solid #b8960c;
            color: #d4c5a9;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            backdrop-filter: blur(5px);
            transition: all 0.3s;
        }
        .back-to-classic:hover {
            background: #b8960c;
            color: #0a0806;
        }
    </style>
</head>
<body>
    <div class="universe-title">
        <h1>⚝ AETERNUM ⚝</h1>
        <p>kozmično vesolje znanja</p>
    </div>
    
    <a href="/?verzija=klasicna" class="back-to-classic">⬅ Klasični pogled</a>
    
    <div class="controls-hint">
        🖱 Klikni planet | ⬆⬇⬅➡ premik | C centriraj | ESC zapri
    </div>
    
    <div id="universe-container">
        <?php echo $vsebina ?? ''; ?>
    </div>
    
    <div id="loading-overlay">
        <div class="loading-text">
            <div class="loading-spinner"></div>
            <div>Nalagam kozmos...</div>
            <div style="font-size: 0.8rem; margin-top: 20px;">✨ Povezujemo se z zvezdami ✨</div>
        </div>
    </div>
    
    <script>
        // Skrij loading overlay ko se vse naloži
        window.addEventListener('load', function() {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                setTimeout(() => {
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.remove(), 1000);
                }, 500);
            }
        });
    </script>
</body>
</html>