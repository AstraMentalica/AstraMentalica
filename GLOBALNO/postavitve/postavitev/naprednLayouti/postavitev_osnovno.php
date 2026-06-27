<?php
/**
 * ============================================================
 * POT: GLOBALNO/prikaz/postavitev/postavitev_osnovno.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Osnovna postavitev (layout) - uporabljajo jo vse strani
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 6
 * ============================================================
 */

// Vključi gradnike
require_once __DIR__ . '/../gradniki/glava.php';
require_once __DIR__ . '/../gradniki/navigacija.php';

// Render glave
$naslov = $naslov ?? 'AstraMentalica';
$opis = $opis ?? 'Večsvetovni runtime sistem za duhovni razvoj';
$kljucneBesede = $kljucneBesede ?? 'duhovnost, tarot, astrologija, meditacija';

gradnik_glava($naslov, $opis, $kljucneBesede);

// Render navigacije
$uporabnik = $uporabnik ?? null;
gradnik_navigacija($uporabnik);

// Vsebina (pride iz strani)
echo '<main class="glavna">';
if (isset($vsebina)) {
    echo $vsebina;
} else {
    echo '<div class="kartica">';
    echo '<h1>' . htmlspecialchars($naslov) . '</h1>';
    echo '<p>Vsebina ni na voljo.</p>';
    echo '</div>';
}
echo '</main>';

// Noga
require_once __DIR__ . '/../gradniki/noga.php';
gradnik_noga();
?>