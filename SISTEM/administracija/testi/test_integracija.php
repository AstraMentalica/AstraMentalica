<?php
/**
 * ============================================================
 * POT: ASTRA/razvoj/testiranje/test_integracija.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Integracijski test - preveri celoten sistem
 * 
 * 🚀 ZAGON:
 *     php ASTRA/razvoj/testiranje/test_integracija.php
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 9
 * ============================================================
 */

// Nastavi poti
define('POT_KOREN', __DIR__ . '/../../..');
define('POT_RUNTIME', POT_KOREN . '/SISTEM/sistem_runtime');

echo "========================================\n";
echo "INTEGRACIJSKI TEST ASTRAMENTALICE\n";
echo "========================================\n\n";

$napake = 0;

// 1. Test bootstrapa
echo "1. TEST BOOTSTRAP\n";
echo "-----------------\n";

try {
    require_once POT_RUNTIME . '/zaganjalnik.php';
    \AstraMentalica\Runtime\zaganjalnik_pozeni();
    echo "✅ Bootstrap uspešen\n\n";
} catch (\Throwable $e) {
    echo "❌ Bootstrap napaka: " . $e->getMessage() . "\n\n";
    $napake++;
}

// 2. Test shrambe
echo "2. TEST SHRAMBE\n";
echo "---------------\n";

try {
    require_once POT_RUNTIME . '/upravljalec_baz.php';
    $test_podatki = ['id' => 'test', 'vrednost' => 'test_value'];
    \AstraMentalica\Runtime\shramba_zapisi('test/zbirka', $test_podatki);
    $prebrano = \AstraMentalica\Runtime\shramba_beri('test/zbirka');
    if (isset($prebrano[0]['vrednost']) && $prebrano[0]['vrednost'] === 'test_value') {
        echo "✅ Shramba deluje\n\n";
    } else {
        echo "❌ Shramba ne deluje\n\n";
        $napake++;
    }
} catch (\Throwable $e) {
    echo "❌ Shramba napaka: " . $e->getMessage() . "\n\n";
    $napake++;
}

// 3. Test RBAC
echo "3. TEST RBAC\n";
echo "------------\n";

try {
    require_once POT_RUNTIME . '/jedro/05_pravice.php';
    $rezultat = \AstraMentalica\Runtime\Jedro\pravice_preveri(40, 30);
    if ($rezultat === true) {
        echo "✅ RBAC deluje\n\n";
    } else {
        echo "❌ RBAC ne deluje\n\n";
        $napake++;
    }
} catch (\Throwable $e) {
    echo "❌ RBAC napaka: " . $e->getMessage() . "\n\n";
    $napake++;
}

// 4. Test modulov
echo "4. TEST MODULOV\n";
echo "---------------\n";

try {
    require_once POT_KOREN . '/SISTEM/procesi/moduli/Peskovnik.php';
    $peskovnik = new \AstraMentalica\Procesi\Moduli\Peskovnik();
    echo "✅ Modul sistem deluje\n\n";
} catch (\Throwable $e) {
    echo "❌ Modul napaka: " . $e->getMessage() . "\n\n";
    $napake++;
}

// 5. Test queue
echo "5. TEST QUEUE\n";
echo "-------------\n";

try {
    require_once POT_KOREN . '/SISTEM/administracija/avtomatika/vrsta.php';
    \AstraMentalica\Administracija\Avtomatika\vrsta_dodaj(['tip' => 'test', 'podatki' => 'test'], 'test');
    \AstraMentalica\Administracija\Avtomatika\vrsta_pocisti('test');
    echo "✅ Queue deluje\n\n";
} catch (\Throwable $e) {
    echo "❌ Queue napaka: " . $e->getMessage() . "\n\n";
    $napake++;
}

// Rezultat
echo "========================================\n";
if ($napake === 0) {
    echo "✅ VSI TESTI USPEŠNI!\n";
    echo "AstraMentalica v8.0.0 deluje pravilno.\n";
} else {
    echo "❌ TESTI NEUSPEŠNI: $napake napak\n";
}
echo "========================================\n";