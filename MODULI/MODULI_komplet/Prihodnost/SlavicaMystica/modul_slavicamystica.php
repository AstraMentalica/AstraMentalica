<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_slavicamystica_funkcije.php";
require_once __DIR__ . "/modul_slavicamystica_pravila.php";
require_once __DIR__ . "/modul_slavicamystica_jsonbaza.php";
class ModulSlavicaMystica {
    private array $config;
    public function __construct() { $db = new ModulSlavicaMysticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"SlavicaMystica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"SlavicaMystica","vsebina"=>"<div class=\"modul-slavicamystica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
