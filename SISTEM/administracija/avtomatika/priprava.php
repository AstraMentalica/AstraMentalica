<?php
/**
 * ============================================================
 * POT: SISTEM/administracija/avtomatika/priprava.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Pripravljalnik - worker proces
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 7
 * ============================================================
 */

function priprava_worker(string \$vrsta = 'obicajna'): void
{
    echo "Worker za vrsto '\$vrsta' se začenja...\n";
    
    while (true) {
        \$paket = vrsta_vzemi(\$vrsta);
        if (\$paket === null) {
            sleep(1);
            continue;
        }
        
        try {
            \$tip = \$paket['tip'] ?? 'splosno';
            // Obdelava paketa
            echo "Obdelan paket: " . (\$paket['id'] ?? 'neznan') . "\n";
        } catch (\Throwable \$e) {
            vrsta_mrtvo(\$vrsta, \$paket, \$e->getMessage());
        }
    }
}
