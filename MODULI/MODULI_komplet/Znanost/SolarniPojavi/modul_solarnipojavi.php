<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_solarnipojavi_funkcije.php";
require_once __DIR__ . "/modul_solarnipojavi_pravila.php";
require_once __DIR__ . "/modul_solarnipojavi_jsonbaza.php";
class ModulSolarniPojavi {
    private array $config;
    public function __construct() { $db = new ModulSolarniPojaviJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"SolarniPojavi","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"SolarniPojavi","vsebina"=>"<div class=\"modul-solarnipojavi\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
