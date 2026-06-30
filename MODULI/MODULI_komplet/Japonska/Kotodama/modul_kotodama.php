<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_kotodama_funkcije.php";
require_once __DIR__ . "/modul_kotodama_pravila.php";
require_once __DIR__ . "/modul_kotodama_jsonbaza.php";
class ModulKotodama {
    private array $config;
    public function __construct() { $db = new ModulKotodamaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Kotodama","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Kotodama","vsebina"=>"<div class=\"modul-kotodama\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
