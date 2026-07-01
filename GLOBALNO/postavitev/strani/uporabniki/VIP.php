<?php
/**
 * ============================================================
 * POT: GLOBALNO/postavitev/strani/uporabniki/VIP.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     VIP stran - samo za S4+ uporabnike
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 6
 * ============================================================
 */

$naslov = 'VIP cona';
$opis = 'Ekskluzivna vsebina za VIP uporabnike.';

ob_start();

// Preveri vlogo
$vloga = $_SESSION['uporabnik_vloga'] ?? 0;

if ($vloga < 50) {
    echo '<div class="kartica"><h1>VIP cona</h1><p>Ta stran je na voljo samo za S4+ uporabnike.</p><a href="?svet=UPORABNIKI&amp;akcija=vip" class="gumb gumb-glavni">Nadgradi na VIP</a></div>';
} else {
    echo '<div class="kartica"><h1>VIP cona</h1><p>Dobrodošli v VIP coni, ' . htmlspecialchars($_SESSION['uporabnik_ime'] ?? '') . '!</p></div>';
    echo '<div class="znamenitosti"><div class="znamenitost kartica"><h3>Ekskluzivni moduli</h3><p>Dostop do naprednih modulov.</p></div></div>';
}

$vsebina = ob_get_clean();
$postavitev = 'pro';
require_once __DIR__ . '/../postavitev/postavitev_pro.php';