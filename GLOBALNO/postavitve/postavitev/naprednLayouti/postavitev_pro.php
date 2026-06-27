<?php
/**
 * ============================================================
 * POT: GLOBALNO/prikaz/postavitev/postavitev_pro.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Pro postavitev (premium) - za S2+ uporabnike
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 6
 * ============================================================
 */

require_once __DIR__ . '/../gradniki/glava.php';
require_once __DIR__ . '/../gradniki/navigacija.php';

// Render glave
$naslov = $naslov ?? 'AstraMentalica PRO';
$dodatniCss = ['premium.css'];
gradnik_glava($naslov, $opis ?? '', $kljucneBesede ?? '', $dodatniCss);

// Render navigacije
$uporabnik = $uporabnik ?? null;
gradnik_navigacija($uporabnik);

// PRO layout - dvostolpčni
echo '<div class="pro-layout">';
echo '<aside class="sidebar">';
if (isset($sidebar)) {
    echo $sidebar;
} else {
    echo '<div class="kartica">';
    echo '<h3>Hitri meni</h3>';
    echo '<ul>';
    echo '<li><a href="?svet=MODULI_CEL&amp;rel=NEBO/Stelaris">Zvezdoslov</a></li>';
    echo '<li><a href="?svet=MODULI_CEL&amp;rel=ORAKLEUM/OrakleumTarot">Tarot</a></li>';
    echo '<li><a href="?svet=UPORABNIKI&amp;akcija=passport">PASSPORT</a></li>';
    echo '</ul>';
    echo '</div>';
}
echo '</aside>';
echo '<main class="glavna-pro">';
if (isset($vsebina)) {
    echo $vsebina;
}
echo '</main>';
echo '</div>';

require_once __DIR__ . '/../gradniki/noga.php';
gradnik_noga();
?>