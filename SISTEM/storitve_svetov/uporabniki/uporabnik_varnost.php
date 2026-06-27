<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_varnost.php
 * v111 (27.5.2026 16:30)
 * ---------------------------------------------------------
 * OPIS: Varnost uporabnikov – 2FA, seje, blokiranje
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - SISTEM/storitve_svetov/uporabniki/uporabniki_seja.php
 *
 * FUNKCIJE:
 * - uporabniki_varnost_2fa_omogoci(), uporabniki_varnost_2fa_preveri()
 * - uporabniki_varnost_blokiraj(), uporabniki_varnost_odblokiraj()
 * - uporabniki_varnost_seje_pridobi(), uporabniki_varnost_seje_preklici()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 46 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function uporabniki_varnost_2fa_omogoci(string $uporabnikId): array
{
// Generiraj secret za TOTP
$secret = bin2hex(random_bytes(20));
$backupCodes = [];
for ($i = 0; $i < 10; $i++) {
    $backupCodes[] = bin2hex(random_bytes(4));
}

baza_posodobi('uporabniki', $uporabnikId, [
    '2fa_omogocen' => true,
    '2fa_secret' => $secret,
    '2fa_backup_codes' => json_encode($backupCodes),
    '2fa_nastavljen' => time()
]);

// Generiraj QR kodo URL (otpauth://totp/...)
$email = baza_beri_enega('uporabniki', $uporabnikId)['elektronski_naslov'] ?? '';
$qrUrl = "otpauth://totp/" . urlencode(IME_APLIKACIJE . ":$email") . "?secret=$secret&issuer=" . urlencode(IME_APLIKACIJE);

return [
    'secret' => $secret,
    'backup_codes' => $backupCodes,
    'qr_url' => $qrUrl
];
}

function uporabniki_varnost_2fa_preveri(string $uporabnikId, string $koda): bool
{
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);
if (!$uporabnik['2fa_omogocen']) {
    return true; // 2FA ni omogočen
}

$secret = $uporabnik['2fa_secret'];
$backupCodes = json_decode($uporabnik['2fa_backup_codes'] ?? '[]', true);

// Preveri backup kode
if (in_array($koda, $backupCodes)) {
    // Odstrani uporabljeno backup kodo
    $backupCodes = array_filter($backupCodes, fn($c) => $c !== $koda);
    baza_posodobi('uporabniki', $uporabnikId, ['2fa_backup_codes' => json_encode(array_values($backupCodes))]);
    return true;
}

// Preveri TOTP kodo (preprosta implementacija – v produkciji uporabi knjižnico)
$expected = hash_hmac('sha1', floor(time() / 30), hex2bin($secret));
$code = substr($expected, -6);

return $koda === $code;
}

function uporabniki_varnost_2fa_onemogoci(string $uporabnikId): bool
{
return baza_posodobi('uporabniki', $uporabnikId, [
    '2fa_omogocen' => false,
    '2fa_secret' => null,
    '2fa_backup_codes' => null
]);
}

function uporabniki_varnost_blokiraj(string $uporabnikId, string $razlog, ?int $trajanje = null): bool
{
$blokada = [
    'blokiran' => true,
    'razlog_blokade' => $razlog,
    'blokiran_od' => time(),
    'blokiran_do' => $trajanje ? time() + $trajanje : null
];

// Prekliči vse aktivne seje
uporabniki_varnost_seje_preklici_vse($uporabnikId);

// Zabeleži v dnevnik
if (function_exists('varnostni_dnevnik_zabelezi')) {
    varnostni_dnevnik_zabelezi('blokada', "Uporabnik $uporabnikId blokiran", $blokada);
}

return baza_posodobi('uporabniki', $uporabnikId, $blokada);
}

function uporabniki_varnost_odblokiraj(string $uporabnikId): bool
{
return baza_posodobi('uporabniki', $uporabnikId, [
    'blokiran' => false,
    'razlog_blokade' => null,
    'blokiran_od' => null,
    'blokiran_do' => null
]);
}

function uporabniki_varnost_je_blokiran(string $uporabnikId): bool
{
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);
if (!$uporabnik || !$uporabnik['blokiran']) {
    return false;
}

// Preveri časovno omejitev
if ($uporabnik['blokiran_do'] !== null && $uporabnik['blokiran_do'] < time()) {
    uporabniki_varnost_odblokiraj($uporabnikId);
    return false;
}

return true;
}

function uporabniki_varnost_seje_pridobi(string $uporabnikId): array
{
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);
$seje = json_decode($uporabnik['aktivne_seje'] ?? '[]', true);
return $seje;
}

function uporabniki_varnost_seje_dodaj(string $uporabnikId, string $sessionId, string $ip, string $userAgent): void
{
$seje = uporabniki_varnost_seje_pridobi($uporabnikId);

// Omeji število aktivnih sej na 5
if (count($seje) >= 5) {
    array_shift($seje);
}

$seje[] = [
    'session_id' => $sessionId,
    'ip' => $ip,
    'user_agent' => $userAgent,
    'prijavljen' => time(),
    'zadnja_aktivnost' => time()
];

baza_posodobi('uporabniki', $uporabnikId, ['aktivne_seje' => json_encode($seje)]);
}

function uporabniki_varnost_seje_preklici(string $uporabnikId, string $sessionId): bool
{
$seje = uporabniki_varnost_seje_pridobi($uporabnikId);
$noveSeje = array_filter($seje, fn($s) => $s['session_id'] !== $sessionId);

return baza_posodobi('uporabniki', $uporabnikId, ['aktivne_seje' => json_encode(array_values($noveSeje))]);
}

function uporabniki_varnost_seje_preklici_vse(string $uporabnikId): bool
{
return baza_posodobi('uporabniki', $uporabnikId, ['aktivne_seje' => json_encode([])]);
}