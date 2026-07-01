<?php
/**
 * ============================================================
 * POT: GLOBALNO/postavitev/strani/uporabniki/trgovina.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Trgovina - duhovni pripomočki
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 6
 * ============================================================
 */

$naslov = 'Trgovina';
$opis = 'Duhovni pripomočki za vašo pot.';

ob_start();
?>

<div class="kartica">
    <h1>Trgovina</h1>
    <p>Raziščite našo ponudbo duhovnih pripomočkov.</p>
</div>

<div class="znamenitosti">
    <div class="znamenitost kartica">
        <div class="znamenitost-ikona">🔮</div>
        <h3>Tarot karte</h3>
        <p>Komplet 78 tarot kart za vedeževanje.</p>
        <span class="cena">39,99€</span>
        <button class="gumb gumb-stranski">V košarico</button>
    </div>
    <div class="znamenitost kartica">
        <div class="znamenitost-ikona">💎</div>
        <h3>Kristali</h3>
        <p>Naravni kristali za energijsko čiščenje.</p>
        <span class="cena">24,99€</span>
        <button class="gumb gumb-stranski">V košarico</button>
    </div>
    <div class="znamenitost kartica">
        <div class="znamenitost-ikona">📖</div>
        <h3>Duhovne knjige</h3>
        <p>Knjige o meditaciji, astrologiji in tarotu.</p>
        <span class="cena">19,99€</span>
        <button class="gumb gumb-stranski">V košarico</button>
    </div>
</div>

<?php
$vsebina = ob_get_clean();
$postavitev = 'osnovno';
require_once __DIR__ . '/../postavitev/postavitev_osnovno.php';