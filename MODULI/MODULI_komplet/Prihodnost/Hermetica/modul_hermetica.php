<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_hermetica_funkcije.php";
require_once __DIR__ . "/modul_hermetica_pravila.php";
require_once __DIR__ . "/modul_hermetica_jsonbaza.php";
class ModulHermetica {
    private array $config;
    public function __construct() { $db = new ModulHermeticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Hermetica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Hermetica","vsebina"=>"<div class=\"modul-hermetica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
