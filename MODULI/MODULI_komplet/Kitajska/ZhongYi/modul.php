<?php
/**
 * MODUL: ZhongYi
 * POT: MODULI/Kitajska/ZhongYi/modul.php
 * 📅 VERZIJA: v1.0.0 (2026-06-28)
 * 📰 NAMEN: Tradicionalna kitajska medicina — meridianski sistem, akupunktura in fitoterapija.
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

function modul_zhongyi_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri("S0")) return odziv_napaka("Dostop zavrnjen", 403);
    return match($akcija) {
        "info"  => _modul_zhongyi_info($podatki),
        "domov" => _modul_zhongyi_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

function _modul_zhongyi_info(array $p): array {
    return odziv_uspeh(["ime"=>"ZhongYi","id"=>"zhongyi","verzija"=>"1.0.0","opis"=>"Tradicionalna kitajska medicina — meridianski sistem, akupunktura in fitoterapija.","uporabnik"=>Modul_Bridge::uporabnik_pridobi()["ime"] ?? "Gost"], "Informacije");
}
function _modul_zhongyi_domov(array $p): array {
    return odziv_uspeh(["sporocilo"=>"Pozdravljen v ZhongYi!","cas"=>time()], "Domov");
}

if (basename($_SERVER["SCRIPT_FILENAME"] ?? "") === "modul.php" && !defined("SISTEM_OBSTAJA")) {
    $odziv = modul_zhongyi_akcija($_REQUEST["akcija"] ?? "domov", $_REQUEST);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
