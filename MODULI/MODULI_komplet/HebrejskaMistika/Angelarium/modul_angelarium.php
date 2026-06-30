<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_angelarium_funkcije.php";
require_once __DIR__ . "/modul_angelarium_pravila.php";
require_once __DIR__ . "/modul_angelarium_jsonbaza.php";
class ModulAngelarium {
    private array $config;
    public function __construct() { $db = new ModulAngelariumJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Angelarium","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Angelarium","vsebina"=>"<div class=\"modul-angelarium\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
