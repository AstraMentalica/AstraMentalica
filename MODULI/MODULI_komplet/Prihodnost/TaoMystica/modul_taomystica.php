<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_taomystica_funkcije.php";
require_once __DIR__ . "/modul_taomystica_pravila.php";
require_once __DIR__ . "/modul_taomystica_jsonbaza.php";
class ModulTaoMystica {
    private array $config;
    public function __construct() { $db = new ModulTaoMysticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"TaoMystica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"TaoMystica","vsebina"=>"<div class=\"modul-taomystica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
