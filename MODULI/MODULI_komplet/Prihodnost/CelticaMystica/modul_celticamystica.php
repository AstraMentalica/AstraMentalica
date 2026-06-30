<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_celticamystica_funkcije.php";
require_once __DIR__ . "/modul_celticamystica_pravila.php";
require_once __DIR__ . "/modul_celticamystica_jsonbaza.php";
class ModulCelticaMystica {
    private array $config;
    public function __construct() { $db = new ModulCelticaMysticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"CelticaMystica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"CelticaMystica","vsebina"=>"<div class=\"modul-celticamystica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
