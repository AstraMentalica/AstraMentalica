<?php
/**
 * Ustvari demo vsebino: forum temo, forum objavo, blog post, codex vnos
 * POT: MODULI/setup_demo_content.php
 */

declare(strict_types=1);

require_once __DIR__ . '/Aetheris/modul_aetheris_baza.php';
require_once __DIR__ . '/Celestara/modul_celestara_baza.php';
require_once __DIR__ . '/Codex/modul_codex_api.php';

$fb = new ForumBaza();
$id1 = $fb->ustvariTemo('Pozdrav Aetheris', 'Dobrodošli na forumu Aetheris', 1, 'Admin');
$fb->dodajObjavo($id1, 'Hvala za prispevek!', 1, 'Admin');

$cb = new BlogBaza();
$p = $cb->ustvariPost('Celestara: prvi zapis', 'Zvezde in lunini cikli...', 1, 'Astra');

if (function_exists('modul_codex_dodaj_vnos')) {
    modul_codex_dodaj_vnos([
        'tip' => 'blog_post',
        'naslov' => 'Celestara: prvi zapis',
        'vsebina' => 'Zvezde in lunini cikli...',
        'avtor' => 'Astra',
        'vir' => 'Celestara',
        'source_id' => $p
    ]);
}

echo "CREATED\n";