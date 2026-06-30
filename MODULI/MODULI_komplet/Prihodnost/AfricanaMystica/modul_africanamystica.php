<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_africanamystica_funkcije.php";
require_once __DIR__ . "/modul_africanamystica_pravila.php";
require_once __DIR__ . "/modul_africanamystica_jsonbaza.php";
class ModulAfricanaMystica {
    private array $config;
    public function __construct() { $db = new ModulAfricanaMysticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"AfricanaMystica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"AfricanaMystica","vsebina"=>"<div class=\"modul-africanamystica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
