<?php
/**
 * mystaia_api.php – AJAX endpoint za Mystaia
 * Kliče se prek /?svet=MODULI&pot=premium/Mystaia&api=1
 * Obdeluje POST akcije: dodaj_v_kosarico, odstrani, narocilo, admin_*
 */

defined('MYSTAIA_ACTIVE') or die('Direkten dostop ni dovoljen.');

header('Content-Type: application/json; charset=utf-8');

$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true) ?? [];
$akcija = $input['akcija'] ?? ($_GET['akcija'] ?? '');

// ── PREVERI VLOGO – samo za admin akcije prek bridgea ────────────────────────
function mystaia_je_admin(): bool {
    $vloge = ['S1'=>20,'S2'=>30,'S3'=>40,'S4'=>50,'S5'=>60,'admin'=>100];
    $vloga = $_SESSION['vloga'] ?? 'gost';
    return ($vloge[$vloga] ?? 0) >= 20;
}

function api_ok(mixed $data): void  { echo json_encode(['ok'=>true, 'data'=>$data],   JSON_UNESCAPED_UNICODE); exit; }
function api_err(string $msg): void { echo json_encode(['ok'=>false,'napaka'=>$msg], JSON_UNESCAPED_UNICODE); exit; }

switch ($akcija) {

    // ── KOŠARICA ──────────────────────────────────────────────────────────────
    case 'dodaj':
        $rez = mystaia_kosarica_dodaj($input['id'] ?? '', (int)($input['kolicina'] ?? 1));
        $rez['ok'] ? api_ok($rez) : api_err($rez['napaka']);
        break;

    case 'odstrani':
        mystaia_kosarica_odstrani($input['id'] ?? '');
        api_ok(mystaia_kosarica_skupaj());
        break;

    case 'kolicina':
        mystaia_kosarica_kolicina($input['id'] ?? '', (int)($input['kolicina'] ?? 1));
        api_ok(mystaia_kosarica_skupaj());
        break;

    case 'kosarica':
        api_ok(mystaia_kosarica_skupaj());
        break;

    // ── NAROČILO ──────────────────────────────────────────────────────────────
    case 'narocilo':
        $rez = mystaia_ustvari_narocilo($input);
        $rez['ok'] ? api_ok($rez) : api_err($rez['napaka']);
        break;

    // ── ADMIN ──────────────────────────────────────────────────────────────────
    case 'admin_status':
        if (!mystaia_je_admin()) api_err('Nezadostne pravice');
        mystaia_narocilo_status($input['id'] ?? '', $input['status'] ?? '');
        api_ok(true);
        break;

    case 'admin_shrani_artikel':
        if (!mystaia_je_admin()) api_err('Nezadostne pravice');
        $a = $input['artikel'] ?? [];
        if (empty($a['id']) || empty($a['ime'])) api_err('Nepopolni podatki');
        $a['aktivno'] = (bool)($a['aktivno'] ?? true);
        $a['cena']    = (float)($a['cena'] ?? 0);
        $a['zalogo']  = (int)($a['zalogo'] ?? 0);
        mystaia_shrani_artikel($a);
        api_ok(true);
        break;

    case 'admin_brisi_artikel':
        if (!mystaia_je_admin()) api_err('Nezadostne pravice');
        mystaia_brisi_artikel($input['id'] ?? '');
        api_ok(true);
        break;

    default:
        api_err('Neznana akcija: ' . htmlspecialchars($akcija));
}
