<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_runir_funkcije.php";
require_once __DIR__ . "/modul_runir_pravila.php";
require_once __DIR__ . "/modul_runir_jsonbaza.php";
class ModulRunir {
    private array $config;
    public function __construct() { $db = new ModulRunirJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Runir","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Runir","vsebina"=>"<div class=\"modul-runir\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
