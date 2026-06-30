<?php
/**
 * MODUL: Anahuac
 * POT: MODULI/AmerikaMezoamerika/Anahuac/modul.php
 * 📅 VERZIJA: v1.0.0 (2026-06-28)
 * 📰 NAMEN: Mezoamerične skrivnosti — maya, azteka in olmeka kozmologija.
 * 👤 AVTOR: Damir Šafarič
 */
declare(strict_types=1);

$bridgePoti = [__DIR__ . '/../../Modul_Bridge/modul_bridge.php', __DIR__ . '/../Modul_Bridge/modul_bridge.php'];
$bridgeNajden = false;
foreach ($bridgePoti as $pot) { if (file_exists($pot)) { require_once $pot; $bridgeNajden = true; break; } }
if (!$bridgeNajden) { header("Content-Type: application/json"); echo json_encode(["napaka" => "Modul_Bridge ni najden"]); exit; }

if (!function_exists("odziv_uspeh")) {
    function odziv_uspeh(array $v, string $s = ""): array { return ["status"=>"uspeh","status_koda"=>200,"sporocilo"=>$s,"vsebina"=>$v]; }
    function odziv_napaka(string $s, int $k = 400): array { return ["status"=>"napaka","status_koda"=>$k,"sporocilo"=>$s,"vsebina"=>[]]; }
}

function modul_anahuac_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri("S0")) return odziv_napaka("Dostop zavrnjen", 403);
    return match($akcija) {
        "info"  => _modul_anahuac_info($podatki),
        "domov" => _modul_anahuac_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

function _modul_anahuac_info(array $p): array {
    return odziv_uspeh(["ime"=>"Anahuac","id"=>"anahuac","verzija"=>"1.0.0","opis"=>"Mezoamerične skrivnosti — maya, azteka in olmeka kozmologija.","uporabnik"=>Modul_Bridge::uporabnik_pridobi()["ime"] ?? "Gost"], "Informacije");
}
function _modul_anahuac_domov(array $p): array {
    return odziv_uspeh(["sporocilo"=>"Pozdravljen v Anahuac!","cas"=>time()], "Domov");
}

if (basename($_SERVER["SCRIPT_FILENAME"] ?? "") === "modul.php" && !defined("SISTEM_OBSTAJA")) {
    $odziv = modul_anahuac_akcija($_REQUEST["akcija"] ?? "domov", $_REQUEST);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
