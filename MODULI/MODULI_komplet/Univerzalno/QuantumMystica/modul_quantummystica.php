<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_quantummystica_funkcije.php";
require_once __DIR__ . "/modul_quantummystica_pravila.php";
require_once __DIR__ . "/modul_quantummystica_jsonbaza.php";
class ModulQuantumMystica {
    private array $config;
    public function __construct() { $db = new ModulQuantumMysticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"QuantumMystica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"QuantumMystica","vsebina"=>"<div class=\"modul-quantummystica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
