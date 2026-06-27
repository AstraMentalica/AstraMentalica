<?php
/**
 * ============================================================
 * POT: SISTEM/sistem_runtime/knjiznice/queue.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Proceduralne funkcije za delo s čakalno vrsto
 * 
 * 🔧 FUNKCIJE:
 *     - queue_dodaj(array $paket, string $vrsta = 'obicajna_prednost'): bool
 *     - queue_vzemi(string $vrsta = 'obicajna_prednost'): ?array
 *     - queue_stevilo(string $vrsta = 'obicajna_prednost'): int
 *     - queue_ponovno_poskusi(array $paket, int $zakasnitev = 5): bool
 *     - queue_mrtvo(string $vrsta, array $paket, string $razlog): void
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 7
 * ============================================================
 */

use AstraMentalica\Runtime\Queue\CakalnaVrsta;

$GLOBALS['QUEUE_INSTANCA'] = null;

function queue_instanca(): CakalnaVrsta
{
    if ($GLOBALS['QUEUE_INSTANCA'] === null) {
        $GLOBALS['QUEUE_INSTANCA'] = new CakalnaVrsta();
    }
    return $GLOBALS['QUEUE_INSTANCA'];
}

function queue_dodaj(array $paket, string $vrsta = 'obicajna_prednost'): bool
{
    return queue_instanca()->dodaj($paket, $vrsta);
}

function queue_vzemi(string $vrsta = 'obicajna_prednost'): ?array
{
    return queue_instanca()->vzemi($vrsta);
}

function queue_stevilo(string $vrsta = 'obicajna_prednost'): int
{
    return queue_instanca()->stevilo($vrsta);
}

function queue_ponovno_poskusi(array $paket, int $zakasnitev = 5): bool
{
    return queue_instanca()->ponovno_poskusi($paket, $zakasnitev);
}

function queue_mrtvo(string $vrsta, array $paket, string $razlog): void
{
    queue_instanca()->mrtvo($vrsta, $paket, $razlog);
}

function queue_pocisti(string $vrsta): void
{
    queue_instanca()->pocisti($vrsta);
}

function queue_vsi(string $vrsta): array
{
    return queue_instanca()->vsi($vrsta);
}

function queue_obdelajVse(string $vrsta, callable $obdelovalec): void
{
    queue_instanca()->obdelajVse($vrsta, $obdelovalec);
}