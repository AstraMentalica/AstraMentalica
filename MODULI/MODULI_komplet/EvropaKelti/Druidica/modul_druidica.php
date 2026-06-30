<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_druidica_funkcije.php";
require_once __DIR__ . "/modul_druidica_pravila.php";
require_once __DIR__ . "/modul_druidica_jsonbaza.php";
class ModulDruidica {
    private array $config;
    public function __construct() { $db = new ModulDruidicaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Druidica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Druidica","vsebina"=>"<div class=\"modul-druidica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
