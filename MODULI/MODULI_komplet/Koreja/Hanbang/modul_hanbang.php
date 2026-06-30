<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_hanbang_funkcije.php";
require_once __DIR__ . "/modul_hanbang_pravila.php";
require_once __DIR__ . "/modul_hanbang_jsonbaza.php";
class ModulHanbang {
    private array $config;
    public function __construct() { $db = new ModulHanbangJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Hanbang","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Hanbang","vsebina"=>"<div class=\"modul-hanbang\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
