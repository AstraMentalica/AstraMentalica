<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_heka_funkcije.php";
require_once __DIR__ . "/modul_heka_pravila.php";
require_once __DIR__ . "/modul_heka_jsonbaza.php";
class ModulHeka {
    private array $config;
    public function __construct() { $db = new ModulHekaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Heka","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Heka","vsebina"=>"<div class=\"modul-heka\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
