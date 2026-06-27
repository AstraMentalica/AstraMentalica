<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/administracija/avtomatika/opravila.php
 * v111 (27.5.2026 07:30)
 * ---------------------------------------------------------
 * OPIS: Definicije opravil (jobov) za čakalno vrsto
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/runtime/vrsta/vrsta_odprava.php
 *
 * UPORABA:
 * - SISTEM/administracija/avtomatika/obdelava.php
 *
 * FUNKCIJE:
 * - opravila_registriraj_vsa() – registracija vseh opravil
 * - opravilo_poslji_email(), opravilo_poslji_notifikacijo()
 * - opravilo_nalozi_modul(), opravilo_posodobi_cache()
 * - opravilo_izvedi_cron_job(), opravilo_analiziraj_aktivnosti()
 * - opravilo_varnostna_kopija(), opravilo_poslji_webhook()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 8 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function opravilo_poslji_email(array $podatki): void
{
$to = $podatki['to'] ?? '';
$subject = $podatki['subject'] ?? '';
$body = $podatki['body'] ?? '';
$headers = $podatki['headers'] ?? "Content-Type: text/html; charset=UTF-8\r\n";

if (empty($to) || empty($subject)) {
    dnevnik_opozorilo("Email opravilo: manjkajo obvezni podatki", $podatki);
    return;
}

if (function_exists('mail')) {
    $poslano = mail($to, $subject, $body, $headers);
    if ($poslano) {
        dnevnik_info("Email poslan na $to: $subject");
    } else {
        dnevnik_napaka("Email ni bil poslan na $to: $subject");
    }
} else {
    dnevnik_opozorilo("Mail funkcija ni na voljo", $podatki);
}
}

function opravilo_poslji_notifikacijo(array $podatki): void
{
$uporabnikId = $podatki['uporabnik_id'] ?? 0;
$sporocilo = $podatki['sporocilo'] ?? '';
$tip = $podatki['tip'] ?? 'info';
$povezava = $podatki['povezava'] ?? null;

if (empty($sporocilo)) {
    return;
}

// Shrani notifikacijo v bazo
$notifikacija = [
    'id' => uniqid('notif_', true),
    'uporabnik_id' => $uporabnikId,
    'sporocilo' => $sporocilo,
    'tip' => $tip,
    'povezava' => $povezava,
    'prebrano' => false,
    'ustvarjeno' => time()
];

if (function_exists('baza_zapisi')) {
    baza_zapisi('notifikacije', $notifikacija);
}

dnevnik_info("Notifikacija poslana uporabniku $uporabnikId: $sporocilo");
}

function opravilo_nalozi_modul(array $podatki): void
{
$imeModula = $podatki['ime_modula'] ?? '';
$akcija = $podatki['akcija'] ?? 'aktiviraj';

if (empty($imeModula)) {
    return;
}

if (function_exists('modul_peskovnik_nalozi')) {
    $modul = modul_peskovnik_nalozi($imeModula);
    if ($modul !== null) {
        if ($akcija === 'aktiviraj' && function_exists('modul_peskovnik_aktiviraj')) {
            modul_peskovnik_aktiviraj($imeModula);
            dnevnik_info("Modul $imeModula aktiviran preko opravila");
        } elseif ($akcija === 'deaktiviraj' && function_exists('modul_peskovnik_deaktiviraj')) {
            modul_peskovnik_deaktiviraj($imeModula);
            dnevnik_info("Modul $imeModula deaktiviran preko opravila");
        }
    } else {
        dnevnik_opozorilo("Modul $imeModula ni bil najden", $podatki);
    }
}
}

function opravilo_posodobi_cache(array $podatki): void
{
$skupina = $podatki['skupina'] ?? '';
$kljuc = $podatki['kljuc'] ?? '';
$vrednost = $podatki['vrednost'] ?? null;
$casZivljenja = $podatki['cas_zivljenja'] ?? 3600;

if (empty($skupina) || empty($kljuc) || $vrednost === null) {
    return;
}

if (function_exists('cache_shrani')) {
    cache_shrani($skupina . '_' . $kljuc, $vrednost, $casZivljenja);
    dnevnik_info("Cache posodobljen: $skupina/$kljuc");
}
}

function opravilo_izvedi_cron_job(array $podatki): void
{
$imeJoba = $podatki['ime_joba'] ?? '';

if (empty($imeJoba)) {
    return;
}

if (function_exists('cron_izvedi_dozorele')) {
    cron_izvedi_dozorele();
    dnevnik_info("Cron job $imeJoba izveden preko opravila");
}
}

function opravilo_analiziraj_aktivnosti(array $podatki): void
{
$uporabnikId = $podatki['uporabnik_id'] ?? 0;
$obdobje = $podatki['obdobje'] ?? 7; // dni

if ($uporabnikId === 0) {
    return;
}

if (function_exists('uporabniki_aktivnosti_zadnje')) {
    $aktivnosti = uporabniki_aktivnosti_zadnje($uporabnikId, 100);
    
    // Pošlji tedenski povzetek
    $stevilo = count($aktivnosti);
    $tipi = [];
    foreach ($aktivnosti as $aktivnost) {
        $tip = $aktivnost['tip'] ?? 'unknown';
        $tipi[$tip] = ($tipi[$tip] ?? 0) + 1;
    }
    
    dnevnik_info("Analiziranih $stevilo aktivnosti za uporabnika $uporabnikId", ['tipi' => $tipi]);
}
}

function opravilo_varnostna_kopija(array $podatki): void
{
$tip = $podatki['tip'] ?? 'baza';
$lokacija = $podatki['lokacija'] ?? POT_PODATKI_SKLADISCE . '/varnostne_kopije';

if (!is_dir($lokacija)) {
    mkdir($lokacija, 0755, true);
}

$imeDatoteke = $lokacija . '/backup_' . $tip . '_' . date('Ymd_His') . '.json';

if ($tip === 'baza') {
    // Shrani vse podatke iz baz
    $zbirke = ['globalno_nastavitve', 'globalno_navigacija', 'moduli', 'uporabniki'];
    $podatkiZaBackup = [];
    
    foreach ($zbirke as $zbirka) {
        if (function_exists('baza_beri')) {
            $podatkiZaBackup[$zbirka] = baza_beri($zbirka);
        }
    }
    
    file_put_contents($imeDatoteke, json_encode($podatkiZaBackup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    dnevnik_info("Varnostna kopija baze ustvarjena: " . basename($imeDatoteke));
}
}

function opravilo_poslji_webhook(array $podatki): void
{
$url = $podatki['url'] ?? '';
$podatkiZaPoslati = $podatki['podatki'] ?? [];
$metoda = $podatki['metoda'] ?? 'POST';

if (empty($url)) {
    return;
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metoda);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($podatkiZaPoslati));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$odgovor = curl_exec($ch);
$httpKoda = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpKoda >= 200 && $httpKoda < 300) {
    dnevnik_info("Webhook poslan na $url: HTTP $httpKoda");
} else {
    dnevnik_opozorilo("Webhook na $url ni uspel: HTTP $httpKoda", ['odgovor' => $odgovor]);
}
}

function opravila_registriraj_vsa(): void
{
if (!function_exists('vrsta_odprava_registriraj')) {
    return;
}

vrsta_odprava_registriraj('email', 'opravilo_poslji_email', 'elektronska_posta');
vrsta_odprava_registriraj('notifikacija', 'opravilo_poslji_notifikacijo', 'obvestila');
vrsta_odprava_registriraj('modul_aktivacija', 'opravilo_nalozi_modul', 'visoka_prednost');
vrsta_odprava_registriraj('cache_posodobitev', 'opravilo_posodobi_cache', 'nizka_prednost');
vrsta_odprava_registriraj('cron_job', 'opravilo_izvedi_cron_job', 'casovnik');
vrsta_odprava_registriraj('analitika', 'opravilo_analiziraj_aktivnosti', 'nizka_prednost');
vrsta_odprava_registriraj('varnostna_kopija', 'opravilo_varnostna_kopija', 'nizka_prednost');
vrsta_odprava_registriraj('webhook', 'opravilo_poslji_webhook', 'sprotno');

dnevnik_info("Vsa opravila registrirana");
}