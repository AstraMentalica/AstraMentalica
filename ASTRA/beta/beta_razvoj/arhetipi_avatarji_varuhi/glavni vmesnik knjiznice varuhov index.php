<?php
/**
 * ============================================================
 * KNJIŽNICA VARUHOV – Centralno zbirališče AI agentov
 * ============================================================
 * Tukaj se srečajo vsi varuhi, magične živali in modrosti
 */

require_once __DIR__ . '/../../pot.php';
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knjižnica Varuhov – AstraMentalica</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Georgia&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="knjiznica.css">
</head>
<body>

<div class="knjiznica-varuhov">
    
    <!-- Glava -->
    <div class="knjiznica-glava">
        <h1>📚 KNJIŽNICA VARUHOV</h1>
        <p>Zbirališče modrosti – vsak varuh ima svojo zgodbo</p>
    </div>
    
    <!-- Varuhi (gumbi) -->
    <div class="varuhi-mreza" id="varuhi-kontejner">
        <!-- JavaScript bo dodal gumbe -->
        <div style="text-align:center; color:#8ba0c0; padding:20px">Nalagam varuhe...</div>
    </div>
    
    <!-- Pogovor -->
    <div class="pogovor" id="pogovor">
        <div class="sporocilo varuh">
            <div class="ikona">🌀</div>
            <div class="besedilo">Izberi svojega varuha in začni pogovor. Vsak varuh ti bo odgovoril iz svoje modrosti.</div>
        </div>
    </div>
    
    <!-- Vnosna vrstica -->
    <div class="vnosna-vrstica">
        <input type="text" id="vnos" placeholder="Vprašaj varuha..." onkeypress="if(event.key==='Enter') poslji()">
        <button class="gumb-glas" id="glas-gumb" title="Glasovni vnos">🎤</button>
        <button class="gumb-poslji" onclick="poslji()">Pošlji</button>
    </div>
    
</div>

<script src="knjiznica.js"></script>

<script>
// Povezava med UI in knjižnico
function poslji() {
    const vnos = document.getElementById('vnos');
    if (vnos.value.trim()) {
        KnjiznicaVaruhov.posljiSporocilo(vnos.value);
        vnos.value = '';
    }
}

// Dostop do KnjiznicaVaruhov iz globalnega obsega
window.poslji = poslji;
window.KnjiznicaVaruhov = KnjiznicaVaruhov;
</script>

</body>
</html>