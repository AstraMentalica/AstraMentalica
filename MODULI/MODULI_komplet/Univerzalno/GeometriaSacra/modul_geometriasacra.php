<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_geometriasacra_funkcije.php";
require_once __DIR__ . "/modul_geometriasacra_pravila.php";
require_once __DIR__ . "/modul_geometriasacra_jsonbaza.php";
class ModulGeometriaSacra {
    private array $config;
    public function __construct() { $db = new ModulGeometriaSacraJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"GeometriaSacra","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"GeometriaSacra","vsebina"=>"<div class=\"modul-geometriasacra\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
