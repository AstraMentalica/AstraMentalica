<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/dogodki.php
 * v100 (10.06.2026)
 * ---------------------------------------------------------
 * OPIS: Preprost event sistem – sprozi in poslušaj dogodke
 * ---------------------------------------------------------
 * FUNKCIJE:
 *     dogodek_sprozi(ime, podatki)
 *     dogodek_poslusalec_registriraj(ime, callback, prioriteta)
 * ---------------------------------------------------------
 */
declare(strict_types=1);

$_DOGODKI_POSLUSALCI = [];

function dogodek_poslusalec_registriraj(string $ime, callable $callback, int $prioriteta = 10): void
{
    global $_DOGODKI_POSLUSALCI;
    $_DOGODKI_POSLUSALCI[$ime][] = [
        'callback'   => $callback,
        'prioriteta' => $prioriteta
    ];
    // Uredi po prioriteti (manjša = prej)
    usort($_DOGODKI_POSLUSALCI[$ime], fn($a, $b) => $a['prioriteta'] <=> $b['prioriteta']);
}

function dogodek_sprozi(string $ime, array $podatki = []): void
{
    global $_DOGODKI_POSLUSALCI;
    if (empty($_DOGODKI_POSLUSALCI[$ime])) return;

    foreach ($_DOGODKI_POSLUSALCI[$ime] as $poslusalec) {
        try {
            ($poslusalec['callback'])($podatki);
        } catch (Throwable $e) {
            // Napaka v poslušalcu ne sme ustaviti toka
            error_log('Napaka v poslušalcu [' . $ime . ']: ' . $e->getMessage());
        }
    }
}