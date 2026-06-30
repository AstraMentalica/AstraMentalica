<?php
// testni_skript.php
// Testni skript za preverjanje delovanja

require_once 'blog_avtomatizacija.php';
require_once 'ai_komunikacija.php';
require_once 'optimizacija_tokenov.php';

echo "=== TEST BLOG AVTOMATIZACIJE ===\n";

// Inicializacija
$api_kljuc = "tvoj_openrouter_api_kljuc"; // Zamenjaj s pravim OpenRouter ključem
$tema = "Tehnologija";
$blog_sistem = new BlogAvtomatizacija($api_kljuc, $tema, 2);

// Test povezave
echo "1. Test povezave z API...\n";
$blog_sistem->nastaviAIKomunikacijo($api_kljuc);
$test_rezultat = $blog_sistem->ai_komunikacija->testirajPovezavo();
echo "Rezultat testa: " . $test_rezultat . "\n\n";

// Test optimizacije
if (strpos($test_rezultat, "uspesna") !== false) {
    echo "2. Test optimizirane generacije...\n";
    $blog_sistem->inicializirajOptimizacijoTokenov();
    
    // Test generiranja ene ideje
    $optimizacija = $blog_sistem->pridobiOptimizacijoTokenov();
    $ideje = $optimizacija->optimizirajGeneriranjeIdej($tema);
    
    echo "Generirane ideje:\n";
    foreach ($ideje as $index => $ideja) {
        echo ($index + 1) . ". " . $ideja . "\n";
    }
    
    // Test generiranja enega članka
    if (!empty($ideje)) {
        echo "\n3. Test generiranja članka...\n";
        $clanek = $optimizacija->generirajClanekPoDelih($ideje[0], $tema);
        echo "Prvi odstavek clanka: " . substr($clanek, 0, 200) . "...\n";
        
        echo "\n4. Statistika porabe:\n";
        $statistika = $optimizacija->pridobiStatistikoPorabe();
        print_r($statistika);
    }
} else {
    echo "API test ni uspel. Preveri API kljuc.\n";
}

echo "\n=== TEST ZAKLJUČEN ===\n";
?>