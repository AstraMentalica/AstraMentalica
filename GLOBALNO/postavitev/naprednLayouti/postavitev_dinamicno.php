<?php
/**
 * ============================================================
 * POT: GLOBALNO/prikaz/postavitev/postavitev_dinamicno.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Dinamična postavitev (layout) - prilagodljiva
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 6
 * ============================================================
 */

require_once __DIR__ . '/../gradniki/glava.php';
require_once __DIR__ . '/../gradniki/navigacija.php';

$naslov = $naslov ?? 'AstraMentalica';
$opis = $opis ?? 'Večsvetovni runtime sistem';

gradnik_glava($naslov, $opis, $kljucneBesede ?? '');

// Dinamična navigacija z dodatnimi možnostmi
$uporabnik = $uporabnik ?? null;
$dodatni_menu = $dodatni_menu ?? [];
gradnik_navigacija($uporabnik, $dodatni_menu);

echo '<div class="dinamicno-layout">';
echo '<aside class="sidebar-levo">';
if (isset($leva_sidebar)) {
    echo $leva_sidebar;
} else {
    echo '<div class="kartica"><h3>Meni</h3><ul><li><a href="?svet=MODULI_CEL">Moduli</a></li><li><a href="?svet=GLOBALNO&amp;stran=trgovina">Trgovina</a></li></ul></div>';
}
echo '</aside>';

echo '<main class="glavna-dinamicno">';
if (isset($vsebina)) {
    echo $vsebina;
}
echo '</main>';

echo '<aside class="sidebar-desno">';
if (isset($desna_sidebar)) {
    echo $desna_sidebar;
} else {
    echo '<div class="kartica"><h3>Novice</h3><p>Ni novic.</p></div>';
}
echo '</aside>';
echo '</div>';

require_once __DIR__ . '/../gradniki/noga.php';
gradnik_noga();