<?php
declare(strict_types=1);
/**
 * SISTEM/uporabniki_logika/nastavitve_logika.php – NOVA DATOTEKA
 * AstraMentalica v6.1.1
 * Poti premaknjene iz prijava.php (kršitev SRP).
 */
defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function nastavitev_shrani(array $p): array {
    if (empty($_SESSION['uporabnik_id'])) return ['status'=>'error','napaka'=>'Nisi prijavljen.'];
    $kljuc    = trim($p['kljuc'] ?? '');
    $vrednost = $p['vrednost'] ?? '';
    if (!$kljuc) return ['status'=>'error','napaka'=>'Manjka ključ.'];
    if (!in_array($kljuc, ['tema','layout','velikost','prikazi_profil','jezik'], true)) {
        return ['status'=>'error','napaka'=>'Nedovoljen ključ: ' . $kljuc];
    }
    if (!isset($_SESSION['nastavitve'])) $_SESSION['nastavitve'] = [];
    $_SESSION['nastavitve'][$kljuc] = $vrednost;
    $id  = (int)$_SESSION['uporabnik_id'];
    $dat = UPORABNIKI . '/' . $id . '/nastavitve.json';
    $nav = file_exists($dat) ? (json_decode(file_get_contents($dat), true) ?? []) : [];
    $nav[$kljuc] = $vrednost;
    file_put_contents($dat, json_encode($nav, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX);
    return ['status'=>'success'];
}

registriraj_pot('shrani_nastavitev', fn(array $p): array => nastavitev_shrani($p));
registriraj_pot('nastavi_temo', function(array $p): array {
    tema_nastavi($p['tema'] ?? 'temna');
    return ['status'=>'success'];
});