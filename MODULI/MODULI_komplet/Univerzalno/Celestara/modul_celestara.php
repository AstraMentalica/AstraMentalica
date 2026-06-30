<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_celestara_funkcije.php";
require_once __DIR__ . "/modul_celestara_pravila.php";
require_once __DIR__ . "/modul_celestara_jsonbaza.php";
class ModulCelestara {
    private array $config;
    public function __construct() { $db = new ModulCelestaraJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Celestara","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Celestara","vsebina"=>"<div class=\"modul-celestara\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
