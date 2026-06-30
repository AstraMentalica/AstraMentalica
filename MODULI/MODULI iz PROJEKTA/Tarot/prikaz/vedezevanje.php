<?php
/**
 * POT: MODULI/ORAKLEUM/Tarot/prikaz/vedezevanje.php
 * Pasivna predloga — SAMO HTML, brez poslovne logike.
 * Uporabnik tu fizično "meša" karte (klik/animacija), JS pošlje
 * izbrane karte na modul.php?akcija=vedezi.
 */
declare(strict_types=1);
?>
<div class="tarot-vedezevanje modul-vsebina">
    <h2>🃏 Tarot vedeževanje</h2>

    <div class="tarot-vprasanje">
        <label for="tarotVprasanje">Tvoje vprašanje</label>
        <textarea id="tarotVprasanje" placeholder="O čem želiš vedeževanje?"></textarea>
    </div>

    <div class="tarot-sirjenje">
        <label for="tarotSirjenje">Postavitev</label>
        <select id="tarotSirjenje">
            <option value="ena_karta">Ena karta</option>
            <option value="tri_karte" selected>Tri karte (Preteklost–Sedanjost–Prihodnost)</option>
            <option value="keltski_kriz">Keltski križ (10 kart)</option>
        </select>
    </div>

    <div id="tarotSpil" class="tarot-spil">
        <p class="navodilo">Premešaj špil in izberi karte.</p>
        <button id="tarotMesajGumb" class="btn">🔀 Premešaj karte</button>
        <div id="tarotKarteIzbor" class="tarot-karte-izbor"></div>
    </div>

    <button id="tarotPosljiGumb" class="btn draw-btn" disabled>✨ Razkrij vedeževanje</button>

    <div id="tarotRezultat" class="tarot-rezultat" style="display:none"></div>
</div>

<script src="vmesnik/js/tarot.js"></script>
