<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/embed/mini_baza.php
 * 📅 VERZIJA: v114 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / EMBED
 *
 * 📰 NAMEN:
 *     Demo baza za Bridge — JSON v $_SESSION.
 *     Posnema vmesnik pravega upravljalec_baz.php.
 *     Ne piše v PODATKI/ (sandbox delovanje).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - mini_baza_beri(string $tabela): array
 *     - mini_baza_zapisi(string $tabela, array $podatki): bool
 *     - mini_baza_dodaj(string $tabela, array $vrstica): int
 *     - mini_baza_poisci(string $tabela, string $polje, mixed $vrednost): array
 *     - mini_baza_brisi(string $tabela, int $id): bool
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, embed, baza, demo
 * ============================================================
 */

declare(strict_types=1);

function mini_baza_beri(string $tabela): array {
    return $_SESSION['mini_baza'][$tabela] ?? [];
}

function mini_baza_zapisi(string $tabela, array $podatki): bool {
    $_SESSION['mini_baza'][$tabela] = $podatki;
    return true;
}

function mini_baza_dodaj(string $tabela, array $vrstica): int {
    if (!isset($_SESSION['mini_baza'][$tabela])) {
        $_SESSION['mini_baza'][$tabela] = [];
    }

    $id = count($_SESSION['mini_baza'][$tabela]) + 1;
    $vrstica['id'] = $id;
    $_SESSION['mini_baza'][$tabela][] = $vrstica;

    return $id;
}

function mini_baza_poisci(string $tabela, string $polje, mixed $vrednost): array {
    $vse = mini_baza_beri($tabela);
    return array_values(array_filter($vse, fn($v) => ($v[$polje] ?? null) === $vrednost));
}

function mini_baza_brisi(string $tabela, int $id): bool {
    if (!isset($_SESSION['mini_baza'][$tabela])) {
        return false;
    }

    foreach ($_SESSION['mini_baza'][$tabela] as $kljuc => $vrstica) {
        if (($vrstica['id'] ?? null) === $id) {
            unset($_SESSION['mini_baza'][$tabela][$kljuc]);
            $_SESSION['mini_baza'][$tabela] = array_values($_SESSION['mini_baza'][$tabela]);
            return true;
        }
    }

    return false;
}
