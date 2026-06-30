<?php
/**
 * SENZORJI: cron.php
 * POT: MODULI/Nasa/cron.php
 *
 * Crontab:
 *   * /15 * * * * php /pot/do/MODULI/Nasa/cron.php >> /var/log/senzorji.log 2>&1
 */

declare(strict_types=1);

define('SENZORJI_CRON', true);

require_once __DIR__ . '/modul.php';

$zacetek = microtime(true);
echo "[" . date('Y-m-d H:i:s') . "] Senzorji cron: začetek\n";

try {
    $snap = senzorji_force_update();
    $cas  = round(microtime(true) - $zacetek, 2);

    echo "[" . date('Y-m-d H:i:s') . "] ✅ Snapshot posodobljen ({$cas}s)\n";
    echo "  Sonce:       " . ($snap['sonce']['status'] ?? '?') . " (jakost: " . ($snap['sonce']['jakost'] ?? 0) . ")\n";
    echo "  Kp-indeks:   " . ($snap['geomagnetno']['kp'] ?? '?') . "\n";
    echo "  Vibracija:   " . ($snap['bridge']['vibracija'] ?? '?') . "/10\n";

    $opozorila = $snap['bridge']['opozorila'] ?? [];
    if (!empty($opozorila)) {
        echo "  ⚠️  OPOZORILA:\n";
        foreach ($opozorila as $op) {
            echo "     - {$op}\n";
        }
    }
} catch (Throwable $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ❌ Napaka: " . $e->getMessage() . "\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Cron končan.\n";
