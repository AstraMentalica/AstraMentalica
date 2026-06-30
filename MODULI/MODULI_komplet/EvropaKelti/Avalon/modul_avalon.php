<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_avalon_funkcije.php";
require_once __DIR__ . "/modul_avalon_pravila.php";
require_once __DIR__ . "/modul_avalon_jsonbaza.php";
class ModulAvalon {
    private array $config;
    public function __construct() { $db = new ModulAvalonJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Avalon","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Avalon","vsebina"=>"<div class=\"modul-avalon\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
