<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_shinto_funkcije.php";
require_once __DIR__ . "/modul_shinto_pravila.php";
require_once __DIR__ . "/modul_shinto_jsonbaza.php";
class ModulShinto {
    private array $config;
    public function __construct() { $db = new ModulShintoJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Shinto","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Shinto","vsebina"=>"<div class=\"modul-shinto\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
