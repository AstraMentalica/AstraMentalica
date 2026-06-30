<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_nadabrahma_funkcije.php";
require_once __DIR__ . "/modul_nadabrahma_pravila.php";
require_once __DIR__ . "/modul_nadabrahma_jsonbaza.php";
class ModulNadaBrahma {
    private array $config;
    public function __construct() { $db = new ModulNadaBrahmaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"NadaBrahma","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"NadaBrahma","vsebina"=>"<div class=\"modul-nadabrahma\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
