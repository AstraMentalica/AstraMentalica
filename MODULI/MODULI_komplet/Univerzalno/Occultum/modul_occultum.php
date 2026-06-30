<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_occultum_funkcije.php";
require_once __DIR__ . "/modul_occultum_pravila.php";
require_once __DIR__ . "/modul_occultum_jsonbaza.php";
class ModulOccultum {
    private array $config;
    public function __construct() { $db = new ModulOccultumJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Occultum","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Occultum","vsebina"=>"<div class=\"modul-occultum\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
