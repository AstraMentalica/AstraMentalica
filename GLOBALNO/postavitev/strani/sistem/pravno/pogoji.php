<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/strani/pravno/pogoji.php
 * v111 (27.5.2026 16:00)
 * ---------------------------------------------------------
 * OPIS: Pogoji uporabe stran
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - Ko je pot 'pogoji'
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

if (!isset($vsebina)) {
    $vsebina = [];
}
?>

<div class="pravno-stran">
    <div class="pravno-glava">
        <h1 class="pravno-naslov">📜 Pogoji uporabe</h1>
        <p class="pravno-datum">Zadnja posodobitev: <?= date('d.m.Y') ?></p>
    </div>
    
    <div class="pravno-vsebina">
        <div class="pravno-clanek">
            <h2>1. Sprejem pogojev</h2>
            <p>Z uporabo sistema <?= IME_APLIKACIJE ?> se strinjate s temi pogoji uporabe. Če se ne strinjate, prosimo, da sistema ne uporabljate.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>2. Opis storitve</h2>
            <p><?= IME_APLIKACIJE ?> je platforma za duhovni razvoj in raziskovanje, ki ponuja različne module in orodja za osebno rast. Vsebine so informativne narave in niso nadomestilo za strokovno medicinsko ali psihološko pomoč.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>3. Uporabniški račun</h2>
            <p>Za uporabo določenih funkcionalnosti potrebujete registriran uporabniški račun. Odgovorni ste za varnost vaših prijavnih podatkov in vse aktivnosti, ki se izvajajo pod vašim računom.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>4. Pravila obnašanja</h2>
            <ul>
                <li>Ne objavljajte nezakonitih, žaljivih ali škodljivih vsebin.</li>
                <li>Ne poskušajte vdreti v sistem ali ogroziti njegove varnosti.</li>
                <li>Ne uporabljajte sistema za oglaševanje brez dovoljenja.</li>
                <li>Spoštujte avtorske pravice drugih.</li>
            </ul>
        </div>
        
        <div class="pravno-clanek">
            <h2>5. Zasebnost in podatki</h2>
            <p>Vaši osebni podatki se obravnavajo v skladu z našo <a href="?svet=GLOBALNO&amp;pot=zasebnost">Politiko zasebnosti</a>. Z uporabo sistema dovoljujete zbiranje in obdelavo podatkov, kot je opisano v politiki zasebnosti.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>6. Odgovornost</h2>
            <p><?= IME_APLIKACIJE ?> ne odgovarja za morebitno škodo, ki bi nastala zaradi uporabe ali nezmožnosti uporabe sistema. Vsebine so zgolj informativne narave.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>7. Spremembe pogojev</h2>
            <p>Pridružujemo si pravico do spremembe teh pogojev kadar koli. Spremembe bodo objavljene na tej strani. Vaša nadaljnja uporaba sistema pomeni sprejem spremenjenih pogojev.</p>
        </div>
        
        <div class="pravno-clanek">
            <h2>8. Kontakt</h2>
            <p>Za vprašanja glede pogojev uporabe nas kontaktirajte na: <a href="mailto:info@astramentalica.com">info@astramentalica.com</a></p>
        </div>
        
        <div class="pravno-strinjanje">
            <p>S klikom na "Strinjam se" potrjujete, da ste prebrali in razumeli pogoje uporabe.</p>
            <button class="gumb gumb-primaren" id="strinjamSeGumb">Strinjam se</button>
        </div>
    </div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
document.getElementById('strinjamSeGumb')?.addEventListener('click', () => {
    localStorage.setItem('pogoji_sprejeti', 'true');
    alert('Hvala za vaše strinjanje.');
    window.location.href = '?svet=GLOBALNO';
});
</script>

<style>
.pravno-stran {
    max-width: 800px;
    margin: 0 auto;
}

.pravno-glava {
    text-align: center;
    margin-bottom: 2rem;
}

.pravno-naslov {
    font-size: 2rem;
    color: #e8c84a;
    margin-bottom: 0.5rem;
}

.pravno-datum {
    color: #888;
    font-size: 0.9rem;
}

.pravno-vsebina {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 20px;
    padding: 2rem;
}

.pravno-clanek {
    margin-bottom: 2rem;
}

.pravno-clanek h2 {
    color: #e8c84a;
    font-size: 1.3rem;
    margin-bottom: 0.75rem;
}

.pravno-clanek p {
    color: #d4c5a9;
    line-height: 1.6;
}

.pravno-clanek ul {
    color: #d4c5a9;
    margin-left: 1.5rem;
    line-height: 1.6;
}

.pravno-strinjanje {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(232, 200, 74, 0.2);
    text-align: center;
}

.pravno-strinjanje p {
    margin-bottom: 1rem;
    color: #aaa;
}
</style>