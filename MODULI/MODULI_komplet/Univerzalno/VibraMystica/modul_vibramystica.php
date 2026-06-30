<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_vibramystica_funkcije.php";
require_once __DIR__ . "/modul_vibramystica_pravila.php";
require_once __DIR__ . "/modul_vibramystica_jsonbaza.php";
class ModulVibraMystica {
    private array $config;
    public function __construct() { $db = new ModulVibraMysticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"VibraMystica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"VibraMystica","vsebina"=>"<div class=\"modul-vibramystica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
