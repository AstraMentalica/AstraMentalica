<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_cosmicascientia_funkcije.php";
require_once __DIR__ . "/modul_cosmicascientia_pravila.php";
require_once __DIR__ . "/modul_cosmicascientia_jsonbaza.php";
class ModulCosmicaScientia {
    private array $config;
    public function __construct() { $db = new ModulCosmicaScientiaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"CosmicaScientia","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"CosmicaScientia","vsebina"=>"<div class=\"modul-cosmicascientia\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
