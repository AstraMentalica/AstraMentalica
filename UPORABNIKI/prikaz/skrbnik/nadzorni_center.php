<?php
/**
 * ============================================================
 * POT: ASTRA/nadzorni_center.php
 * ============================================================
 * 
 * @package AstraMentalica\Astra
 * 
 * 📦 NAMEN:
 *     Admin nadzorni center - samo S5+ dostop
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 6
 * ============================================================
 */

// Preveri ali je uporabnik S5+ (vloga >= 60)
session_start();
$vloga = $_SESSION['uporabnik_vloga'] ?? 0;

if ($vloga < 60) {
    header('Location: ?svet=&error=Nimate dostopa do admin centra');
    exit;
}

$naslov = 'Nadzorni center';
$opis = 'Administratorsko upravljanje sistema.';

ob_start();
?>

<div class="kartica">
    <h1>Nadzorni center</h1>
    <p>Dobrodošli v admin panelu, <?php echo htmlspecialchars($_SESSION['uporabnik_ime'] ?? 'Admin'); ?></p>
</div>

<div class="znamenitosti">
    <div class="znamenitost kartica">
        <div class="znamenitost-ikona">📦</div>
        <h3>Upravljanje modulov</h3>
        <p>Pregled, namestitev, aktivacija in deaktivacija modulov.</p>
        <a href="?svet=ASTRA&amp;orodje=moduli" class="nav-gumb">Odpri</a>
    </div>
    
    <div class="znamenitost kartica">
        <div class="znamenitost-ikona">👥</div>
        <h3>Upravljanje uporabnikov</h3>
        <p>Pregled, urejanje in spreminjanje vlog uporabnikov.</p>
        <a href="?svet=ASTRA&amp;orodje=uporabniki" class="nav-gumb">Odpri</a>
    </div>
    
    <div class="znamenitost kartica">
        <div class="znamenitost-ikona">📊</div>
        <h3>Diagnostika</h3>
        <p>Preverjanje zdravja sistema, logi in monitoring.</p>
        <a href="?svet=ASTRA&amp;orodje=diagnostika" class="nav-gumb">Odpri</a>
    </div>
    
    <div class="znamenitost kartica">
        <div class="znamenitost-ikona">⚙️</div>
        <h3>Sistemske nastavitve</h3>
        <p>Konfiguracija sistema in okoljske spremenljivke.</p>
        <a href="?svet=ASTRA&amp;orodje=nastavitve" class="nav-gumb">Odpri</a>
    </div>
</div>

<div class="kartica">
    <h2>Sistemske informacije</h2>
    <ul>
        <li><strong>Verzija:</strong> 8.0.0</li>
        <li><strong>PHP:</strong> <?php echo PHP_VERSION; ?></li>
        <li><strong>Čas strežnika:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
        <li><strong>Aktivni uporabniki:</strong> <?php echo rand(1, 50); ?> (simulirano)</li>
    </ul>
</div>

<?php
$vsebina = ob_get_clean();
$postavitev = 'admin';
require_once __DIR__ . '/../GLOBALNO/prikaz/postavitev/postavitev_osnovno.php';
?>