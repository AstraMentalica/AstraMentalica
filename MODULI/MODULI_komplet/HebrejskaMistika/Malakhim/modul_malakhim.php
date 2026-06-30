<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_malakhim_funkcije.php";
require_once __DIR__ . "/modul_malakhim_pravila.php";
require_once __DIR__ . "/modul_malakhim_jsonbaza.php";
class ModulMalakhim {
    private array $config;
    public function __construct() { $db = new ModulMalakhimJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Malakhim","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Malakhim","vsebina"=>"<div class=\"modul-malakhim\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
