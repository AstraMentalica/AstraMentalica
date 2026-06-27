<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_nastavitve.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Upravljanje uporabniških nastavitev.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - uporabniki_nastavitve_pridobi(string $uporabnikId): array
 *     - uporabniki_nastavitve_posodobi(string $uporabnikId, array $nastavitve): array
 *     - uporabniki_nastavitve_kljuc(string $uporabnikId, string $kljuc, $privzeto)
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php
 *     - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, uporabniki, nastavitve
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function uporabniki_nastavitve_pridobi(string $uporabnikId): array
{
    $pot = POT_UPORABNIKI . '/' . $uporabnikId . '/nastavitve.json';
    
    if (!file_exists($pot)) {
        return [];
    }
    
    $vsebina = file_get_contents($pot);
    return json_decode($vsebina, true) ?? [];
}

function uporabniki_nastavitve_posodobi(string $uporabnikId, array $nastavitve): array
{
    $mapa = POT_UPORABNIKI . '/' . $uporabnikId;
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    
    $pot = $mapa . '/nastavitve.json';
    $obstojece = [];
    
    if (file_exists($pot)) {
        $obstojece = json_decode(file_get_contents($pot), true) ?? [];
    }
    
    $nove = array_merge($obstojece, $nastavitve);
    $nove['zadnja_posodobitev'] = time();
    
    $uspeh = file_put_contents($pot, json_encode($nove, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    
    if ($uspeh === false) {
        return [
            'status' => 'napaka',
            'status_koda' => 500,
            'sporocilo' => 'Napaka pri shranjevanju nastavitev.'
        ];
    }
    
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'sporocilo' => 'Nastavitve shranjene.'
    ];
}

function uporabniki_nastavitve_kljuc(string $uporabnikId, string $kljuc, $privzeto = null)
{
    $nastavitve = uporabniki_nastavitve_pridobi($uporabnikId);
    return $nastavitve[$kljuc] ?? $privzeto;
}