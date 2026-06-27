<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/api/odziv_api.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     API kanal odziv
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

function odziv_api_poslji(array $odziv): void
{
    http_response_code($odziv['status_koda'] ?? 200);
    
    if ($odziv['status'] === 'napaka') {
        echo '<h1>Napaka</h1><p>' . htmlspecialchars($odziv['sporocilo'] ?? 'Neznana napaka') . '</p>';
        return;
    }
    
    echo '<pre>' . json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
}
