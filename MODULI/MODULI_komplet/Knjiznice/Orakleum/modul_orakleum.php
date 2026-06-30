<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_orakleum_funkcije.php";
require_once __DIR__ . "/modul_orakleum_pravila.php";
require_once __DIR__ . "/modul_orakleum_jsonbaza.php";
class ModulOrakleum {
    private array $config;
    public function __construct() { $db = new ModulOrakleumJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Orakleum","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Orakleum","vsebina"=>"<div class=\"modul-orakleum\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
