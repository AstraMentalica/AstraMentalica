<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/web/render.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Renderiranje HTML strani
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

function kanal_web_render(array $odziv): void
{
    $tip = $odziv['tip'] ?? 'domov';
    $vsebina = $odziv['vsebina'] ?? [];
    $sporocilo = $odziv['sporocilo'] ?? '';
    
    // Glava
    require_once __DIR__ . '/glava.php';
    kanal_web_glava($tip);
    
    // Navigacija
    require_once __DIR__ . '/../postavitev.php';
    kanal_web_navigacija();
    
    // Vsebina
    echo '<div class="vsebina">';
    if (!empty($sporocilo)) {
        echo '<div class="obvestilo">' . htmlspecialchars($sporocilo) . '</div>';
    }
    
    // Izpis vsebine (odvisno od tipa)
    switch ($tip) {
        case 'domov':
            echo '<h1>Dobrodošli v AstraMentalica</h1>';
            echo '<p>Večsvetovni runtime sistem za duhovni razvoj.</p>';
            break;
        case 'uporabniki':
            echo '<h1>Uporabniški svet</h1>';
            break;
        case 'modul':
            if (isset($vsebina['html'])) {
                echo $vsebina['html'];
            }
            break;
        default:
            echo '<pre>' . htmlspecialchars(print_r($vsebina, true)) . '</pre>';
    }
    echo '</div>';
    
    // Noga
    require_once __DIR__ . '/noga.php';
    kanal_web_noga();
}