<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_ayurveda_funkcije.php";
require_once __DIR__ . "/modul_ayurveda_pravila.php";
require_once __DIR__ . "/modul_ayurveda_jsonbaza.php";
class ModulAyurveda {
    private array $config;
    public function __construct() { $db = new ModulAyurvedaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Ayurveda","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Ayurveda","vsebina"=>"<div class=\"modul-ayurveda\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
