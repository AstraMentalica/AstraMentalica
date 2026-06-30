<?php
/**
 * MODULI/Celestara/modul_celestara_api.php
 * API adapter za Celestara blog
 */

declare(strict_types=1);

require_once __DIR__ . '/modul_celestara_baza.php';

function modul_celestara_dodaj_post(array $podatki): ?int {
    $baza = new BlogBaza();
    $naslov = $podatki['naslov'] ?? ($podatki['title'] ?? '');
    $vsebina = $podatki['vsebina'] ?? ($podatki['content'] ?? '');
    $avtorId = (int)($podatki['avtor_id'] ?? 0);
    $avtorIme = $podatki['avtor_ime'] ?? ($podatki['author'] ?? 'Anonim');

    if (empty($naslov) || empty($vsebina)) return null;

    $id = $baza->ustvariPost($naslov, $vsebina, $avtorId, $avtorIme);

    // Po potrebi dodaj v Codex
    $codexPot = __DIR__ . '/../Codex/modul_codex_api.php';
    if (file_exists($codexPot)) {
        require_once $codexPot;
        if (function_exists('modul_codex_dodaj_vnos')) {
            modul_codex_dodaj_vnos([
                'tip' => 'blog_post',
                'naslov' => $naslov,
                'vsebina' => $vsebina,
                'avtor' => $avtorIme,
                'vir' => 'Celestara',
                'source_id' => $id
            ]);
        }
    }

    return $id;
}