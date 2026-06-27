<?php
/**
 * ============================================================
 * POT: GLOBALNO/prikaz/postavitev/postavitev_telefon.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Telefonska postavitev (mobilna) - prilagojena manjšim zaslonom
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

// Dodaj mobilni CSS
$dodatni_css = ['mobilno.css'];
gradnik_glava($naslov, $opis, $kljucneBesede ?? '', $dodatni_css);

$uporabnik = $uporabnik ?? null;
gradnik_navigacija($uporabnik);

echo '<div class="mobilni-layout">';
echo '<button class="mobilni-menu-gumb" id="mobilniMenuGumb">☰ Meni</button>';
echo '<div class="mobilni-menu" id="mobilniMenu">';
echo '<a href="?svet=">Domov</a>';
echo '<a href="?svet=MODULI_CEL">Moduli</a>';
echo '<a href="?svet=GLOBALNO&amp;stran=trgovina">Trgovina</a>';
if ($uporabnik) {
    echo '<a href="?svet=UPORABNIKI&amp;akcija=profil">Profil</a>';
    echo '<a href="?svet=UPORABNIKI&amp;akcija=odjava">Odjava</a>';
} else {
    echo '<a href="?svet=UPORABNIKI&amp;akcija=prijava">Prijava</a>';
    echo '<a href="?svet=UPORABNIKI&amp;akcija=registracija">Registracija</a>';
}
echo '</div>';
echo '<main class="glavna-mobilna">';
if (isset($vsebina)) {
    echo $vsebina;
}
echo '</main>';
echo '</div>';

echo '<script>
document.getElementById("mobilniMenuGumb")?.addEventListener("click", function() {
    var menu = document.getElementById("mobilniMenu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
});
</script>';

require_once __DIR__ . '/../gradniki/noga.php';
gradnik_noga();