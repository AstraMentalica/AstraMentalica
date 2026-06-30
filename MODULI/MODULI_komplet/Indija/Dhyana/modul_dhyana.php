<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_dhyana_funkcije.php";
require_once __DIR__ . "/modul_dhyana_pravila.php";
require_once __DIR__ . "/modul_dhyana_jsonbaza.php";
class ModulDhyana {
    private array $config;
    public function __construct() { $db = new ModulDhyanaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Dhyana","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Dhyana","vsebina"=>"<div class=\"modul-dhyana\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
