<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/web/postavitev.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Web postavitev
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

function web_postavitev_izrisi(array $odziv): void
{
    $vsebina = $odziv['vsebina'] ?? [];
    $sporocilo = $odziv['sporocilo'] ?? '';
    
    echo '<div class="vsebina">';
    if (!empty($sporocilo)) {
        echo '<div class="obvestilo">' . htmlspecialchars($sporocilo) . '</div>';
    }
    
    if (isset($vsebina['html'])) {
        echo $vsebina['html'];
    } elseif (isset($vsebina['sporocilo'])) {
        echo '<p>' . htmlspecialchars($vsebina['sporocilo']) . '</p>';
    }
    echo '</div>';
}
