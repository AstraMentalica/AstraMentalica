<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_kresnik_funkcije.php";
require_once __DIR__ . "/modul_kresnik_pravila.php";
require_once __DIR__ . "/modul_kresnik_jsonbaza.php";
class ModulKresnik {
    private array $config;
    public function __construct() { $db = new ModulKresnikJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Kresnik","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Kresnik","vsebina"=>"<div class=\"modul-kresnik\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
