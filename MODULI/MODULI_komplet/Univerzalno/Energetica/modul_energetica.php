<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_energetica_funkcije.php";
require_once __DIR__ . "/modul_energetica_pravila.php";
require_once __DIR__ . "/modul_energetica_jsonbaza.php";
class ModulEnergetica {
    private array $config;
    public function __construct() { $db = new ModulEnergeticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Energetica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Energetica","vsebina"=>"<div class=\"modul-energetica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
