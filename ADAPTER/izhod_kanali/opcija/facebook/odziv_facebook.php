<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/facebook/odziv_facebook.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     FACEBOOK kanal odziv
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

function odziv_facebook_poslji(array $odziv): void
{
    http_response_code($odziv['status_koda'] ?? 200);
    
    if ($odziv['status'] === 'napaka') {
        echo '<h1>Napaka</h1><p>' . htmlspecialchars($odziv['sporocilo'] ?? 'Neznana napaka') . '</p>';
        return;
    }
    
    echo '<pre>' . json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
}
