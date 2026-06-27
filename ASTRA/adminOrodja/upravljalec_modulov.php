<?php
/**
 * ============================================================
 * POT: ASTRA/orodja/upravljanje/upravljalec_modulov.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Upravljalec modulov
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 6
 * ============================================================
 */

session_start();
if ((\$_SESSION['uporabnik_vloga'] ?? 0) < 60) exit('Nimate dostopa');

\$registracija = new \AstraMentalica\Procesi\Moduli\Registracija();
\$moduli = \$registracija->vse();

echo "<h1>Upravljanje modulov</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Ime</th><th>Verzija</th><th>Status</th><th>Akcije</th></tr>";
foreach (\$moduli as \$ime => \$modul) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars(\$ime) . "</td>";
    echo "<td>" . htmlspecialchars(\$modul['verzija'] ?? '1.0') . "</td>";
    echo "<td>" . htmlspecialchars(\$modul['status'] ?? 'unknown') . "</td>";
    echo "<td><a href='?akcija=aktiviraj&modul=" . urlencode(\$ime) . "'>Aktiviraj</a> | ";
    echo "<a href='?akcija=deaktiviraj&modul=" . urlencode(\$ime) . "'>Deaktiviraj</a> | ";
    echo "<a href='?akcija=odstrani&modul=" . urlencode(\$ime) . "'>Odstrani</a></td>";
    echo "</tr>";
}
echo "</table>";
