<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_sufica_funkcije.php";
require_once __DIR__ . "/modul_sufica_pravila.php";
require_once __DIR__ . "/modul_sufica_jsonbaza.php";
class ModulSufica {
    private array $config;
    public function __construct() { $db = new ModulSuficaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Sufica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Sufica","vsebina"=>"<div class=\"modul-sufica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
